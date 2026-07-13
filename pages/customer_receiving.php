<?php
require_once '../includes/database.php';
requireLogin();

$pageTitle = 'Customer Payment Receiving';
$message = '';
$messageType = '';

$flash = getFlash();
if ($flash) {
    $message = $flash['message'];
    $messageType = $flash['type'];
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $receipt = getRow("SELECT id FROM customer_receipts WHERE id = ?", 'i', [$id]);
    if ($receipt) {
        $r = getRow("SELECT sale_id, amount FROM customer_receipts WHERE id = ?", 'i', [$id]);
        if ($r) {
            $sale = getRow("SELECT paid_amount, total_amount, discount FROM sales WHERE id = ?", 'i', [$r['sale_id']]);
            if ($sale) {
                $newPaid = max(0, $sale['paid_amount'] - $r['amount']);
                $newBalance = ($sale['total_amount'] - ($sale['discount'] ?? 0)) - $newPaid;
                $newStatus = $newPaid >= ($sale['total_amount'] - ($sale['discount'] ?? 0)) ? 'paid' : ($newPaid > 0 ? 'partial' : 'unpaid');
                modifyData("UPDATE sales SET paid_amount = ?, balance = ?, payment_status = ? WHERE id = ?", 'ddsi', [$newPaid, $newBalance, $newStatus, $r['sale_id']]);
            }
        }
        modifyData("DELETE FROM customer_receipts WHERE id = ?", 'i', [$id]);
        setFlash('Payment deleted successfully!', 'success');
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = (int)$_POST['customer_id'];
    $payment_date = $_POST['payment_date'];
    $amount = (float)($_POST['amount'] ?? 0);
    $payment_type = $_POST['payment_type'] ?? '';
    $bank_name = trim($_POST['bank_name'] ?? '');
    $cheque_no = trim($_POST['cheque_no'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($customer_id)) {
        $message = 'Please select a customer!';
        $messageType = 'danger';
    } elseif (empty($payment_date)) {
        $message = 'Please select payment date!';
        $messageType = 'danger';
    } elseif ($amount <= 0) {
        $message = 'Amount must be greater than 0!';
        $messageType = 'danger';
    } else {
        $currentDateTime = getCurrentDateTime();
        $receiptNo = generateCode('RCT');

        $result = insertData("INSERT INTO customer_receipts (customer_id, receipt_no, amount, payment_method, payment_date, notes, date_time) VALUES (?, ?, ?, ?, ?, ?, ?)",
            'isdsss', [$customer_id, $receiptNo, $amount, $payment_type, $payment_date, $notes, $currentDateTime]);

        if ($result) {
            $customerRow = getRow("SELECT customer_name FROM customers WHERE id = ?", 'i', [$customer_id]);
            $customerName = $customerRow ? $customerRow['customer_name'] : 'Customer';

            if ($payment_type === 'cash') {
                $lastEntry = getRow("SELECT balance FROM accounts WHERE account_type = 'Cash' ORDER BY id DESC LIMIT 1");
                $lastBalance = $lastEntry ? (float)$lastEntry['balance'] : 0;
                insertData(
                    "INSERT INTO accounts (account_date, account_type, reference_type, reference_id, description, debit, credit, balance, date_time) VALUES (?, 'Cash', 'receipt', ?, ?, 0, ?, ?, ?)",
                    'sisdds', [$payment_date, $customer_id, 'Payment received from ' . $customerName . ' (Receipt: ' . $receiptNo . ')', $amount, $lastBalance + $amount, $currentDateTime]
                );
            } else {
                $lastEntry = getRow("SELECT balance FROM accounts WHERE account_type = 'Bank' ORDER BY id DESC LIMIT 1");
                $lastBalance = $lastEntry ? (float)$lastEntry['balance'] : 0;
                $desc = 'Payment received from ' . $customerName . ($bank_name ? ' via ' . $bank_name : '') . ' (Receipt: ' . $receiptNo . ')';
                insertData(
                    "INSERT INTO accounts (account_date, account_type, reference_type, reference_id, description, debit, credit, balance, date_time) VALUES (?, 'Bank', 'receipt', ?, ?, 0, ?, ?, ?)",
                    'sisdds', [$payment_date, $customer_id, $desc, $amount, $lastBalance + $amount, $currentDateTime]
                );
            }

            setFlash('Payment received! Receipt #' . $receiptNo, 'success');
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $message = 'Error recording payment!';
            $messageType = 'danger';
        }
    }
}

$customers = getRows("SELECT c.id, c.customer_name, c.company_name, c.opening_balance,
    COALESCE(SUM(s.balance), 0) AS outstanding_sales,
    COALESCE((SELECT SUM(cr.amount) FROM customer_receipts cr WHERE cr.customer_id = c.id), 0) AS payments_received,
    (c.opening_balance + COALESCE(SUM(s.balance), 0) - COALESCE((SELECT SUM(cr.amount) FROM customer_receipts cr WHERE cr.customer_id = c.id), 0)) AS current_balance
    FROM customers c
    LEFT JOIN sales s ON s.customer_id = c.id AND s.payment_status != 'paid'
    WHERE c.status = 'active'
    GROUP BY c.id
    ORDER BY c.customer_name");

$receipts = getRows("SELECT cr.*, c.customer_name, c.company_name
    FROM customer_receipts cr
    LEFT JOIN customers c ON cr.customer_id = c.id
    ORDER BY cr.id DESC LIMIT 100");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<style>
#balanceBox { background: #f8f9fa; border-left: 4px solid #1a2332; padding: 12px 18px; border-radius: 0 6px 6px 0; margin-top: 10px; display: none; }
#balanceBox .bal-label { font-size: 12px; color: #666; }
#balanceBox .bal-amount { font-size: 18px; font-weight: 700; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:#1a2332;color:#fff;">
                <span><i class="fas fa-hand-holding-usd me-2"></i>Receive Customer Payment</span>
                <a href="../index.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer *</label>
                            <select class="form-select" id="customerSelect" name="customer_id" required>
                                <option value="">Select Customer</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"
                                        data-balance="<?php echo $c['current_balance']; ?>"
                                        data-opening="<?php echo $c['opening_balance']; ?>"
                                        data-payments="<?php echo $c['payments_received']; ?>">
                                        <?php echo htmlspecialchars($c['customer_name']); ?>
                                        <?php if ($c['company_name']): ?> (<?php echo htmlspecialchars($c['company_name']); ?>)<?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="balanceBox">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="bal-label">Opening Balance</div>
                                        <div class="bal-amount" id="balOpening">0.00</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="bal-label">Received</div>
                                        <div class="bal-amount text-success" id="balPayments">0.00</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="bal-label">Balance Due</div>
                                        <div class="bal-amount text-danger" id="balCurrent">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Date *</label>
                            <input type="date" class="form-control" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Amount (PKR) *</label>
                            <input type="number" step="0.01" class="form-control" id="payAmount" name="amount" min="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Payment Type *</label>
                            <select class="form-select" id="payType" name="payment_type" required>
                                <option value="cash">Cash In</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Reference No</label>
                            <input type="text" class="form-control" name="reference_no">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3" id="bankDiv" style="display:none">
                            <label class="form-label">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name">
                        </div>
                        <div class="col-md-4 mb-3" id="chequeDiv" style="display:none">
                            <label class="form-label">Cheque No</label>
                            <input type="text" class="form-control" name="cheque_no">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Notes</label>
                            <input type="text" class="form-control" name="notes">
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-paper-plane me-2"></i>Submit Payment</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:#1a2332;color:#fff;">
                <span><i class="fas fa-history me-2"></i>Recent Payments</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="paymentTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Receipt No</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($receipts as $r): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($r['receipt_no']); ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($r['customer_name']); ?>
                                        <?php if ($r['company_name']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($r['company_name']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d-m-Y', strtotime($r['payment_date'])); ?></td>
                                    <td class="text-success"><strong><?php echo formatCurrency($r['amount']); ?></strong></td>
                                    <td><span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $r['payment_method'])); ?></span></td>
                                    <td>
                                        <a href="?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger delete-confirm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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
function fmtNum(v) { return parseFloat(v).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

$(document).ready(function() {
    $('#customerSelect').on('change', function() {
        var opt = $(this).find('option:selected');
        var box = $('#balanceBox');
        if (!$(this).val()) { box.hide(); return; }
        var opening = parseFloat(opt.data('opening')) || 0;
        var payments = parseFloat(opt.data('payments')) || 0;
        var current  = parseFloat(opt.data('balance')) || 0;
        $('#balOpening').text(fmtNum(opening));
        $('#balPayments').text(fmtNum(payments));
        $('#balCurrent').text(fmtNum(current));
        $('#payAmount').attr('max', current).attr('placeholder', 'Max: ' + fmtNum(current));
        box.show();
    });

    $('#payType').on('change', function() {
        var t = $(this).val();
        $('#bankDiv').toggle(t === 'bank_transfer' || t === 'cheque');
        $('#chequeDiv').toggle(t === 'cheque');
    });

    $('#paymentTable').DataTable({ pageLength: 10, order: [[0, 'desc']], language: { emptyTable: 'No payments found' } });
});
</script>

<?php include '../includes/footer.php'; ?>
