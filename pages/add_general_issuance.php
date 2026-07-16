<?php
require_once '../includes/database.php';
requireLogin();

$pageTitle = 'General Issuance';
$message = '';
$messageType = '';

$flash = getFlash();
if ($flash) {
    $message = $flash['message'];
    $messageType = $flash['type'];
}

$rawMaterials = getRows("SELECT id, material_code, material_name, unit, current_stock 
                         FROM raw_materials WHERE status = 'active' ORDER BY material_name");

$editIssue = null;
$editItems = [];
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editIssue = getRow("SELECT * FROM general_issuances WHERE id = ?", 'i', [$editId]);
    if ($editIssue) {
        $editItems = getRows("SELECT * FROM general_issuance_items WHERE issuance_id = ?", 'i', [$editId]);
        $pageTitle = 'Edit General Issuance';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $issue_date = trim($_POST['issue_date'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $mat_ids = $_POST['material_ids'] ?? [];
    $mat_qtys = $_POST['material_qtys'] ?? [];

    if (empty($issue_date)) {
        $message = 'Please select issuance date!';
        $messageType = 'danger';
    } elseif (empty($mat_ids)) {
        $message = 'Please add at least one material!';
        $messageType = 'danger';
    } else {
        $currentDateTime = getCurrentDateTime();
        $validMaterials = [];
        foreach ($mat_ids as $i => $mid) {
            $mid = (int)$mid;
            $qty = isset($mat_qtys[$i]) ? (float)$mat_qtys[$i] : 0;
            if ($mid > 0 && $qty > 0) {
                $validMaterials[] = ['material_id' => $mid, 'quantity' => $qty];
            }
        }

        if (empty($validMaterials)) {
            $message = 'Please select a material and enter a valid quantity!';
            $messageType = 'danger';
        } else {
            if ($editId > 0) {
                $oldItems = getRows("SELECT material_id, quantity FROM general_issuance_items WHERE issuance_id = ?", 'i', [$editId]);
                foreach ($oldItems as $old) {
                    modifyData("UPDATE raw_materials SET current_stock = current_stock + ? WHERE id = ?", 'di', [$old['quantity'], $old['material_id']]);
                }
            }

            $insufficientStock = false;
            foreach ($validMaterials as $vm) {
                $check = getRow("SELECT current_stock FROM raw_materials WHERE id = ?", 'i', [$vm['material_id']]);
                if (!$check || (float)$check['current_stock'] < $vm['quantity']) {
                    $insufficientStock = true;
                    break;
                }
            }

            if ($insufficientStock) {
                $message = 'Insufficient stock for one or more materials!';
                $messageType = 'danger';
            } else {
                $totalQty = 0;
                foreach ($validMaterials as $vm) {
                    $totalQty += $vm['quantity'];
                }

                if ($editId > 0) {
                    modifyData(
                        "UPDATE general_issuances SET issuance_date = ?, notes = ?, total_items = ?, total_quantity = ?, date_time = ? WHERE id = ?",
                        'ssddsi',
                        [$issue_date, $notes, count($validMaterials), $totalQty, $currentDateTime, $editId]
                    );
                    modifyData("DELETE FROM general_issuance_items WHERE issuance_id = ?", 'i', [$editId]);

                    foreach ($validMaterials as $vm) {
                        insertData(
                            "INSERT INTO general_issuance_items (issuance_id, material_id, quantity, date_time) VALUES (?, ?, ?, ?)",
                            'iiid',
                            [$editId, $vm['material_id'], $vm['quantity'], $currentDateTime]
                        );
                        modifyData(
                            "UPDATE raw_materials SET current_stock = current_stock - ? WHERE id = ?",
                            'di',
                            [$vm['quantity'], $vm['material_id']]
                        );
                    }

                    setFlash('Issuance updated successfully!', 'success');
                } else {
                    $issueNo = generateCode('GI');
                    $issueId = insertData(
                        "INSERT INTO general_issuances (issuance_no, issuance_date, notes, total_items, total_quantity, status, date_time) VALUES (?, ?, ?, ?, ?, 'issued', ?)",
                        'sssdds',
                        [$issueNo, $issue_date, $notes, count($validMaterials), $totalQty, $currentDateTime]
                    );

                    if ($issueId !== false) {
                        foreach ($validMaterials as $vm) {
                            insertData(
                                "INSERT INTO general_issuance_items (issuance_id, material_id, quantity, date_time) VALUES (?, ?, ?, ?)",
                                'iiid',
                                [$issueId, $vm['material_id'], $vm['quantity'], $currentDateTime]
                            );
                            modifyData(
                                "UPDATE raw_materials SET current_stock = current_stock - ? WHERE id = ?",
                                'di',
                                [$vm['quantity'], $vm['material_id']]
                            );
                        }
                        setFlash('General issuance created! Stock deducted.', 'success');
                    } else {
                        $message = 'Error creating issuance!';
                        $messageType = 'danger';
                    }
                }

                if (empty($message)) {
                    header('Location: view_general_issuance.php');
                    exit;
                }
            }
        }
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
        <a href="view_general_issuance.php" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i>Back to Issuances</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background:#1a2332;color:#fff;">
                <i class="fas fa-sign-out-alt me-2"></i><?php echo $editIssue ? 'Edit General Issuance' : 'New General Issuance'; ?>
            </div>
            <div class="card-body">
                <form method="POST" action="" onsubmit="return validateForm()">
                    <?php if ($editIssue): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editIssue['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Issuance Date *</label>
                            <input type="date" class="form-control" name="issue_date" 
                                   value="<?php echo $editIssue ? $editIssue['issuance_date'] : date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Notes</label>
                            <input type="text" class="form-control" name="notes" placeholder="Optional notes..."
                                   value="<?php echo $editIssue ? htmlspecialchars($editIssue['notes']) : ''; ?>">
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-cubes me-2"></i>Materials to Issue</span>
                            <button type="button" class="btn btn-sm btn-success" onclick="addRow()">
                                <i class="fas fa-plus me-1"></i>Add Material
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="materialTable">
                                    <thead>
                                        <tr>
                                            <th style="width:50%;">Raw Material</th>
                                            <th style="width:15%;">In Stock</th>
                                            <th style="width:25%;">Qty to Issue</th>
                                            <th style="width:10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="materialBody">
                                        <?php if (!empty($editItems)): ?>
                                            <?php foreach ($editItems as $item): ?>
                                                <tr>
                                                    <td>
                                                        <select class="form-select form-select-sm mat-select" name="material_ids[]" required onchange="onSelect(this)">
                                                            <option value="">Select Material</option>
                                                            <?php foreach ($rawMaterials as $m): ?>
                                                                <option value="<?php echo $m['id']; ?>" data-stock="<?php echo $m['current_stock']; ?>"
                                                                    <?php echo ($item['material_id'] == $m['id']) ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($m['material_code'] . ' - ' . $m['material_name'] . ' (' . $m['unit'] . ')'); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td class="text-center stock-val">-</td>
                                                    <td>
                                                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" 
                                                               name="material_qtys[]" value="<?php echo $item['quantity']; ?>" required placeholder="Qty">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm mat-select" name="material_ids[]" required onchange="onSelect(this)">
                                                        <option value="">Select Material</option>
                                                        <?php foreach ($rawMaterials as $m): ?>
                                                            <option value="<?php echo $m['id']; ?>" data-stock="<?php echo $m['current_stock']; ?>">
                                                                <?php echo htmlspecialchars($m['material_code'] . ' - ' . $m['material_name'] . ' (' . $m['unit'] . ')'); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td class="text-center stock-val">-</td>
                                                <td>
                                                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" 
                                                           name="material_qtys[]" value="" required placeholder="Qty">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i><?php echo $editIssue ? 'Update Issuance' : 'Create Issuance'; ?>
                        </button>
                        <a href="view_general_issuance.php" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var materials = <?php echo json_encode($rawMaterials); ?>;

function onSelect(el) {
    var row = el.closest('tr');
    var opt = el.options[el.selectedIndex];
    var stock = opt.dataset.stock || '-';
    row.querySelector('.stock-val').textContent = stock;
}

function updateStockDisplays() {
    document.querySelectorAll('.mat-select').forEach(function(sel) {
        var row = sel.closest('tr');
        var opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.stock !== undefined) {
            row.querySelector('.stock-val').textContent = opt.dataset.stock;
        }
    });
}

function addRow() {
    var tbody = document.getElementById('materialBody');
    var row = tbody.insertRow();
    var html = '<td><select class="form-select form-select-sm mat-select" name="material_ids[]" required onchange="onSelect(this)"><option value="">Select Material</option>';
    materials.forEach(function(m) {
        html += '<option value="' + m.id + '" data-stock="' + m.current_stock + '">' + m.material_code + ' - ' + m.material_name + ' (' + m.unit + ')</option>';
    });
    html += '</select></td>';
    html += '<td class="text-center stock-val">-</td>';
    html += '<td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm" name="material_qtys[]" value="" required placeholder="Qty"></td>';
    html += '<td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-times"></i></button></td>';
    row.innerHTML = html;
}

function removeRow(btn) {
    var tbody = document.getElementById('materialBody');
    if (tbody.rows.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('At least one material is required!');
    }
}

function validateForm() {
    var selects = document.querySelectorAll('.mat-select');
    var qtys = document.querySelectorAll('input[name="material_qtys[]"]');
    if (selects.length === 0) {
        alert('Please add at least one material.');
        return false;
    }
    for (var i = 0; i < selects.length; i++) {
        var sel = selects[i];
        var qty = parseFloat(qtys[i].value) || 0;
        var stock = parseFloat(sel.closest('tr').querySelector('.stock-val').textContent) || 0;
        if (!sel.value) {
            alert('Please select a material for every row.');
            sel.focus();
            return false;
        }
        if (qty <= 0) {
            alert('Quantity must be greater than 0.');
            qtys[i].focus();
            return false;
        }
        if (stock > 0 && qty > stock) {
            alert('Issue quantity (' + qty + ') exceeds available stock (' + stock + ')!');
            qtys[i].focus();
            return false;
        }
    }
    return true;
}

$(document).ready(function() {
    updateStockDisplays();
});
</script>

<?php include '../includes/footer.php'; ?>
