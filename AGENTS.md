# AGENTS.md — Manufacturing ERP System

## Stack
- **Plain PHP** (no framework), **MySQLi** with prepared statements, **session-based auth**
- **No hashing** — passwords stored as plain text (`login.php:30`)
- **No CSRF protection** on any form
- **Database**: MySQL `erp_system` on `localhost` (`root` / empty password)
  - `includes/database.php` defines helpers: `getRow`, `getRows`, `insertData`, `modifyData`, `executePrepared`, `escapeString`
  - `erp_system.sql` is the canonical schema + seed; also present: `erp_new.sql`, `erp_system (1).sql`, `fix_missing_tables.sql` — no migration tool, ALTER TABLE manually
  - Session auto-starts at `database.php:209` — do NOT call `session_start()` again
- **Frontend**: Bootstrap 5.3, Font Awesome 6.4, jQuery 3.7, DataTables 1.13 — all **CDN-loaded**
  - jQuery loads in `header.php:300` (before inline scripts)
  - Bootstrap JS bundle loads in `footer.php:6`
  - DataTables CSS/JS loaded **per page** (not globally) — include only on list/table pages
- Default credentials: `admin` / `admin`

## Database Conventions
- **No `timestamp`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`** — use only `date_time DATETIME`
- Money: `DECIMAL(18,2)`; IDs: `INT AUTO_INCREMENT PRIMARY KEY`; Text: `VARCHAR` where possible
- `formatCurrency()` is just `number_format($amount, 2)` — it does NOT add currency symbols; labels like "PKR" are added in HTML

## Architecture
- `login.php`, `logout.php` — standalone auth entrypoints; logout.php calls `session_start()` directly (doesn't include database.php)
- `index.php` — dashboard (requires login, includes header/sidebar/footer)
- `includes/` — `header.php` (opens `<html>` + sidebar wrapper), `sidebar.php`, `footer.php` (closes wrapper + Bootstrap JS)
- `includes/product_detail.php` — anomaly: lives in `includes/` but follows the `pages/` pattern (uses `../includes/` paths); acts as a standalone page
- `pages/` — one file per module, called directly (e.g. `/erp_system/pages/raw_materials.php`)
- `sidebar.php` auto-detects `$isInPages` to set `$p` (prefix for links) and `$r` (root-prefix for back-to-root links)
  - `$p = $isInPages ? '' : 'pages/'` — links use `<?php echo $p; ?>filename.php`
  - `$r = $isInPages ? '../' : ''` — for links back to root files like `index.php`
  - Sidebar active-state uses `strpos()` on `$_SERVER['PHP_SELF']` — substrings can match (e.g. `sales` may match when on `raw_sales`)
- Accordion submenus: `data-bs-parent=".sidebar-nav"` on each collapse — opening one closes others

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
- **SQL files**: Multiple SQL files exist in root — `erp_system.sql` is canonical; others are incremental fixes or backups

## Development
- **No build step, no package manager, no tests, no CI** — PHP on XAMPP
- Run: create `erp_system` DB via phpMyAdmin → import `erp_system.sql` → visit `http://localhost/erp_system/`
- PHP binary at `C:\xampp\php\php.exe`
