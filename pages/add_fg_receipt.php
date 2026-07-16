<?php
require_once '../includes/database.php';
requireLogin();

$pageTitle = 'Receive Finished Goods';
$message = '';
$messageType = '';

$flash = getFlash();
if ($flash) {
    $message = $flash['message'];
    $messageType = $flash['type'];
}

$finishedGoods = getRows("SELECT id, product_code, product_name, unit, current_stock
                           FROM finished_goods WHERE status = 'active' ORDER BY product_name");

$editReceipt = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editReceipt = getRow("SELECT * FROM fg_receipts WHERE id = ?", 'i', [$editId]);
    if ($editReceipt) {
        $pageTitle = 'Edit FG Receipt';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $receipt_date = trim($_POST['receipt_date'] ?? '');
    $finished_good_id = (int)($_POST['finished_good_id'] ?? 0);
    $quantity = (float)($_POST['quantity'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    if (empty($receipt_date)) {
        $message = 'Please select receipt date!';
        $messageType = 'danger';
    } elseif ($finished_good_id <= 0) {
        $message = 'Please select a finished good!';
        $messageType = 'danger';
    } elseif ($quantity <= 0) {
        $message = 'Quantity must be greater than 0!';
        $messageType = 'danger';
    } else {
        $currentDateTime = getCurrentDateTime();

        if ($editId > 0) {
            $oldReceipt = getRow("SELECT finished_good_id, quantity FROM fg_receipts WHERE id = ?", 'i', [$editId]);
            if ($oldReceipt) {
                modifyData("UPDATE finished_goods SET current_stock = current_stock - ? WHERE id = ?",
                    'di', [$oldReceipt['quantity'], $oldReceipt['finished_good_id']]);
            }

            modifyData(
                "UPDATE fg_receipts SET receipt_date = ?, finished_good_id = ?, quantity = ?, notes = ?, date_time = ? WHERE id = ?",
                'sidssi',
                [$receipt_date, $finished_good_id, $quantity, $notes, $currentDateTime, $editId]
            );

            modifyData("UPDATE finished_goods SET current_stock = current_stock + ? WHERE id = ?",
                'di', [$quantity, $finished_good_id]);

            setFlash('FG receipt updated! Stock adjusted.', 'success');
        } else {
            $receiptNo = generateCode('FGR');
            $receiptId = insertData(
                "INSERT INTO fg_receipts (receipt_no, receipt_date, finished_good_id, quantity, notes, date_time) VALUES (?, ?, ?, ?, ?, ?)",
                'sssdss',
                [$receiptNo, $receipt_date, $finished_good_id, $quantity, $notes, $currentDateTime]
            );

            if ($receiptId !== false) {
                modifyData("UPDATE finished_goods SET current_stock = current_stock + ? WHERE id = ?",
                    'di', [$quantity, $finished_good_id]);

                setFlash('Finished goods received! Stock updated.', 'success');
            } else {
                $message = 'Error saving receipt!';
                $messageType = 'danger';
            }
        }

        if (empty($message)) {
            header('Location: view_fg_receipt.php');
            exit;
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
        <a href="view_fg_receipt.php" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i>Back to FG Receipts</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background:#1a2332;color:#fff;">
                <i class="fas fa-box-open me-2"></i><?php echo $editReceipt ? 'Edit FG Receipt' : 'Receive Finished Goods'; ?>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($editReceipt): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editReceipt['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Receipt Date *</label>
                            <input type="date" class="form-control" name="receipt_date"
                                   value="<?php echo $editReceipt ? $editReceipt['receipt_date'] : date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Finished Good *</label>
                            <select class="form-select" name="finished_good_id" id="fgSelect" required>
                                <option value="">Select Finished Good</option>
                                <?php foreach ($finishedGoods as $fg): ?>
                                    <option value="<?php echo $fg['id']; ?>"
                                            data-stock="<?php echo $fg['current_stock']; ?>"
                                            data-unit="<?php echo htmlspecialchars($fg['unit']); ?>"
                                        <?php echo ($editReceipt && $editReceipt['finished_good_id'] == $fg['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($fg['product_code'] . ' - ' . $fg['product_name']); ?>
                                        (<?php echo $fg['current_stock']; ?> <?php echo htmlspecialchars($fg['unit']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control" id="currentStock" readonly value="-">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantity to Add *</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" name="quantity"
                                   value="<?php echo $editReceipt ? $editReceipt['quantity'] : ''; ?>"
                                   required placeholder="Enter quantity">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Notes</label>
                            <input type="text" class="form-control" name="notes" placeholder="Optional notes..."
                                   value="<?php echo $editReceipt ? htmlspecialchars($editReceipt['notes']) : ''; ?>">
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i><?php echo $editReceipt ? 'Update Receipt' : 'Save Receipt'; ?>
                        </button>
                        <a href="view_fg_receipt.php" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var fgSelect = document.getElementById('fgSelect');
var currentStock = document.getElementById('currentStock');

function updateStock() {
    var opt = fgSelect.options[fgSelect.selectedIndex];
    if (opt && opt.dataset.stock !== undefined) {
        currentStock.value = opt.dataset.stock + ' ' + (opt.dataset.unit || '');
    } else {
        currentStock.value = '-';
    }
}

fgSelect.addEventListener('change', updateStock);
updateStock();
</script>

<?php include '../includes/footer.php'; ?>
