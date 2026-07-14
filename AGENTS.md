# AGENTS.md — Manufacturing ERP System

## Stack
- **Plain PHP** (no framework), **MySQLi** with prepared statements, **session-based auth**
- **No hashing** — passwords stored as plain text (`login.php:30`)
- **No CSRF protection** on any form
- **Database**: MySQL `erp_system` on `localhost` (`root` / empty password)
  - `includes/database.php` defines helpers: `getRow`, `getRows`, `insertData`, `modifyData`, `executePrepared`, `escapeString`
  - `erp_system.sql` is the canonical schema + seed — no migration tool, ALTER TABLE manually
   - Session auto-starts at `database.php:210` — do NOT call `session_start()` again
- **Frontend**: Bootstrap 5.3, Font Awesome 6.4, jQuery 3.7, DataTables 1.13 — all **CDN-loaded**
  - jQuery loads in `header.php:300` (before inline scripts)
  - Bootstrap JS bundle loads in `footer.php:6`
  - DataTables CSS/JS loaded **per page** (not globally) — include only on list/table pages
- Default credentials: `admin` / `admin`

## Database Conventions
- **No `timestamp`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`** — use only `date_time DATETIME`
- Money: `DECIMAL(18,2)`; IDs: `INT AUTO_INCREMENT PRIMARY KEY`; Text: `VARCHAR` where possible
- `formatCurrency()` is just `number_format($amount, 2)` — it does NOT add currency symbols; labels like "PKR" are added in HTML
- **Avoid `SELECT t.* ... GROUP BY t.id`** — MySQL 8's `ONLY_FULL_GROUP_BY` rejects non-aggregated columns not in GROUP BY. Use a correlated subquery instead: `SELECT t.*, COALESCE((SELECT SUM(...) FROM child c WHERE c.fk = t.id), 0) as total ... FROM parent t`

## Architecture
- `login.php`, `logout.php` — standalone auth entrypoints; logout.php calls `session_start()` directly (doesn't include database.php)
- `index.php` — dashboard (requires login, includes header/sidebar/footer)
- `includes/` — `header.php` (opens `<html>` + sidebar wrapper), `sidebar.php`, `footer.php` (closes wrapper + Bootstrap JS), `print_header.php` (shared print header with company info, used by report/print pages)
- `includes/product_detail.php` — anomaly: lives in `includes/` but follows the `pages/` pattern (uses `../includes/` paths); acts as a standalone page
- `pages/` — one file per module, called directly (e.g. `/erp_system/pages/raw_materials.php`)
- `sidebar.php` auto-detects `$isInPages` to set `$p` (prefix for links) and `$r` (root-prefix for back-to-root links)
  - `$p = $isInPages ? '' : 'pages/'` — links use `<?php echo $p; ?>filename.php`
  - `$r = $isInPages ? '../' : ''` — for links back to root files like `index.php`
  - Sidebar active-state uses `in_array($page, $array)` with exact filename arrays per section — NOT `strpos()` (which caused false positives like `all_customers_closing.php` matching the Customers section)
  - `$page = basename($_SERVER['PHP_SELF'])` is set at top; each section has an explicit filename list (e.g. `$salesPages`, `$customerPages`, `$reportPages`)
  - **CRITICAL**: Sidebar arrays use `*Pages` suffix (`$supplierPages`, `$customerPages`, etc.) because `sidebar.php` is `include`d inside the page's scope — using bare names like `$customers` or `$sales` silently overwrites the page's query results with a string array, causing `TypeError: Cannot access offset of type string on string` in the template. The comment at `sidebar.php:9` documents this.
  - When adding a new page, add its filename to the relevant array in `sidebar.php` to get correct active/open behavior
- Accordion submenus: `data-bs-parent=".sidebar-nav"` on each collapse — opening one closes others
- **Sidebar gaps**: `material_issue.php`, `sale_refund.php`, `add_expense_head.php` exist in `pages/` but are NOT in any sidebar filename array — they load but get no active-state highlighting. Detail pages (`*_detail.php`) and print pages (`*_print.php`) are also unlisted (intentional — they are secondary views)
- **AJAX endpoints**: `ajax_customer_balance.php`, `ajax_import_purchase.php` return JSON via `header('Content-Type: application/json')` — they do NOT include sidebar/footer and don't follow the page pattern

## Page Pattern
Every `pages/*.php`:
```php
require_once '../includes/database.php';
requireLogin();
$pageTitle = '...';
// flash handling
$flash = getFlash();
if ($flash) { $message = $flash['message']; $messageType = $flash['type']; }
// POST handler → setFlash() + header('Location: ...') + exit (PRG pattern)
// GET logic
include '../includes/header.php';
include '../includes/sidebar.php';
?>
<!-- HTML content -->
<?php include '../includes/footer.php'; ?>
```

## PRG Pattern (Post-Redirect-Get)
All pages use `setFlash()`/`getFlash()` (`database.php:216-230`) to avoid duplicate form submissions:
- On POST success: `setFlash('msg', 'success'); header('Location: ' . $_SERVER['PHP_SELF']); exit;`
- On POST failure: set `$message`/`$messageType` directly (no redirect, form values persist)
- No output buffering — all redirects must happen before any HTML output

## Delete Pattern
- Deletes use **GET parameters** (`?delete=X`), not POST — every list page follows this
- After delete, stock changes are reversed (sales restore FG stock, purchases restore RM stock)
- Products/customers with dependent records are blocked from deletion

## Business Flow
```
Chinese Supplier → Import Purchase (CNY) → Raw Materials
                                                    ↕
                                         Local Purchase (PKR)
                                                    ↓
                                         Production / Assembly → Material Issue
                                                    ↓
                                              Finished Goods
                                                    ↓
                                               Sales → Customer Ledger
```
Also: Employee Ledger, Expense Management, Accounts, Reports.

## Key Conventions
- **Dual currency**: Chinese suppliers/purchases in CNY with exchange rate → PKR conversion for inventory. Chinese ledgers stay in CNY. Exchange rates stored in `currencies` table.
- **Codes**: `generateCode('LP')` → `LP20260710_1234` — prefixes: `LP`, `RM`, `FG`, `RCT`, `PRD`, `SL` (sales), `AT` (account transactions)
- **Stock tracking**: Purchases increment `raw_materials.current_stock`; production deducts raw materials and adds finished goods; sales decrement finished goods
- **Delete protection**: Products with sales records cannot be deleted
- **Output escaping**: `htmlspecialchars()` is used in many places but NOT universally — when adding new output, always escape user data
- **Forms**: Inline `<style>`/`<script>` per page; add forms are standalone pages (e.g. `add_customer.php`) or modals (`data-bs-toggle`); edit forms use data-attributes + JS to populate modals (no page reload)
- **Customers** split: Add (`add_customer.php`) and View (`customers.php`) are separate pages; Customer Receiving (`customer_receiving.php`) is separate
- **Production**: One-step flow — creates order as `'completed'`, deducts raw materials, adds finished goods with weighted-average cost
- **Sales**: Can be `'hold'` (draft) or `'completed'`; hold bills are editable from `view_hold_bills.php`
- **Sales balance**: In `new_sale.php`, `balance = total_amount - discount` (NOT `final_amount` which includes prev balance for display). `final_amount` = `bill - discount` (excludes prev balance). Prev balance is display-only in the UI.
- **Sales table columns**: `sales` table has `customer_type`, `walkin_name`, `walkin_phone`, `final_amount`, `payment_method` beyond the base columns. INSERT queries must include all of them.
- **customer_receiving.php balance**: Receipt delete recalculates using `total_amount - discount` to stay consistent with `new_sale.php` balance convention.
- **SQL files**: `erp_system.sql` is the canonical schema + seed file in root

## Development
- **No build step, no package manager, no tests, no CI** — PHP on XAMPP
- Run: create `erp_system` DB via phpMyAdmin → import `erp_system.sql` → visit `http://localhost/erp_system/`
- PHP binary at `C:\xampp\php\php.exe`
- To run one-off PHP: `C:\xampp\php\php.exe -r "require_once 'C:/xampp/htdocs/erp_system/includes/database.php'; ..."`
