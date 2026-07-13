<?php
require_once '../includes/database.php';
requireLogin();

$pageTitle = 'Customer Receiving';
$message = '';
$messageType = '';

$flash = getFlash();
if ($flash) {
    $message = $flash['message'];
    $messageType = $flash['type'];
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $sale_id = (int)$_POST['sale_id'];
    $payment_amount = (float)$_POST['payment_amount'];
    $payment_method = trim($_POST['payment_method']);
    $reference = trim($_POST['reference']);
    $payment_date = $_POST['payment_date'];
    $notes = trim($_POST['notes']);

    if ($payment_amount <= 0) {
        $message = 'Payment amount must be greater than zero!';
        $messageType = 'danger';
    } else {
        $sale = getRow("SELECT * FROM sales WHERE id = ?", 'i', [$sale_id]);

        if (!$sale) {
            $message = 'Sale invoice not found!';
            $messageType = 'danger';
        } else {
            $newPaid = $sale['paid_amount'] + $payment_amount;
            $newBalance = $sale['total_amount'] - $newPaid;

            if ($newBalance < 0) {
                $message = 'Payment exceeds the outstanding balance!';
                $messageType = 'danger';
            } else {
                $paymentStatus = 'unpaid';
                if ($newPaid >= $sale['total_amount']) {
                    $paymentStatus = 'paid';
                } elseif ($newPaid > 0) {
                    $paymentStatus = 'partial';
                }

                $sql = "UPDATE sales SET paid_amount = ?, balance = ?, payment_status = ?, date_time = ? WHERE id = ?";
                $result = modifyData($sql, 'ddddsi', [$newPaid, $newBalance, $paymentStatus, getCurrentDateTime(), $sale_id]);

                if ($result !== false) {
                    $receiptNo = generateCode('RCT');
                    $sql = "INSERT INTO customer_receipts (sale_id, customer_id, receipt_no, amount, payment_method, reference, payment_date, notes, date_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    insertData($sql, 'iisdsssss', [$sale_id, $sale['customer_id'], $receiptNo, $payment_amount, $payment_method, $reference, $payment_date, $notes, getCurrentDateTime()]);

                    setFlash('Payment received successfully! Receipt #' . $receiptNo, 'success');
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?customer_id=' . $selectedCustomerId);
                    exit;
                } else {
                    $message = 'Error recording payment!';
                    $messageType = 'danger';
                }
            }
        }
    }
}

$customers = getRows("SELECT c.*, 
    (SELECT COALESCE(SUM(balance), 0) FROM sales WHERE customer_id = c.id AND payment_status != 'paid') as outstanding
    FROM customers c WHERE c.status = 'active' ORDER BY c.customer_name");

$selectedCustomerId = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : (isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0);

$invoices = [];
if ($selectedCustomerId > 0) {
    $invoices = getRows("SELECT * FROM sales WHERE customer_id = ? AND payment_status != 'paid' AND balance > 0 ORDER BY sale_date DESC", 'i', [$selectedCustomerId]);
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
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-hand-holding-usd me-2"></i>Customer Receiving
                <span class="ms-2 badge bg-success">PKR</span></span>
                <a href="../index.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Select Customer</label>
                        <select class="form-select" name="customer_id" onchange="this.form.submit()">
                            <option value="">-- Choose Customer --</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $selectedCustomerId == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['customer_name']); ?>
                                    <?php if ($c['company_name']): ?>(<?php echo htmlspecialchars($c['company_name']); ?>)<?php endif; ?>
                                    - Outstanding: <?php echo formatCurrency($c['outstanding']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

                <?php if ($selectedCustomerId > 0 && count($invoices) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Total (PKR)</th>
                                    <th>Paid (PKR)</th>
                                    <th>Balance (PKR)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $inv): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($inv['sale_no']); ?></strong></td>
                                        <td><?php echo date('d-m-Y', strtotime($inv['sale_date'])); ?></td>
                                        <td class="text-end"><?php echo formatCurrency($inv['total_amount']); ?></td>
                                        <td class="text-end"><?php echo formatCurrency($inv['paid_amount']); ?></td>
                                        <td class="text-end text-danger"><strong><?php echo formatCurrency($inv['balance']); ?></strong></td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal"
                                                data-id="<?php echo $inv['id']; ?>"
                                                data-invoice="<?php echo htmlspecialchars($inv['sale_no']); ?>"
                                                data-balance="<?php echo $inv['balance']; ?>"
                                                onclick="openPaymentModal(this)">
                                                <i class="fas fa-money-bill me-1"></i>Receive Payment
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($selectedCustomerId > 0): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No outstanding invoices for this customer.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i>Receive Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="sale_id" id="sale_id">
                    <input type="hidden" name="customer_id" value="<?php echo $selectedCustomerId; ?>">

                    <div class="mb-3">
                        <label class="form-label">Invoice #</label>
                        <input type="text" class="form-control" id="display_invoice" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Outstanding Balance (PKR)</label>
                        <input type="text" class="form-control" id="display_balance" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount (PKR) *</label>
                        <input type="number" step="0.01" class="form-control" id="payment_amount" name="payment_amount" min="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date *</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="credit_card">Credit Card</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="reference" class="form-label">Reference No</label>
                        <input type="text" class="form-control" id="reference" name="reference">
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_payment" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Receive Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPaymentModal(btn) {
    document.getElementById('sale_id').value = btn.dataset.id;
    document.getElementById('display_invoice').value = btn.dataset.invoice;
    document.getElementById('display_balance').value = parseFloat(btn.dataset.balance).toFixed(2);
    document.getElementById('payment_amount').value = '';
    document.getElementById('payment_amount').max = btn.dataset.balance;
}
</script>

<style>
@media print { .btn, .navbar-custom, #sidebar, .sidebar-header, .sidebar-nav { display: none !important; } #sidebar { width: 0 !important; } #content { margin-left: 0 !important; padding: 20px !important; } }
</style>

<?php include '../includes/footer.php'; ?>
