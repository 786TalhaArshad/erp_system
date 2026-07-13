<?php
/**
 * Finished Goods Management
 * Manufacturing ERP System
 */

// Include database connection
require_once '../includes/database.php';

// Require login
requireLogin();

$pageTitle = 'Finished Goods';
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
    $sql = "DELETE FROM finished_goods WHERE id = ?";
    $result = modifyData($sql, 'i', [$id]);
    
    if ($result !== false) {
        setFlash('Product deleted successfully!', 'success');
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        setFlash('Error deleting product!', 'danger');
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $product_code = trim($_POST['product_code']);
    $product_name = trim($_POST['product_name']);
    $category = trim($_POST['category']);
    $unit = trim($_POST['unit']);
    $current_stock = (float)$_POST['current_stock'];
    $minimum_stock = (float)$_POST['minimum_stock'];
    $selling_price = (float)$_POST['selling_price'];
    $cost_price = (float)$_POST['cost_price'];
    $status = $_POST['status'];
    
    // Validation
    if (empty($product_code)) {
        $message = 'Product code is required!';
        $messageType = 'danger';
    } elseif (empty($product_name)) {
        $message = 'Product name is required!';
        $messageType = 'danger';
    } elseif (empty($unit)) {
        $message = 'Unit is required!';
        $messageType = 'danger';
    } else {
        $currentDateTime = getCurrentDateTime();
        
        // Check if product code exists
        $checkSql = "SELECT id FROM finished_goods WHERE product_code = ? AND id != ?";
        $check = getRow($checkSql, 'si', [$product_code, $id]);
        
        if ($check) {
            $message = 'Product code already exists!';
            $messageType = 'danger';
        } else {
            if ($id > 0) {
                // Update
                $sql = "UPDATE finished_goods SET 
                        product_code = ?, 
                        product_name = ?, 
                        category = ?, 
                        unit = ?, 
                        current_stock = ?,
                        minimum_stock = ?,
                        selling_price = ?,
                        cost_price = ?,
                        status = ?,
                        date_time = ?
                        WHERE id = ?";
                
                $params = [
                    $product_code, 
                    $product_name, 
                    $category, 
                    $unit, 
                    $current_stock,
                    $minimum_stock,
                    $selling_price,
                    $cost_price,
                    $status,
                    $currentDateTime,
                    $id
                ];
                
                $result = modifyData($sql, 'ssssdddddsi', $params);
                
                if ($result !== false) {
                    setFlash('Product updated successfully!', 'success');
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $message = 'Error updating product!';
                    $messageType = 'danger';
                }
            } else {
                // Insert
                $sql = "INSERT INTO finished_goods (
                        product_code, product_name, category, unit, current_stock,
                        minimum_stock, selling_price, cost_price, status, date_time
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $params = [
                    $product_code, 
                    $product_name, 
                    $category, 
                    $unit, 
                    $current_stock,
                    $minimum_stock,
                    $selling_price,
                    $cost_price,
                    $status,
                    $currentDateTime
                ];
                
                $result = insertData($sql, 'ssssddddds', $params);
                
                if ($result !== false) {
                    setFlash('Product added successfully!', 'success');
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $message = 'Error adding product!';
                    $messageType = 'danger';
                }
            }
        }
    }
}

// Get all finished goods
$sql = "SELECT * FROM finished_goods ORDER BY id DESC";
$products = getRows($sql);

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
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class="fas fa-plus me-2"></i>Add New Product
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2"></i>Finished Goods List</span>
                <a href="../index.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="productTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Stock</th>
                                <th>Min Stock</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th>Profit Margin</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($products) > 0): ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($product['product_code']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                                        <td><?php echo htmlspecialchars($product['unit']); ?></td>
                                        <td><?php echo number_format($product['current_stock'], 2); ?></td>
                                        <td><?php echo number_format($product['minimum_stock'], 2); ?></td>
                                        <td><?php echo formatCurrency($product['selling_price']); ?></td>
                                        <td><?php echo formatCurrency($product['cost_price']); ?></td>
                                        <td>
                                            <?php 
                                            $margin = 0;
                                            if ($product['cost_price'] > 0) {
                                                $margin = (($product['selling_price'] - $product['cost_price']) / $product['cost_price']) * 100;
                                            }
                                            echo number_format($margin, 2) . '%';
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-status badge-<?php echo $product['status']; ?>">
                                                <?php echo ucfirst($product['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                               data-id="<?php echo $product['id']; ?>"
                                               data-code="<?php echo htmlspecialchars($product['product_code']); ?>"
                                               data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                               data-category="<?php echo htmlspecialchars($product['category']); ?>"
                                               data-unit="<?php echo htmlspecialchars($product['unit']); ?>"
                                               data-stock="<?php echo $product['current_stock']; ?>"
                                               data-min="<?php echo $product['minimum_stock']; ?>"
                                               data-price="<?php echo $product['selling_price']; ?>"
                                               data-cost="<?php echo $product['cost_price']; ?>"
                                               data-status="<?php echo $product['status']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger delete-confirm">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle me-2"></i>No finished goods found
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

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">
                    <i class="fas fa-box me-2"></i>
                    <span id="modalTitle">Add New Product</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" onsubmit="return validateProductForm()">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id" value="0">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_code" class="form-label">Product Code *</label>
                            <input type="text" class="form-control" id="product_code" name="product_code" 
                                   value="<?php echo generateCode('FG'); ?>" 
                                   required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="product_name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" 
                                   value="" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category" name="category" value="">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">Unit *</label>
                            <input type="text" class="form-control" id="unit" name="unit" value="" required>
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
                            <label for="selling_price" class="form-label">Selling Price (PKR)</label>
                            <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" value="0.00">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="cost_price" class="form-label">Cost Price (PKR)</label>
                            <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" value="0.00">
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i><span id="submitBtnText">Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var code = $(this).data('code');
        var name = $(this).data('name');
        var category = $(this).data('category');
        var unit = $(this).data('unit');
        var stock = $(this).data('stock');
        var min = $(this).data('min');
        var price = $(this).data('price');
        var cost = $(this).data('cost');
        var status = $(this).data('status');

        $('#modalTitle').text('Edit Product');
        $('#submitBtnText').text('Update');
        $('#edit_id').val(id);
        $('#product_code').val(code);
        $('#product_name').val(name);
        $('#category').val(category);
        $('#unit').val(unit);
        $('#current_stock').val(stock);
        $('#minimum_stock').val(min);
        $('#selling_price').val(price);
        $('#cost_price').val(cost);
        $('#status').val(status);

        $('#productModal').modal('show');
    });

    $('#productModal').on('hidden.bs.modal', function() {
        $('#modalTitle').text('Add New Product');
        $('#submitBtnText').text('Save');
        $('#edit_id').val(0);
        $('#product_name').val('');
        $('#category').val('');
        $('#unit').val('');
        $('#current_stock').val('0.00');
        $('#minimum_stock').val('0.00');
        $('#selling_price').val('0.00');
        $('#cost_price').val('0.00');
        $('#status').val('active');
    });
});

function validateProductForm() {
    var code = document.getElementById('product_code').value.trim();
    var name = document.getElementById('product_name').value.trim();
    var unit = document.getElementById('unit').value.trim();

    if (code === '') {
        alert('Please enter product code.');
        document.getElementById('product_code').focus();
        return false;
    }

    if (name === '') {
        alert('Please enter product name.');
        document.getElementById('product_name').focus();
        return false;
    }

    if (unit === '') {
        alert('Please enter unit.');
        document.getElementById('unit').focus();
        return false;
    }

    return true;
}
 </script>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#productTable').DataTable({
        "pageLength": 25,
"language": {
    "search": "Search Products:",
    "lengthMenu": "Show _MENU_ entries",
    "info": "Showing _START_ to _END_ of _TOTAL_ products",
    "emptyTable": "No finished goods found"
}
    });
});
</script>

<?php
include '../includes/footer.php';
?>