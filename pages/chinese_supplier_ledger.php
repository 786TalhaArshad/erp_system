<?php
/**
 * Chinese Supplier Ledger Page
 * Manufacturing ERP System
 */

// Include database connection
require_once '../includes/database.php';

// Require login
requireLogin();

$pageTitle = 'Chinese Supplier Ledger';
$message = '';
$messageType = '';

// Get supplier ID from URL
$supplierId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($supplierId <= 0) {
    header('Location: chinese_suppliers.php');
    exit;
}

// Get supplier details with currency info and calculated current_balance
$supplier = getRow("SELECT s.*, c.currency_code, c.currency_name, c.symbol, c.exchange_rate,
    COALESCE(SUM(ip.balance_cny), 0) AS purchase_balance_cny,
    COALESCE((SELECT SUM(cp.amount_cny) FROM chinese_supplier_payments cp WHERE cp.supplier_id = s.id), 0) AS payments_made_cny,
    (s.opening_balance + COALESCE(SUM(ip.balance_cny), 0) - COALESCE((SELECT SUM(cp.amount_cny) FROM chinese_supplier_payments cp WHERE cp.supplier_id = s.id), 0)) AS current_balance
    FROM chinese_suppliers s
    LEFT JOIN currencies c ON s.currency_id = c.id
    LEFT JOIN import_purchases ip ON ip.supplier_id = s.id
    WHERE s.id = ?
    GROUP BY s.id", 'i', [$supplierId]);

if (!$supplier) {
    header('Location: chinese_suppliers.php');
    exit;
}

// Get date filters
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Get supplier transactions
$sql = "SELECT 
            'Purchase' as transaction_type,
            purchase_no as reference_no,
            purchase_date as transaction_date,
            total_cny as amount_cny,
            total_pkr as amount_pkr,
            paid_amount_cny as paid_cny,
            paid_amount_pkr as paid_pkr,
            balance_cny as balance_cny,
            balance_pkr as balance_pkr,
            payment_status,
            status,
            invoice_no,
            exchange_rate
        FROM import_purchases 
        WHERE supplier_id = ? 
        AND purchase_date BETWEEN ? AND ?
        ORDER BY purchase_date ASC, id ASC";
$purchases = getRows($sql, 'iss', [$supplierId, $fromDate, $toDate]);

// Get supplier payments
$sql = "SELECT 
            'Payment' as transaction_type,
            payment_no as reference_no,
            payment_date as transaction_date,
            amount_cny,
            amount_pkr,
            amount_cny as paid_cny,
            amount_pkr as paid_pkr,
            0 as balance_cny,
            0 as balance_pkr,
            'paid' as payment_status,
            'completed' as status,
            '' as invoice_no,
            exchange_rate
        FROM chinese_supplier_payments 
        WHERE supplier_id = ? 
        AND payment_date BETWEEN ? AND ?
        ORDER BY payment_date ASC, id ASC";
$payments = getRows($sql, 'iss', [$supplierId, $fromDate, $toDate]);

// Merge transactions
$transactions = array_merge($purchases, $payments);

// Sort by date
usort($transactions, function($a, $b) {
    return strtotime($a['transaction_date']) - strtotime($b['transaction_date']);
});

// Calculate running balance
$runningBalanceCNY = $supplier['opening_balance'];
$runningBalancePKR = $supplier['opening_balance'] * $supplier['exchange_rate'];

$exchangeRate = $supplier['exchange_rate'] ?: 40.5000;

// Include header
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/print_header.php';
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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="chinese_suppliers.php">Chinese Suppliers</a></li>
                <li class="breadcrumb-item"><a href="chinese_supplier_detail.php?id=<?php echo $supplierId; ?>">Supplier Detail</a></li>
                <li class="breadcrumb-item active">Supplier Ledger</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Supplier Header -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-book me-2"></i>Ledger - <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                <span class="ms-2 badge bg-info"><?php echo $supplier['currency_code']; ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Opening Balance:</strong>
                        <span class="text-primary"><?php echo $supplier['symbol']; ?> <?php echo formatCurrency($supplier['opening_balance']); ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Current Balance:</strong>
                        <span class="<?php echo $supplier['current_balance'] < 0 ? 'text-danger' : 'text-success'; ?>">
                            <?php echo $supplier['symbol']; ?> <?php echo formatCurrency($supplier['current_balance']); ?>
                        </span>
                        <br><small class="text-muted">PKR <?php echo formatCurrency($supplier['current_balance'] * $exchangeRate); ?></small>
                    </div>
                    <div class="col-md-3">
                        <strong>Exchange Rate:</strong>
                        <span class="text-info">1 <?php echo $supplier['currency_code']; ?> = <?php echo number_format($exchangeRate, 4); ?> PKR</span>
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Ledger
                        </button>
                        <a href="chinese_supplier_detail.php?id=<?php echo $supplierId; ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <input type="hidden" name="id" value="<?php echo $supplierId; ?>">
                    <div class="col-md-4">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" value="<?php echo $fromDate; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" value="<?php echo $toDate; ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="chinese_supplier_ledger.php?id=<?php echo $supplierId; ?>" class="btn btn-secondary ms-2">
                            <i class="fas fa-undo me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ledger Table -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>Transaction History
                <span class="ms-2 badge bg-warning text-dark">Amounts in <?php echo $supplier['currency_code']; ?></span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="ledgerTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Invoice No</th>
                                <th>Type</th>
                                <th>Amount (<?php echo $supplier['currency_code']; ?>)</th>
                                <th>Amount (PKR)</th>
                                <th>Paid (<?php echo $supplier['currency_code']; ?>)</th>
                                <th>Balance (<?php echo $supplier['currency_code']; ?>)</th>
                                <th>Running Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($transactions) > 0): ?>
                                <?php 
                                $counter = 1;
                                foreach ($transactions as $transaction): 
                                    if ($transaction['transaction_type'] == 'Purchase') {
                                        $runningBalanceCNY += $transaction['balance_cny'];
                                        $runningBalancePKR += $transaction['balance_pkr'];
                                    } else {
                                        $runningBalanceCNY -= $transaction['amount_cny'];
                                        $runningBalancePKR -= $transaction['amount_pkr'];
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($transaction['transaction_date'])); ?></td>
                                        <td>
                                            <a href="import_purchase_detail.php?id=<?php echo $transaction['reference_no']; ?>">
                                                <?php echo htmlspecialchars($transaction['reference_no']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($transaction['invoice_no']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $transaction['transaction_type'] == 'Purchase' ? 'primary' : 'success'; ?>">
                                                <?php echo $transaction['transaction_type']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatCurrency($transaction['amount_cny']); ?></td>
                                        <td><?php echo formatCurrency($transaction['amount_pkr']); ?></td>
                                        <td><?php echo formatCurrency($transaction['paid_cny']); ?></td>
                                        <td><?php echo formatCurrency($transaction['balance_cny']); ?></td>
                                        <td><?php echo $supplier['symbol']; ?> <?php echo formatCurrency($runningBalanceCNY); ?></td>
                                        <td>
                                            <span class="badge badge-status badge-<?php echo strtolower($transaction['payment_status']); ?>">
                                                <?php echo ucfirst($transaction['payment_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle me-2"></i>No transactions found for this period
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-primary">
                            <tr>
                                <th colspan="5" class="text-end">Total</th>
                                <th><?php echo $supplier['symbol']; ?> <?php echo formatCurrency(array_sum(array_column($transactions, 'amount_cny'))); ?></th>
                                <th>PKR <?php echo formatCurrency(array_sum(array_column($transactions, 'amount_pkr'))); ?></th>
                                <th><?php echo $supplier['symbol']; ?> <?php echo formatCurrency(array_sum(array_column($transactions, 'paid_cny'))); ?></th>
                                <th><?php echo $supplier['symbol']; ?> <?php echo formatCurrency(array_sum(array_column($transactions, 'balance_cny'))); ?></th>
                                <th><?php echo $supplier['symbol']; ?> <?php echo formatCurrency($runningBalanceCNY); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Print function
function printLedger() {
    window.print();
}
</script>

<?php
include '../includes/footer.php';
?>