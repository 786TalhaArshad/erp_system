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

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $items = getRows("SELECT material_id, quantity FROM general_issuance_items WHERE issuance_id = ?", 'i', [$id]);
    if (!empty($items)) {
        foreach ($items as $item) {
            modifyData("UPDATE raw_materials SET current_stock = current_stock + ? WHERE id = ?", 'di', [$item['quantity'], $item['material_id']]);
        }
    }
    modifyData("DELETE FROM general_issuance_items WHERE issuance_id = ?", 'i', [$id]);
    $result = modifyData("DELETE FROM general_issuances WHERE id = ?", 'i', [$id]);
    if ($result !== false) {
        setFlash('Issuance deleted! Stock restored.', 'success');
    } else {
        setFlash('Error deleting issuance!', 'danger');
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$issues = getRows("SELECT gi.*,
        (SELECT COUNT(*) FROM general_issuance_items WHERE issuance_id = gi.id) as item_count
        FROM general_issuances gi
        ORDER BY gi.id DESC");

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
        <a href="add_general_issuance.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New General Issuance</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2"></i>General Issuances</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="issuanceTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Issuance No</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total Qty</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($issues)): ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($issues as $issue): ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($issue['issuance_no']); ?></strong></td>
                                        <td><?php echo date('d-m-Y', strtotime($issue['issuance_date'])); ?></td>
                                        <td><?php echo $issue['item_count']; ?></td>
                                        <td><?php echo number_format($issue['total_quantity'], 2); ?></td>
                                        <td><span class="badge bg-success"><?php echo ucfirst($issue['status']); ?></span></td>
                                        <td><?php echo htmlspecialchars($issue['notes'] ?: '-'); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="add_general_issuance.php?edit=<?php echo $issue['id']; ?>" class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?delete=<?php echo $issue['id']; ?>" class="btn btn-danger btn-sm delete-confirm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox me-2"></i>No general issuances found
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
    $('#issuanceTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
        "language": {
            "search": "Search Issuances:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ issuances",
            "emptyTable": "No general issuances found"
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
