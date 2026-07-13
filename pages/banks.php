require_once '../includes/database.php';
requireLogin();

$pageTitle = 'Banks';

// Flash handling
$flash = getFlash();
$message = '';
$messageType = '';
if ($flash) {
    $message = $flash['message'];
    $messageType = $flash['type'];
}

// Delete handler
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $bankId = (int)$_GET['delete'];

    // Check if any account transactions reference this bank
    $checkResult = executePrepared(
        "SELECT COUNT(*) as cnt FROM accounts WHERE account_name = (SELECT bank_name FROM banks WHERE id = ?)",
        "i",
        [$bankId]
    );
    $checkRow = $checkResult->fetch_assoc();

    if ($checkRow && $checkRow['cnt'] > 0) {
        setFlash('Cannot delete this bank because it is referenced in account transactions.', 'danger');
    } else {
        $result = executePrepared("DELETE FROM banks WHERE id = ?", "i", [$bankId]);
        if ($result) {
            setFlash('Bank deleted successfully.', 'success');
        } else {
            setFlash('Failed to delete bank.', 'danger');
        }
    }
    header('Location: banks.php');
    exit;
}

// Query all banks
$banks = getRows("SELECT * FROM banks ORDER BY id DESC");

// Summary data
$totalBanks = count($banks);
$totalOpeningBalance = 0;
$totalCurrentBalance = 0;
foreach ($banks as $bank) {
    $totalOpeningBalance += $bank['opening_balance'];
    $totalCurrentBalance += $bank['current_balance'];
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Banks</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                        <li class="breadcrumb-item active">Banks</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php if ($message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card" style="border-top: 3px solid #1a2332;">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Total Banks</h5>
                            <h3 class="mb-0"><?php echo $totalBanks; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card" style="border-top: 3px solid #1a2332;">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Total Opening Balance</h5>
                            <h3 class="mb-0"><?php echo number_format($totalOpeningBalance, 2); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card" style="border-top: 3px solid #1a2332;">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Total Current Balance</h5>
                            <h3 class="mb-0"><?php echo number_format($totalCurrentBalance, 2); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card" style="border-top: 3px solid #28a745;">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Actions</h5>
                            <a href="add_bank.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add Bank
                            </a>
                            <a href="../index.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banks Table -->
            <div class="card" style="border-top: 3px solid #1a2332;">
                <div class="card-header" style="background-color: #1a2332; color: #fff;">
                    <h3 class="card-title"><i class="fas fa-university"></i> All Banks</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="banksTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bank Name</th>
                                    <th>Account No</th>
                                    <th>Account Type</th>
                                    <th>Opening Balance</th>
                                    <th>Current Balance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($banks as $index => $bank): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($bank['bank_name']); ?></td>
                                        <td><?php echo htmlspecialchars($bank['account_no']); ?></td>
                                        <td><?php echo htmlspecialchars($bank['account_type']); ?></td>
                                        <td class="text-right"><?php echo number_format($bank['opening_balance'], 2); ?></td>
                                        <td class="text-right"><?php echo number_format($bank['current_balance'], 2); ?></td>
                                        <td>
                                            <a href="add_bank.php?edit=<?php echo $bank['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="banks.php?delete=<?php echo $bank['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this bank?');">
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
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#banksTable').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 25,
        "language": {
            "emptyTable": "No banks found"
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
