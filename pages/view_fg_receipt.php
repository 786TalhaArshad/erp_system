<?php
require_once '../includes/database.php';
requireLogin();

$pageTitle = 'FG Receipts';
$message = '';
$messageType = '';

$flash = getFlash();
if ($flash) {
    $message = $flash['message'];
    $messageType = $flash['type'];
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $receipt = getRow("SELECT finished_good_id, quantity FROM fg_receipts WHERE id = ?", 'i', [$id]);
    if ($receipt) {
        modifyData("UPDATE finished_goods SET current_stock = current_stock - ? WHERE id = ?",
            'di', [$receipt['quantity'], $receipt['finished_good_id']]);
        modifyData("DELETE FROM fg_receipts WHERE id = ?", 'i', [$id]);
        setFlash('Receipt deleted! Stock restored.', 'success');
    } else {
        setFlash('Receipt not found!', 'danger');
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$receipts = getRows("SELECT fr.*, fg.product_code, fg.product_name, fg.unit
                     FROM fg_receipts fr
                     JOIN finished_goods fg ON fr.finished_good_id = fg.id
                     ORDER BY fr.id DESC");

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
        <a href="add_fg_receipt.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Receive FG Stock</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2"></i>FG Receipts</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="receiptTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Receipt No</th>
                                <th>Date</th>
                                <th>Finished Good</th>
                                <th>Qty Added</th>
                                <th>Unit</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($receipts)): ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($receipts as $r): ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($r['receipt_no']); ?></strong></td>
                                        <td><?php echo date('d-m-Y', strtotime($r['receipt_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($r['product_code'] . ' - ' . $r['product_name']); ?></td>
                                        <td><?php echo number_format($r['quantity'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($r['unit']); ?></td>
                                        <td><?php echo htmlspecialchars($r['notes'] ?: '-'); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="add_fg_receipt.php?edit=<?php echo $r['id']; ?>" class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm delete-confirm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox me-2"></i>No FG receipts found
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

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#receiptTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
        "language": {
            "search": "Search Receipts:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ receipts",
            "emptyTable": "No FG receipts found"
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
