<?php
// Auto-detect path prefix: works whether included from root or pages/
$isInPages = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
$p = $isInPages ? '' : 'pages/';
$r = $isInPages ? '../' : '';
?>
<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-header">
        <h3>ERP System</h3>
        <small>Manufacturing ERP v1.0</small>
    </div>
    
    <div class="sidebar-nav">
        <!-- Dashboard -->
        <div class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <a href="<?php echo $r; ?>index.php">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </div>
        
        <!-- Local Purchases -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'local_purchases') !== false || strpos($_SERVER['PHP_SELF'], 'add_local_purchase') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#localPurchaseSubmenu">
                <i class="fas fa-shopping-bag"></i> Local Purchases
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'local_purchases') !== false || strpos($_SERVER['PHP_SELF'], 'add_local_purchase') !== false ? 'show' : ''; ?>" id="localPurchaseSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <a href="<?php echo $p; ?>add_local_purchase.php"><i class="fas fa-angle-right me-2"></i>Add Purchase</a>
                    <a href="<?php echo $p; ?>local_purchases.php"><i class="fas fa-angle-right me-2"></i>View Purchase</a>
                </div>
            </div>
        </div>
        
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'import_purchases') !== false || strpos($_SERVER['PHP_SELF'], 'add_import_purchase') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#importPurchaseSubmenu">
                <i class="fas fa-ship"></i> Import Purchases (CNY)
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'import_purchases') !== false || strpos($_SERVER['PHP_SELF'], 'add_import_purchase') !== false ? 'show' : ''; ?>" id="importPurchaseSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <a href="<?php echo $p; ?>add_import_purchase.php"><i class="fas fa-angle-right me-2"></i>Add Purchase</a>
                    <a href="<?php echo $p; ?>import_purchases.php"><i class="fas fa-angle-right me-2"></i>View Purchase</a>
                </div>
            </div>
        </div>
        
        <!-- Suppliers -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'suppliers') !== false || strpos($_SERVER['PHP_SELF'], 'supplier_payments') !== false || strpos($_SERVER['PHP_SELF'], 'supplier_detail') !== false || strpos($_SERVER['PHP_SELF'], 'supplier_ledger') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#supplierSubmenu">
                <i class="fas fa-truck"></i> Suppliers
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'suppliers') !== false || strpos($_SERVER['PHP_SELF'], 'supplier_payments') !== false || strpos($_SERVER['PHP_SELF'], 'supplier_detail') !== false || strpos($_SERVER['PHP_SELF'], 'supplier_ledger') !== false ? 'show' : ''; ?>" id="supplierSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <div class="sub-group-label">Local Suppliers</div>
                    <a href="<?php echo $p; ?>local_suppliers.php"><i class="fas fa-angle-right me-2"></i>Supplier List</a>
                    <a href="<?php echo $p; ?>suppliers_summary.php"><i class="fas fa-angle-right me-2"></i>Summary</a>
                    <a href="<?php echo $p; ?>supplier_payments.php"><i class="fas fa-angle-right me-2"></i>Payments (PKR)</a>
                    <div class="sub-group-label mt-2">Chinese Suppliers</div>
                    <a href="<?php echo $p; ?>chinese_suppliers.php"><i class="fas fa-angle-right me-2"></i>Supplier List</a>
                    <a href="<?php echo $p; ?>chinese_suppliers_summary.php"><i class="fas fa-angle-right me-2"></i>Summary</a>
                    <a href="<?php echo $p; ?>chinese_supplier_payment.php"><i class="fas fa-angle-right me-2"></i>Payments (CNY)</a>
                </div>
            </div>
        </div>
        
        <!-- Inventory -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'raw_materials') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $p; ?>raw_materials.php">
                <i class="fas fa-cubes"></i> Raw Materials
            </a>
        </div>
        
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'finished_goods') !== false || strpos($_SERVER['PHP_SELF'], 'products') !== false || strpos($_SERVER['PHP_SELF'], 'add_product') !== false ? 'active' : ''; ?>">
            <a href="<?php echo $p; ?>finished_goods.php">
                <i class="fas fa-boxes"></i> Finished Goods
            </a>
        </div>
        
        <!-- Production -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'add_production') !== false || strpos($_SERVER['PHP_SELF'], 'production') !== false || strpos($_SERVER['PHP_SELF'], 'view_production') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#productionSubmenu">
                <i class="fas fa-cogs"></i> Production
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'add_production') !== false || strpos($_SERVER['PHP_SELF'], 'production') !== false || strpos($_SERVER['PHP_SELF'], 'view_production') !== false ? 'show' : ''; ?>" id="productionSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <a href="<?php echo $p; ?>add_production.php"><i class="fas fa-angle-right me-2"></i>New Production</a>
                    <a href="<?php echo $p; ?>production.php"><i class="fas fa-angle-right me-2"></i>View Production</a>
                </div>
            </div>
        </div>
        
        <!-- Sales -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'sales') !== false || strpos($_SERVER['PHP_SELF'], 'new_sale') !== false || strpos($_SERVER['PHP_SELF'], 'view_hold_bills') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#salesSubmenu">
                <i class="fas fa-handshake"></i> Sales
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'sales') !== false || strpos($_SERVER['PHP_SELF'], 'new_sale') !== false || strpos($_SERVER['PHP_SELF'], 'view_hold_bills') !== false ? 'show' : ''; ?>" id="salesSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <div class="sub-group-label">Sales Orders</div>
                    <a href="<?php echo $p; ?>new_sale.php"><i class="fas fa-angle-right me-2"></i>New Sale</a>
                    <a href="<?php echo $p; ?>sales.php"><i class="fas fa-angle-right me-2"></i>View Sale Invoice</a>
                    <a href="<?php echo $p; ?>view_hold_bills.php"><i class="fas fa-angle-right me-2"></i>View Hold Bills</a>
                </div>
            </div>
        </div>
        
        <!-- Customers -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'customers') !== false || strpos($_SERVER['PHP_SELF'], 'add_customer') !== false || strpos($_SERVER['PHP_SELF'], 'customer_receiving') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#customerSubmenu">
                <i class="fas fa-user-friends"></i> Customers
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'customers') !== false || strpos($_SERVER['PHP_SELF'], 'add_customer') !== false || strpos($_SERVER['PHP_SELF'], 'customer_receiving') !== false ? 'show' : ''; ?>" id="customerSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <a href="<?php echo $p; ?>add_customer.php"><i class="fas fa-angle-right me-2"></i>Add Customer</a>
                    <a href="<?php echo $p; ?>customers.php"><i class="fas fa-angle-right me-2"></i>View Customer</a>
                    <a href="<?php echo $p; ?>customer_receiving.php"><i class="fas fa-angle-right me-2"></i>Customer Receiving</a>
                </div>
            </div>
        </div>

        <!-- Party Ledger -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'party') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#partySubmenu">
                <i class="fas fa-address-book"></i> Party Ledger
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'party') !== false ? 'show' : ''; ?>" id="partySubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <a href="<?php echo $p; ?>add_party.php"><i class="fas fa-angle-right me-2"></i>Add Party</a>
                    <a href="<?php echo $p; ?>parties.php"><i class="fas fa-angle-right me-2"></i>View Parties</a>
                    <a href="<?php echo $p; ?>add_party_payable.php"><i class="fas fa-angle-right me-2"></i>Add Payable</a>
                    <a href="<?php echo $p; ?>add_party_received.php"><i class="fas fa-angle-right me-2"></i>Receive from Party</a>
                    <a href="<?php echo $p; ?>add_party_paid.php"><i class="fas fa-angle-right me-2"></i>Pay to Party</a>
                </div>
            </div>
        </div>

        <!-- Employees -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'employee') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#employeeSubmenu">
                <i class="fas fa-users"></i> Employees
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'employee') !== false ? 'show' : ''; ?>" id="employeeSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <a href="<?php echo $p; ?>add_employee.php"><i class="fas fa-angle-right me-2"></i>Add Employee</a>
                    <a href="<?php echo $p; ?>employees.php"><i class="fas fa-angle-right me-2"></i>View Employees</a>
                    <a href="<?php echo $p; ?>employee_payable.php"><i class="fas fa-angle-right me-2"></i>Employee Payable</a>
                    <a href="<?php echo $p; ?>employee_paid.php"><i class="fas fa-angle-right me-2"></i>Employee Paid</a>
                </div>
            </div>
        </div>
        
        <!-- Expenses -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'expense') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#expenseSubmenu">
                <i class="fas fa-money-bill-wave"></i> Expenses
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'expense') !== false ? 'show' : ''; ?>" id="expenseSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <a href="<?php echo $p; ?>expense_heads.php"><i class="fas fa-angle-right me-2"></i>Expense Heads</a>
                    <a href="<?php echo $p; ?>expenses.php"><i class="fas fa-angle-right me-2"></i>All Expenses</a>
                </div>
            </div>
        </div>
        
        <!-- Accounts -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'accounts') !== false || strpos($_SERVER['PHP_SELF'], 'balance') !== false || strpos($_SERVER['PHP_SELF'], 'cashbook') !== false || strpos($_SERVER['PHP_SELF'], 'bankbook') !== false || strpos($_SERVER['PHP_SELF'], 'banks') !== false || strpos($_SERVER['PHP_SELF'], 'add_bank') !== false || strpos($_SERVER['PHP_SELF'], 'company_settings') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#accountsSubmenu">
                <i class="fas fa-book"></i> Accounts
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'accounts') !== false || strpos($_SERVER['PHP_SELF'], 'balance') !== false || strpos($_SERVER['PHP_SELF'], 'cashbook') !== false || strpos($_SERVER['PHP_SELF'], 'bankbook') !== false || strpos($_SERVER['PHP_SELF'], 'banks') !== false || strpos($_SERVER['PHP_SELF'], 'add_bank') !== false || strpos($_SERVER['PHP_SELF'], 'company_settings') !== false ? 'show' : ''; ?>" id="accountsSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <a href="<?php echo $p; ?>accounts.php"><i class="fas fa-angle-right me-2"></i>Ledger</a>
                    <a href="<?php echo $p; ?>cashbook.php"><i class="fas fa-angle-right me-2"></i>Cash Book</a>
                    <a href="<?php echo $p; ?>bankbook.php"><i class="fas fa-angle-right me-2"></i>Bank Book</a>
                    <a href="<?php echo $p; ?>banks.php"><i class="fas fa-angle-right me-2"></i>Banks</a>
                    <a href="<?php echo $p; ?>balance_sheet.php"><i class="fas fa-angle-right me-2"></i>Balance Sheet</a>
                    <div class="sub-group-label mt-2">Setup</div>
                    <a href="<?php echo $p; ?>company_settings.php"><i class="fas fa-angle-right me-2"></i>Company Settings</a>
                </div>
            </div>
        </div>
        
        <!-- Reports -->
        <div class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'reports') !== false || strpos($_SERVER['PHP_SELF'], 'closing') !== false || strpos($_SERVER['PHP_SELF'], 'supplier_ledger') !== false || strpos($_SERVER['PHP_SELF'], 'customer_ledger') !== false || strpos($_SERVER['PHP_SELF'], 'chinese_supplier_ledger') !== false || strpos($_SERVER['PHP_SELF'], 'suppliers_summary') !== false || strpos($_SERVER['PHP_SELF'], 'chinese_suppliers_summary') !== false ? 'active' : ''; ?>">
            <a href="#" data-bs-toggle="collapse" data-bs-target="#reportsSubmenu">
                <i class="fas fa-chart-bar"></i> Reports
                <i class="fas fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse <?php echo strpos($_SERVER['PHP_SELF'], 'reports') !== false || strpos($_SERVER['PHP_SELF'], 'closing') !== false || strpos($_SERVER['PHP_SELF'], 'supplier_ledger') !== false || strpos($_SERVER['PHP_SELF'], 'customer_ledger') !== false || strpos($_SERVER['PHP_SELF'], 'chinese_supplier_ledger') !== false || strpos($_SERVER['PHP_SELF'], 'suppliers_summary') !== false || strpos($_SERVER['PHP_SELF'], 'chinese_suppliers_summary') !== false ? 'show' : ''; ?>" id="reportsSubmenu" data-bs-parent=".sidebar-nav">
                <div class="sub-menu">
                    <div class="sub-group-label">Financial</div>
                    <a href="<?php echo $p; ?>reports_profit_loss.php"><i class="fas fa-angle-right me-2"></i>Profit & Loss</a>
                    <a href="<?php echo $p; ?>reports_trial_balance.php"><i class="fas fa-angle-right me-2"></i>Trial Balance</a>
                    <a href="<?php echo $p; ?>reports_cashflow.php"><i class="fas fa-angle-right me-2"></i>Cash Flow</a>
                    <div class="sub-group-label mt-2">Business</div>
                    <a href="<?php echo $p; ?>reports_purchases.php"><i class="fas fa-angle-right me-2"></i>Purchase Reports</a>
                    <a href="<?php echo $p; ?>reports_sales.php"><i class="fas fa-angle-right me-2"></i>Sales Reports</a>
                    <a href="<?php echo $p; ?>reports_inventory.php"><i class="fas fa-angle-right me-2"></i>Inventory Reports</a>
                    <a href="<?php echo $p; ?>reports_expenses.php"><i class="fas fa-angle-right me-2"></i>Expense Reports</a>
                    <a href="<?php echo $p; ?>reports_production.php"><i class="fas fa-angle-right me-2"></i>Production Reports</a>
                    <div class="sub-group-label mt-2">Parties & People</div>
                    <a href="<?php echo $p; ?>reports_suppliers.php"><i class="fas fa-angle-right me-2"></i>Supplier Reports</a>
                    <a href="<?php echo $p; ?>reports_customers.php"><i class="fas fa-angle-right me-2"></i>Customer Reports</a>
                    <a href="<?php echo $p; ?>reports_parties.php"><i class="fas fa-angle-right me-2"></i>Party Ledger Reports</a>
                    <a href="<?php echo $p; ?>reports_employees.php"><i class="fas fa-angle-right me-2"></i>Employee Reports</a>
                    <div class="sub-group-label mt-2">Closing Balances</div>
                    <a href="<?php echo $p; ?>all_customers_closing.php"><i class="fas fa-angle-right me-2"></i>All Customers Closing</a>
                    <a href="<?php echo $p; ?>all_suppliers_closing.php"><i class="fas fa-angle-right me-2"></i>All Suppliers Closing</a>
                    <a href="<?php echo $p; ?>all_parties_closing.php"><i class="fas fa-angle-right me-2"></i>All Parties Closing</a>
                    <div class="sub-group-label mt-2">Ledgers</div>
                    <a href="<?php echo $p; ?>customer_ledger.php"><i class="fas fa-angle-right me-2"></i>Customer Ledger</a>
                    <a href="<?php echo $p; ?>supplier_ledger.php"><i class="fas fa-angle-right me-2"></i>Local Supplier Ledger</a>
                    <a href="<?php echo $p; ?>chinese_supplier_ledger.php"><i class="fas fa-angle-right me-2"></i>Chinese Supplier Ledger</a>
                    <a href="<?php echo $p; ?>suppliers_summary.php"><i class="fas fa-angle-right me-2"></i>Local Supplier Summary</a>
                    <a href="<?php echo $p; ?>chinese_suppliers_summary.php"><i class="fas fa-angle-right me-2"></i>Chinese Supplier Summary</a>
                </div>
            </div>
        </div>
        
        <!-- Logout -->
        <div class="nav-item">
            <a href="<?php echo $r; ?>logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div id="content">
    <!-- Top Navbar -->
    <nav class="navbar-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button type="button" id="sidebarCollapse" class="btn btn-light">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="ms-3 fw-bold"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></span>
            </div>
            <div class="user-info">
                <span><?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User'; ?></span>
                <div class="avatar">
                    <?php echo isset($_SESSION['full_name']) ? substr($_SESSION['full_name'], 0, 1) : 'U'; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Page Content Start -->
    <div class="container-fluid px-0">
