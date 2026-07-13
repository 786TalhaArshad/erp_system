<?php
/**
 * Material Issue to Production
 * Manufacturing ERP System
 */

require_once '../includes/database.php';
requireLogin();

$pageTitle = 'Issue Material';
$message = '';
$messageType = '';

$flash = getFlash();
if ($flash) {
    $message = $flash['message'];
    $messageType = $flash['type'];
}

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $sql = "SELECT material_id, quantity FROM material_issue_items WHERE issue_id = ?";
    $items = getRows($sql, 'i', [$id]);
    
    $sql = "DELETE FROM material_issues WHERE id = ?";
    $result = modifyData($sql, 'i', [$id]);
    
    if ($result !== false) {
        $sql = "DELETE FROM material_issue_items WHERE issue_id = ?";
        modifyData($sql, 'i', [$id]);
        
        foreach ($items as $item) {
            $sql = "UPDATE raw_materials SET current_stock = current_stock + ? WHERE id = ?";
            modifyData($sql, 'di', [$item['quantity'], $item['material_id']]);
        }
        
        setFlash('Material issue deleted successfully! Stock restored.', 'success');
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        setFlash('Error deleting material issue!', 'danger');
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $issue_date = $_POST['issue_date'];
    $production_order_id = isset($_POST['production_order_id']) && $_POST['production_order_id'] ? (int)$_POST['production_order_id'] : null;
    $notes = trim($_POST['notes']);
    $materials = isset($_POST['materials']) ? $_POST['materials'] : [];
    
    if (empty($issue_date)) {
        $message = 'Please select issue date!';
        $messageType = 'danger';
    } elseif (empty($materials)) {
        $message = 'Please add at least one material!';
        $messageType = 'danger';
    } else {
        $currentDateTime = getCurrentDateTime();
        $issue_no = generateCode('MI');
        $totalValue = 0;
        
        foreach ($materials as $material) {
            $qty = (float)$material['quantity'];
            $price = (float)$material['unit_price'];
            $totalValue += $qty * $price;
        }
        
        if ($id > 0) {
            $sql = "SELECT material_id, quantity FROM material_issue_items WHERE issue_id = ?";
            $oldItems = getRows($sql, 'i', [$id]);
            
            foreach ($oldItems as $oldItem) {
                $sql = "UPDATE raw_materials SET current_stock = current_stock + ? WHERE id = ?";
                modifyData($sql, 'di', [$oldItem['quantity'], $oldItem['material_id']]);
            }
            
            $sql = "UPDATE material_issues SET issue_date = ?, production_order_id = ?, notes = ?, total_value = ?, date_time = ? WHERE id = ?";
            modifyData($sql, 'sdsdsi', [$issue_date, $production_order_id, $notes, $totalValue, $currentDateTime, $id]);
            
            $sql = "DELETE FROM material_issue_items WHERE issue_id = ?";
            modifyData($sql, 'i', [$id]);
            
            foreach ($materials as $material) {
                $material_id = (int)$material['material_id'];
                $quantity = (float)$material['quantity'];
                $unit_price = (float)$material['unit_price'];
                $total = $quantity * $unit_price;
                
                $sql = "INSERT INTO material_issue_items (issue_id, material_id, quantity, unit_price, total, date_time) VALUES (?, ?, ?, ?, ?, ?)";
                insertData($sql, 'iiddds', [$id, $material_id, $quantity, $unit_price, $total, $currentDateTime]);
                
                $sql = "UPDATE raw_materials SET current_stock = current_stock - ? WHERE id = ?";
                modifyData($sql, 'di', [$quantity, $material_id]);
            }
            
            setFlash('Material issue updated successfully!', 'success');
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $sql = "INSERT INTO material_issues (issue_no, issue_date, production_order_id, notes, total_value, status, date_time) VALUES (?, ?, ?, ?, ?, 'issued', ?)";
            $result = insertData($sql, 'ssdsds', [$issue_no, $issue_date, $production_order_id, $notes, $totalValue, $currentDateTime]);
            
            if ($result !== false) {
                $issueId = $result;
                
                foreach ($materials as $material) {
                    $material_id = (int)$material['material_id'];
                    $quantity = (float)$material['quantity'];
                    $unit_price = (float)$material['unit_price'];
                    $total = $quantity * $unit_price;
                    
                    $sql = "INSERT INTO material_issue_items (issue_id, material_id, quantity, unit_price, total, date_time) VALUES (?, ?, ?, ?, ?, ?)";
                    insertData($sql, 'iiddds', [$issueId, $material_id, $quantity, $unit_price, $total, $currentDateTime]);
                    
                    $sql = "UPDATE raw_materials SET current_stock = current_stock - ? WHERE id = ?";
                    modifyData($sql, 'di', [$quantity, $material_id]);
                }
                
                setFlash('Material issued to production successfully!', 'success');
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $message = 'Error creating material issue!';
                $messageType = 'danger';
            }
        }
    }
}

// Get all issues
$sql = "SELECT mi.*, po.production_no 
        FROM material_issues mi 
        LEFT JOIN production_orders po ON mi.production_order_id = po.id 
        ORDER BY mi.id DESC";
$issues = getRows($sql);

// Get exchange rate
$rateRow = getRow("SELECT exchange_rate FROM currencies WHERE currency_code = 'CNY'");
$cnyRate = $rateRow ? (float)$rateRow['exchange_rate'] : 40;

// Get all raw materials
$materials = getRows("SELECT id, material_code, material_name, unit, purchase_price_pkr, purchase_price_cny, supplier_type, current_stock 
                      FROM raw_materials WHERE status = 'active' AND current_stock > 0 ORDER BY material_name");

// Compute effective PKR price for each material
foreach ($materials as &$m) {
    if ((float)$m['purchase_price_pkr'] > 0) {
        $m['pkr_price'] = (float)$m['purchase_price_pkr'];
    } else {
        $m['pkr_price'] = (float)$m['purchase_price_cny'] * $cnyRate;
    }
}
unset($m);

// Get production orders
$productionOrders = getRows("SELECT id, production_no, status FROM production_orders ORDER BY id DESC");

// Get single issue for edit
$editIssue = null;
$editItems = [];
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $sql = "SELECT * FROM material_issues WHERE id = ?";
    $editIssue = getRow($sql, 'i', [$editId]);
    
    if ($editIssue) {
        $sql = "SELECT * FROM material_issue_items WHERE issue_id = ?";
        $editItems = getRows($sql, 'i', [$editId]);
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="row">
    <div class="col-12">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issueModal">
            <i class="fas fa-plus me-2"></i>New Material Issue
        </button>
        <a href="raw_materials.php" class="btn btn-info text-white">
            <i class="fas fa-cubes me-2"></i>Raw Materials
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-sign-out-alt me-2"></i>Material Issues List</span>
                <a href="../index.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="issueTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Issue No</th>
                                <th>Date</th>
                                <th>Production Order</th>
                                <th>Total Value (PKR)<br><small class="text-muted">All currencies converted</small></th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($issues) > 0): ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($issues as $issue): ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($issue['issue_no']); ?></strong></td>
                                        <td><?php echo date('d-m-Y', strtotime($issue['issue_date'])); ?></td>
                                        <td><?php echo $issue['production_no'] ? htmlspecialchars($issue['production_no']) : '-'; ?></td>
                                        <td><?php echo formatCurrency($issue['total_value']); ?></td>
                                        <td>
                                            <span class="badge badge-status badge-<?php echo strtolower($issue['status']); ?>">
                                                <?php echo ucfirst($issue['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($issue['notes'] ?: '-'); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="?edit=<?php echo $issue['id']; ?>" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#issueModal" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?delete=<?php echo $issue['id']; ?>" class="btn btn-danger delete-confirm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle me-2"></i>No material issues found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Issue Modal -->
<div class="modal fade" id="issueModal" tabindex="-1" aria-labelledby="issueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="issueModalLabel">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <?php echo $editIssue ? 'Edit Material Issue' : 'New Material Issue'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" onsubmit="return validateIssueForm()">
                <div class="modal-body">
                    <?php if ($editIssue): ?>
                        <input type="hidden" name="id" value="<?php echo $editIssue['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="issue_date" class="form-label">Issue Date *</label>
                            <input type="date" class="form-control" id="issue_date" name="issue_date" 
                                   value="<?php echo $editIssue ? $editIssue['issue_date'] : date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="production_order_id" class="form-label">Production Order</label>
                            <select class="form-select" id="production_order_id" name="production_order_id">
                                <option value="">-- General Issue (No Order) --</option>
                                <?php foreach ($productionOrders as $po): ?>
                                    <option value="<?php echo $po['id']; ?>" 
                                        <?php echo ($editIssue && $editIssue['production_order_id'] == $po['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($po['production_no']); ?> (<?php echo ucfirst($po['status']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"><?php echo $editIssue ? htmlspecialchars($editIssue['notes']) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Materials Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="fas fa-cubes me-2"></i>Materials to Issue
                            <button type="button" class="btn btn-sm btn-success float-end" onclick="addMaterialRow()">
                                <i class="fas fa-plus me-1"></i>Add Material
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="materialTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Material</th>
                                            <th style="width: 15%;">Available Stock</th>
                                            <th style="width: 15%;">Quantity to Issue</th>
                                            <th style="width: 15%;">Unit Price (PKR)<br><small class="text-muted">CNY converted</small></th>
                                            <th style="width: 15%;">Total (PKR)</th>
                                            <th style="width: 10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="materialBody">
                                        <?php if ($editItems): ?>
                                            <?php foreach ($editItems as $item): ?>
                                                <tr>
                                                    <td>
                                                        <select class="form-select material-select" name="materials[][material_id]" required>
                                                            <option value="">Select Material</option>
                                                            <?php foreach ($materials as $material): ?>
                                                            <option value="<?php echo $material['id']; ?>" 
                                                                     <?php echo ($item['material_id'] == $material['id']) ? 'selected' : ''; ?>
                                                                     data-price="<?php echo $material['pkr_price']; ?>"
                                                                     data-pricecny="<?php echo $material['purchase_price_cny']; ?>"
                                                                     data-stock="<?php echo $material['current_stock']; ?>"
                                                                     data-type="<?php echo $material['supplier_type']; ?>">
                                                                     <?php echo $material['material_code'] . ' - ' . $material['material_name']; ?>
                                                                     <?php if ($material['supplier_type'] == 'chinese'): ?>
                                                                         (CNY <?php echo number_format($material['purchase_price_cny'], 2); ?>)
                                                                     <?php endif; ?>
                                                                 </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td class="text-center stock-display"><?php echo $item['quantity']; ?></td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control quantity" name="materials[][quantity]" 
                                                               value="<?php echo $item['quantity']; ?>" required onchange="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control unit-price" name="materials[][unit_price]" 
                                                               value="<?php echo $item['unit_price']; ?>" required onchange="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control row-total" value="<?php echo formatCurrency($item['total']); ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td>
                                                    <select class="form-select material-select" name="materials[][material_id]" required>
                                                        <option value="">Select Material</option>
                                                        <?php foreach ($materials as $material): ?>
                                                            <option value="<?php echo $material['id']; ?>" 
                                                                     data-price="<?php echo $material['pkr_price']; ?>"
                                                                     data-pricecny="<?php echo $material['purchase_price_cny']; ?>"
                                                                     data-stock="<?php echo $material['current_stock']; ?>"
                                                                     data-type="<?php echo $material['supplier_type']; ?>">
                                                                     <?php echo $material['material_code'] . ' - ' . $material['material_name']; ?>
                                                                     <?php if ($material['supplier_type'] == 'chinese'): ?>
                                                                         (CNY <?php echo number_format($material['purchase_price_cny'], 2); ?>)
                                                                     <?php endif; ?>
                                                                 </option>
                                                         <?php endforeach; ?>
                                                     </select>
                                                 </td>
                                                 <td class="text-center stock-display">0</td>
                                                 <td>
                                                     <input type="number" step="0.01" class="form-control quantity" name="materials[][quantity]" 
                                                            value="1" required onchange="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)">
                                                 </td>
                                                 <td>
                                                     <input type="number" step="0.01" class="form-control unit-price" name="materials[][unit_price]" 
                                                            value="0" required onchange="calculateRowTotal(this)" onkeyup="calculateRowTotal(this)">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control row-total" value="0.00" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-primary">
                                            <th colspan="4" class="text-end">Total Value</th>
                                            <th>
                                                <input type="text" class="form-control" id="grand_total" value="0.00" readonly>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle me-2"></i><?php echo $editIssue ? 'Update Issue' : 'Issue Materials'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addMaterialRow() {
    var tbody = document.getElementById('materialBody');
    var row = tbody.insertRow();
    
    var materials = <?php echo json_encode($materials); ?>;
    
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);
    var cell6 = row.insertCell(5);
    
    var select = document.createElement('select');
    select.className = 'form-select material-select';
    select.name = 'materials[][material_id]';
    select.required = true;
    
    var option = document.createElement('option');
    option.value = '';
    option.text = 'Select Material';
    select.appendChild(option);
    
    materials.forEach(function(material) {
        var opt = document.createElement('option');
        opt.value = material.id;
        opt.text = material.material_code + ' - ' + material.material_name + (material.supplier_type === 'chinese' ? ' (CNY ' + parseFloat(material.purchase_price_cny).toFixed(2) + ')' : '');
        opt.dataset.price = material.pkr_price || material.purchase_price_pkr;
        opt.dataset.stock = material.current_stock;
        select.appendChild(opt);
    });
    
    select.onchange = function() {
        var price = this.options[this.selectedIndex].dataset.price || 0;
        var stock = this.options[this.selectedIndex].dataset.stock || 0;
        var row = this.closest('tr');
        row.querySelector('.unit-price').value = price;
        row.querySelector('.stock-display').textContent = stock;
        calculateRowTotal(row.querySelector('.quantity'));
    };
    
    cell1.appendChild(select);
    
    var stockCell = document.createElement('td');
    stockCell.className = 'text-center stock-display';
    stockCell.textContent = '0';
    cell2.appendChild(stockCell);
    
    var qtyInput = document.createElement('input');
    qtyInput.type = 'number';
    qtyInput.step = '0.01';
    qtyInput.className = 'form-control quantity';
    qtyInput.name = 'materials[][quantity]';
    qtyInput.value = '1';
    qtyInput.required = true;
    qtyInput.onchange = function() { calculateRowTotal(this); };
    qtyInput.onkeyup = function() { calculateRowTotal(this); };
    cell3.appendChild(qtyInput);
    
    var priceInput = document.createElement('input');
    priceInput.type = 'number';
    priceInput.step = '0.01';
    priceInput.className = 'form-control unit-price';
    priceInput.name = 'materials[][unit_price]';
    priceInput.value = '0';
    priceInput.required = true;
    priceInput.onchange = function() { calculateRowTotal(this); };
    priceInput.onkeyup = function() { calculateRowTotal(this); };
    cell4.appendChild(priceInput);
    
    var totalInput = document.createElement('input');
    totalInput.type = 'text';
    totalInput.className = 'form-control row-total';
    totalInput.value = '0.00';
    totalInput.readOnly = true;
    cell5.appendChild(totalInput);
    
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn btn-danger btn-sm';
    btn.innerHTML = '<i class="fas fa-times"></i>';
    btn.onclick = function() { removeRow(this); };
    cell6.appendChild(btn);
}

function removeRow(btn) {
    var tbody = document.getElementById('materialBody');
    if (tbody.rows.length > 1) {
        var row = btn.closest('tr');
        tbody.deleteRow(row.rowIndex - 1);
        calculateGrandTotal();
    } else {
        alert('At least one material is required!');
    }
}

function calculateRowTotal(element) {
    var row = element.closest('tr');
    var qty = parseFloat(row.querySelector('.quantity').value) || 0;
    var price = parseFloat(row.querySelector('.unit-price').value) || 0;
    var total = qty * price;
    row.querySelector('.row-total').value = total.toFixed(2);
    calculateGrandTotal();
}

function calculateGrandTotal() {
    var rows = document.querySelectorAll('#materialBody tr');
    var grandTotal = 0;
    rows.forEach(function(row) {
        var total = parseFloat(row.querySelector('.row-total').value) || 0;
        grandTotal += total;
    });
    document.getElementById('grand_total').value = grandTotal.toFixed(2);
}

$(document).ready(function() {
    $(document).on('change', '.material-select', function() {
        var opt = $(this).find('option:selected');
        var price = opt.data('price');
        var stock = opt.data('stock');
        if (price) {
            $(this).closest('tr').find('.unit-price').val(price);
            $(this).closest('tr').find('.stock-display').text(stock);
            calculateRowTotal($(this).closest('tr').find('.quantity')[0]);
        }
    });
    
    <?php if (isset($_GET['edit']) && $editIssue): ?>
        $('#issueModal').modal('show');
        setTimeout(calculateGrandTotal, 500);
    <?php endif; ?>
});

function validateIssueForm() {
    var date = document.getElementById('issue_date').value;
    var rows = document.querySelectorAll('#materialBody tr');
    
    if (date === '') {
        alert('Please select issue date.');
        document.getElementById('issue_date').focus();
        return false;
    }
    
    if (rows.length === 0) {
        alert('Please add at least one material.');
        return false;
    }
    
    var valid = true;
    rows.forEach(function(row) {
        var material = row.querySelector('.material-select').value;
        var qty = parseFloat(row.querySelector('.quantity').value) || 0;
        var stock = parseFloat(row.querySelector('.stock-display').textContent) || 0;
        
        if (material === '') valid = false;
        if (qty <= 0) valid = false;
        if (qty > stock) {
            alert('Issue quantity cannot exceed available stock!');
            valid = false;
        }
    });
    
    if (!valid) {
        alert('Please ensure all materials have valid data and sufficient stock.');
        return false;
    }
    
    return true;
}
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#issueTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
"language": {
    "search": "Search Issues:",
    "lengthMenu": "Show _MENU_ entries",
    "info": "Showing _START_ to _END_ of _TOTAL_ issues",
    "emptyTable": "No material issues found"
}
    });
});
</script>

<?php
include '../includes/footer.php';
?>
