<?php
/**
 * Raw Materials Management
 * Manufacturing ERP System
 */

// Include database connection
require_once '../includes/database.php';

// Require login
requireLogin();

$pageTitle = 'Raw Materials';
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
    $sql = "DELETE FROM raw_materials WHERE id = ?";
    $result = modifyData($sql, 'i', [$id]);
    
    if ($result !== false) {
        setFlash('Raw material deleted successfully!', 'success');
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        setFlash('Error deleting raw material!', 'danger');
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $material_code = trim($_POST['material_code']);
    $material_name = trim($_POST['material_name']);
    $category = trim($_POST['category']);
    $unit = trim($_POST['unit']);
    $current_stock = (float)$_POST['current_stock'];
    $minimum_stock = (float)$_POST['minimum_stock'];
    $purchase_price_pkr = (float)$_POST['purchase_price_pkr'];
    $purchase_price_cny = (float)$_POST['purchase_price_cny'];
    $selling_price = (float)$_POST['selling_price'];
    $supplier_type = $_POST['supplier_type'];
    $supplier_id = isset($_POST['supplier_id']) && $_POST['supplier_id'] ? (int)$_POST['supplier_id'] : null;
    $status = $_POST['status'];
    
    // Validation
    if (empty($material_code)) {
        $message = 'Material code is required!';
        $messageType = 'danger';
    } elseif (empty($material_name)) {
        $message = 'Material name is required!';
        $messageType = 'danger';
    } elseif (empty($unit)) {
        $message = 'Unit is required!';
        $messageType = 'danger';
    } else {
        $currentDateTime = getCurrentDateTime();
        
        // Check if material code exists
        $checkSql = "SELECT id FROM raw_materials WHERE material_code = ? AND id != ?";
        $check = getRow($checkSql, 'si', [$material_code, $id]);
        
        if ($check) {
            $message = 'Material code already exists!';
            $messageType = 'danger';
        } else {
            if ($id > 0) {
                // Update
                $sql = "UPDATE raw_materials SET 
                        material_code = ?, 
                        material_name = ?, 
                        category = ?, 
                        unit = ?, 
                        current_stock = ?,
                        minimum_stock = ?,
                        purchase_price_pkr = ?,
                        purchase_price_cny = ?,
                        selling_price = ?,
                        supplier_type = ?,
                        supplier_id = ?,
                        status = ?,
                        date_time = ?
                        WHERE id = ?";
                
                $params = [
                    $material_code, 
                    $material_name, 
                    $category, 
                    $unit, 
                    $current_stock,
                    $minimum_stock,
                    $purchase_price_pkr,
                    $purchase_price_cny,
                    $selling_price,
                    $supplier_type,
                    $supplier_id,
                    $status,
                    $currentDateTime,
                    $id
                ];
                
                $result = modifyData($sql, 'ssssdddddssssi', $params);
                
                if ($result !== false) {
                    setFlash('Raw material updated successfully!', 'success');
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $message = 'Error updating raw material!';
                    $messageType = 'danger';
                }
            } else {
                // Insert
                $sql = "INSERT INTO raw_materials (
                        material_code, material_name, category, unit, current_stock,
                        minimum_stock, purchase_price_pkr, purchase_price_cny, selling_price, supplier_type,
                        supplier_id, status, date_time
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $params = [
                    $material_code, 
                    $material_name, 
                    $category, 
                    $unit, 
                    $current_stock,
                    $minimum_stock,
                    $purchase_price_pkr,
                    $purchase_price_cny,
                    $selling_price,
                    $supplier_type,
                    $supplier_id,
                    $status,
                    $currentDateTime
                ];
                
                $result = insertData($sql, 'ssssdddddssss', $params);
                
                if ($result !== false) {
                    setFlash('Raw material added successfully!', 'success');
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $message = 'Error adding raw material!';
                    $messageType = 'danger';
                }
            }
        }
    }
}

// Get all raw materials
$sql = "SELECT r.*, 
        CASE 
            WHEN r.supplier_type = 'chinese' THEN (SELECT supplier_name FROM chinese_suppliers WHERE id = r.supplier_id)
            WHEN r.supplier_type = 'local' THEN (SELECT supplier_name FROM local_suppliers WHERE id = r.supplier_id)
            ELSE NULL
        END as supplier_name
        FROM raw_materials r 
        ORDER BY r.id DESC";
$materials = getRows($sql);

// Get suppliers for dropdown
$chineseSuppliers = getRows("SELECT id, supplier_name FROM chinese_suppliers WHERE status = 'active' ORDER BY supplier_name");
$localSuppliers = getRows("SELECT id, supplier_name FROM local_suppliers WHERE status = 'active' ORDER BY supplier_name");

// Include header
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<!-- Page Content -->
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
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#materialModal" onclick="resetForm()">
            <i class="fas fa-plus me-2"></i>Add New Material
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2"></i>Raw Materials List</span>
                <a href="../index.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="materialTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Material Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Stock</th>
                                <th>Min Stock</th>
                                <th>Price (PKR)</th>
                                <th>Price (CNY)</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($materials) > 0): ?>
                                <?php foreach ($materials as $material): ?>
                                    <tr>
                                        <td><?php echo $material['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($material['material_code']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($material['material_name']); ?></td>
                                        <td><?php echo htmlspecialchars($material['category']); ?></td>
                                        <td><?php echo htmlspecialchars($material['unit']); ?></td>
                                        <td><?php echo number_format($material['current_stock'], 2); ?></td>
                                        <td><?php echo number_format($material['minimum_stock'], 2); ?></td>
                                        <td><?php echo formatCurrency($material['purchase_price_pkr']); ?></td>
                                        <td>
                                            <?php if ($material['supplier_type'] == 'chinese'): ?>
                                                <span class="badge bg-info"><?php echo number_format($material['purchase_price_cny'] ?? 0, 2); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $material['supplier_type'] == 'chinese' ? 'info' : 'success'; ?>">
                                                <?php echo ucfirst($material['supplier_type']); ?>
                                            </span>
                                            <?php if ($material['supplier_name']): ?>
                                                <br><small><?php echo htmlspecialchars($material['supplier_name']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-status badge-<?php echo $material['status']; ?>">
                                                <?php echo ucfirst($material['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#materialModal"
                                               data-id="<?php echo $material['id']; ?>"
                                               data-code="<?php echo htmlspecialchars($material['material_code']); ?>"
                                               data-name="<?php echo htmlspecialchars($material['material_name']); ?>"
                                               data-category="<?php echo htmlspecialchars($material['category']); ?>"
                                               data-unit="<?php echo htmlspecialchars($material['unit']); ?>"
                                               data-stock="<?php echo $material['current_stock']; ?>"
                                               data-minstock="<?php echo $material['minimum_stock']; ?>"
                                               data-pricepkr="<?php echo $material['purchase_price_pkr']; ?>"
                                               data-pricecny="<?php echo $material['purchase_price_cny'] ?? 0; ?>"
                                               data-selling="<?php echo $material['selling_price']; ?>"
                                               data-suppliertype="<?php echo $material['supplier_type']; ?>"
                                               data-supplierid="<?php echo $material['supplier_id'] ?? ''; ?>"
                                               data-status="<?php echo $material['status']; ?>"
                                               onclick="populateEditForm(this)">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo $material['id']; ?>" class="btn btn-sm btn-danger delete-confirm">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle me-2"></i>No raw materials found
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

<!-- Material Modal -->
<div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materialModalLabel">
                    <i class="fas fa-cube me-2"></i>Add New Raw Material
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" onsubmit="return validateMaterialForm()">
                <div class="modal-body">
                    <input type="hidden" name="id" id="material_id" value="0">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="material_code" class="form-label">Material Code *</label>
                            <input type="text" class="form-control" id="material_code" name="material_code" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="material_name" class="form-label">Material Name *</label>
                            <input type="text" class="form-control" id="material_name" name="material_name" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category" name="category">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">Unit *</label>
                            <input type="text" class="form-control" id="unit" name="unit" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="current_stock" class="form-label">Current Stock</label>
                            <input type="number" step="0.01" class="form-control" id="current_stock" name="current_stock" value="0.00">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="minimum_stock" class="form-label">Minimum Stock</label>
                            <input type="number" step="0.01" class="form-control" id="minimum_stock" name="minimum_stock" value="0.00">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="purchase_price_pkr" class="form-label">Purchase Price (PKR)</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_price_pkr" name="purchase_price_pkr" value="0.00">
                            <small class="text-muted">For local suppliers</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="purchase_price_cny" class="form-label">Purchase Price (CNY)</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_price_cny" name="purchase_price_cny" value="0.00">
                            <small class="text-muted">For Chinese suppliers</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="selling_price" class="form-label">Selling Price (PKR)</label>
                            <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" value="0.00">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="supplier_type" class="form-label">Supplier Type</label>
                            <select class="form-select" id="supplier_type" name="supplier_type" onchange="toggleSupplier()">
                                <option value="local">Local</option>
                                <option value="chinese">Chinese</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3" id="supplierDiv">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select class="form-select" id="supplier_id" name="supplier_id">
                                <option value="">Select Supplier</option>
                                <?php if ($editMaterial && $editMaterial['supplier_type'] == 'chinese'): ?>
                                    <?php foreach ($chineseSuppliers as $supplier): ?>
                                        <option value="<?php echo $supplier['id']; ?>" 
                                            <?php echo ($editMaterial && $editMaterial['supplier_id'] == $supplier['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php foreach ($localSuppliers as $supplier): ?>
                                        <option value="<?php echo $supplier['id']; ?>" 
                                            <?php echo ($editMaterial && $editMaterial['supplier_id'] == $supplier['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">
                        <i class="fas fa-save me-2"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var chineseSuppliers = <?php echo json_encode($chineseSuppliers); ?>;
var localSuppliers = <?php echo json_encode($localSuppliers); ?>;

function toggleSupplier() {
    var type = document.getElementById('supplier_type').value;
    var sel = document.getElementById('supplier_id');
    sel.innerHTML = '<option value="">Select Supplier</option>';
    var list = type === 'chinese' ? chineseSuppliers : localSuppliers;
    list.forEach(function(s) {
        sel.innerHTML += '<option value="' + s.id + '">' + s.supplier_name + '</option>';
    });
}

function resetForm() {
    document.getElementById('material_id').value = 0;
    document.getElementById('material_code').value = '<?php echo generateCode('RM'); ?>';
    document.getElementById('material_name').value = '';
    document.getElementById('category').value = '';
    document.getElementById('unit').value = '';
    document.getElementById('current_stock').value = '0.00';
    document.getElementById('minimum_stock').value = '0.00';
    document.getElementById('purchase_price_pkr').value = '0.00';
    document.getElementById('purchase_price_cny').value = '0.00';
    document.getElementById('selling_price').value = '0.00';
    document.getElementById('supplier_type').value = 'local';
    document.getElementById('status').value = 'active';
    document.getElementById('materialModalLabel').innerHTML = '<i class="fas fa-cube me-2"></i>Add New Raw Material';
    document.getElementById('formSubmitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Save';
    toggleSupplier();
}

function populateEditForm(btn) {
    var d = btn.dataset;
    document.getElementById('material_id').value = d.id;
    document.getElementById('material_code').value = d.code;
    document.getElementById('material_name').value = d.name;
    document.getElementById('category').value = d.category || '';
    document.getElementById('unit').value = d.unit;
    document.getElementById('current_stock').value = d.stock;
    document.getElementById('minimum_stock').value = d.minstock;
    document.getElementById('purchase_price_pkr').value = d.pricepkr;
    document.getElementById('purchase_price_cny').value = d.pricecny;
    document.getElementById('selling_price').value = d.selling;
    document.getElementById('supplier_type').value = d.suppliertype;
    document.getElementById('status').value = d.status;
    document.getElementById('materialModalLabel').innerHTML = '<i class="fas fa-cube me-2"></i>Edit Raw Material';
    document.getElementById('formSubmitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Update';
    toggleSupplier();
    document.getElementById('supplier_id').value = d.supplierid || '';
}

function validateMaterialForm() {
    var code = document.getElementById('material_code').value.trim();
    var name = document.getElementById('material_name').value.trim();
    var unit = document.getElementById('unit').value.trim();
    if (code === '') { alert('Please enter material code.'); document.getElementById('material_code').focus(); return false; }
    if (name === '') { alert('Please enter material name.'); document.getElementById('material_name').focus(); return false; }
    if (unit === '') { alert('Please enter unit.'); document.getElementById('unit').focus(); return false; }
    return true;
}
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#materialTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
"language": {
    "search": "Search Materials:",
    "lengthMenu": "Show _MENU_ entries",
    "info": "Showing _START_ to _END_ of _TOTAL_ materials",
    "emptyTable": "No raw materials found"
}
    });
});
</script>

<?php
include '../includes/footer.php';
?>