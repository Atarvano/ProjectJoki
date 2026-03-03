# Technology Stack

**Project:** Sicuti HRD Cuti Tracker
**Researched:** 2026-03-03

## Recommended Stack

This project should be built as a **server-rendered procedural PHP prototype** with **Bootstrap 5.3.x**, a **small custom CSS layer**, **plain JavaScript only where Bootstrap or simple UI behavior needs it**, and **local PHP array data files** as the entire v1 data layer.

This is not a generic SaaS recommendation. It is the most appropriate stack for this exact constraint set:
- greenfield prototype
- native procedural PHP only
- no database in v1
- frontend-first delivery
- Excel export required
- enterprise-clean UI, not design-heavy marketing-tech complexity

### Core Framework

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| PHP | **8.4.x** | Application runtime | Best default for 2026 greenfield work here: currently in active support, modern, stable, and still simple for procedural apps. Prefer 8.4 over 8.5 for a prototype unless the environment already standardizes on 8.5. |
| Native procedural PHP | Project architecture | Routing-by-page, rendering, form handling, calculation, export | Matches the explicit project constraint. Keep business logic in functions and includes, not classes or a framework. |

### Frontend

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| Bootstrap | **5.3.8** | Layout, forms, nav, cards, tables, modal/offcanvas behavior | Current documented 5.3 release is ideal for fast enterprise UI delivery without introducing a build pipeline. |
| Native CSS | Current browser CSS | Brand layer, spacing system, dashboard polish, table styling | Required to avoid a generic Bootstrap look. Use CSS variables and a small design token layer. |
| Vanilla JavaScript | ES2020+ browser baseline | Minor UI interactions only | Enough for login-role toggle visuals, table filters, print/export triggers, and responsive navigation. No SPA needed. |

### Data / Persistence

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| PHP arrays in dedicated data files | N/A | Dummy employees, leave reports, sample calculations | Fastest v1 path. Zero schema migration overhead. Easy to inspect and edit during demo iteration. |
| Session (optional, minimal) | Built into PHP | Keep visual login role or selected employee across pages | Acceptable even for visual-only auth flow, but keep usage shallow and non-security-critical. |

### Export

| Technology | Version | Purpose | Why |
|------------|---------|---------|-----|
| `fputcsv()` + streamed download headers | Built into PHP | **Default v1 Excel export** | Best choice for this prototype. Excel opens CSV directly, no extra dependency, trivial to debug, and fully aligned with flat report exports. |
| PhpSpreadsheet | Current release only if needed later | **Fallback only when true `.xlsx` is explicitly required** | Valid library in 2026, but heavier than the prototype needs. Keep it out of v1 unless formatted multi-sheet `.xlsx` becomes a hard requirement. |

### Supporting Libraries / Assets

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| Bootstrap Icons | Current matching release | Optional UI iconography | Use sparingly for dashboard actions, status labels, export buttons. |
| Composer | 2.x | Dependency management | Only needed if you later add PhpSpreadsheet. Not required for baseline v1 if export stays CSV-only. |

## Prescriptive Implementation Choices

### 1) PHP structure: keep it flat, modular, and procedural

Use a small include-based structure like this:

```text
/index.php                  # landing page
/login.php                  # visual login entry
/dashboard.php              # HR calculator/dashboard
/employee.php               # employee self-view
/reports.php                # report list + export trigger
/export-reports.php         # CSV/Excel-compatible download endpoint

/includes/
  app.php                   # bootstrap file: config, requires, constants
  functions.php             # shared pure functions
  layout-header.php         # shared <head> + navbar start
  layout-sidebar.php        # dashboard sidebar if needed
  layout-footer.php         # scripts + footer
  auth-visual.php           # visual role handling helpers only
  calculations.php          # leave entitlement rules
  report-formatters.php     # row mapping for tables/export

/data/
  employees.php             # employee dummy array
  leave_reports.php         # generated-looking report array
  leave_policy.php          # constants like year 7/8 = 6 days

/assets/
  /css/
    bootstrap.min.css       # local copy preferred for stable demos
    app.css                 # global custom styling
    landing.css             # landing-page-specific layer
    dashboard.css           # dashboard/table/report styles
  /js/
    bootstrap.bundle.min.js
    app.js                  # small custom interactions only
  /img/
    ...
```

Recommended rule: **pages orchestrate, includes render, functions calculate, data files only hold arrays**.

Do **not** place large business logic blocks directly inside page templates. Keep the pages readable:

```php
<?php
require __DIR__ . '/includes/app.php';

$joinYear = isset($_GET['join_year']) ? (int) $_GET['join_year'] : null;
$result = $joinYear ? build_leave_entitlement_table($joinYear, (int) date('Y')) : [];

require __DIR__ . '/includes/layout-header.php';
// page markup
require __DIR__ . '/includes/layout-footer.php';
```

### 2) Frontend assets: Bootstrap locally, custom CSS intentionally

Recommendation:
- Use **Bootstrap 5.3.8**
- Store **local copies** of `bootstrap.min.css` and `bootstrap.bundle.min.js` inside `/assets/`
- Layer custom CSS on top instead of editing Bootstrap files

Why local instead of CDN for this prototype:
- demo reliability on internal/local networks
- no broken styling if CDN access is blocked
- easier version control over the exact visual baseline

CSS approach:
- Create a tiny token layer in `app.css` using custom properties
- Use Bootstrap for grid, forms, tables, cards, nav, modal/offcanvas
- Use native CSS for brand color, radius, shadows, chart placeholders, hero section, dashboard spacing, and table emphasis

Recommended UI pattern:
- **Landing page:** Bootstrap grid + strong hero + proof cards + clear CTA cards for HR vs Employee
- **Login page:** visual role selection only, not real auth
- **Dashboard:** top navbar + left sidebar/offcanvas + stat cards + calculator form + result table
- **Reports page:** filter toolbar + striped table + export action pinned high in the layout

### 3) Excel export approach: use CSV first, not `.xlsx`

For **v1**, recommend exporting **UTF-8 CSV** that opens directly in Excel.

This is the right call because:
- the exported content is tabular, not spreadsheet-complex
- no formulas/charts/multi-sheet formatting are required
- built-in PHP functions are enough
- fewer failure points than generating `.xlsx`
- fully compatible with a prototype that uses local arrays

Implementation guidance:
- Use a dedicated endpoint like `/export-reports.php`
- Send download headers before any output
- Stream with `php://output`
- Use `fputcsv()` with the **escape parameter explicitly set**
- Write a UTF-8 BOM so Excel handles Unicode names better

Recommended pattern:

```php
<?php
require __DIR__ . '/includes/app.php';

$rows = get_leave_report_export_rows();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="leave-reports.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, ['Employee ID', 'Name', 'Join Year', 'Year', 'Entitlement', 'Status'], ',', '"', '');

foreach ($rows as $row) {
    fputcsv($out, $row, ',', '"', '');
}

fclose($out);
exit;
```

Important details:
- In modern PHP, relying on `fputcsv()`'s default `escape` value is deprecated; pass it explicitly.
- Do not output whitespace/BOM from the PHP file itself before headers.
- Save export scripts as **UTF-8 without BOM**; add BOM only to the CSV stream intentionally.

When to allow PhpSpreadsheet later:
- stakeholder explicitly asks for real `.xlsx`
- multiple worksheets are needed
- styled headers, auto widths, filters, frozen panes, formulas, or branded workbook formatting are needed

If that happens, isolate it to `export-reports-xlsx.php` and keep the rest of the app procedural. The app can remain procedural even if one library internally uses OOP.

### 4) Local dummy data organization: separate entities from rules

Use multiple array files, not one giant mixed blob.

Recommended split:

#### `data/employees.php`
Contains employee master demo records.

```php
<?php
return [
    [
        'employee_id' => 'EMP001',
        'name' => 'Alya Putri',
        'department' => 'HR',
        'join_year' => 2019,
        'role_view' => 'employee',
    ],
];
```

#### `data/leave_policy.php`
Contains leave-rule constants and labels, not employee data.

```php
<?php
return [
    'projection_years' => 8,
    'year_7_days' => 6,
    'year_8_days' => 6,
    'status_labels' => [
        'available' => 'Available',
        'upcoming' => 'Upcoming',
        'expired' => 'Expired',
    ],
];
```

#### `data/leave_reports.php`
Contains saved-looking demo report rows for the reports screen.

```php
<?php
return [
    [
        'employee_id' => 'EMP001',
        'employee_name' => 'Alya Putri',
        'join_year' => 2019,
        'generated_at' => '2026-03-01',
        'rows' => [
            ['year' => 1, 'days' => 0, 'status' => 'completed'],
            ['year' => 7, 'days' => 6, 'status' => 'eligible'],
        ],
    ],
];
```

Practical rule: **data files return arrays only**. No rendering, no calculations, no side effects.

### 5) Calculation logic: pure functions, not inline conditions everywhere

Put all entitlement logic in `includes/calculations.php`.

Recommended functions:
- `build_leave_entitlement_table(int $joinYear, int $currentYear): array`
- `calculate_entitlement_days_for_year(int $relativeYear): int`
- `calculate_status_for_row(array $row, int $currentYear): string`

Why this matters:
- easier to validate the 8-year policy rule
- easier to change policy in v2
- easier to reuse for HR dashboard, employee view, and export

For this project, hard-code the requested rule clearly:
- year 7 = 6 leave days
- year 8 = 6 leave days

Do not bury this rule in template markup.

## Alternatives Considered

| Category | Recommended | Alternative | Why Not |
|----------|-------------|-------------|---------|
| PHP runtime | PHP 8.4 | PHP 8.5 | 8.5 is supported, but 8.4 is the safer default for a greenfield prototype unless the local environment already runs 8.5 confidently. |
| CSS delivery | Local Bootstrap assets | Bootstrap CDN | CDN is fine technically, but local assets are more reliable for internal demos and offline-ish environments. |
| Export | CSV via `fputcsv()` | PhpSpreadsheet `.xlsx` | Too heavy for v1's flat export requirement. Adds dependency/tooling cost with little product value right now. |
| UI scripting | Vanilla JS | jQuery / SPA framework | Adds weight and patterns this prototype does not need. Bootstrap 5 itself no longer requires jQuery. |
| App architecture | Page-per-feature procedural PHP | Laravel / Symfony / custom MVC | Violates the spirit of the constraint and slows down delivery. |
| Dummy storage | Separated PHP array files | JSON blobs or hardcoded arrays inside pages | PHP arrays are easiest to require directly and keep consistent with procedural rendering. |

## Installation

### Baseline v1 (recommended)

No package manager is strictly required if you keep export as CSV and vendor Bootstrap assets locally.

```bash
# No mandatory installs for baseline v1
# Copy Bootstrap 5.3.8 CSS and JS into /assets/
```

### If true `.xlsx` becomes mandatory later

```bash
# Install Composer if not already available
# Then add PhpSpreadsheet only when needed
composer require phpoffice/phpspreadsheet
```

## What to Avoid in v1

### Avoid architectural overreach
- No framework
- No MVC rewrite
- No service container
- No repository pattern
- No OOP domain model just to look “enterprise”

This product is a prototype. The right architecture is the **smallest structure that stays tidy**.

### Avoid frontend overbuild
- No React/Vue/Angular
- No Vite/Webpack pipeline unless design requirements change drastically
- No animation library
- No charting library unless reports truly need charts

Bootstrap + CSS + tiny JS is enough.

### Avoid fake persistence complexity
- No SQLite “just in case”
- No JSON write-back layer unless the prototype explicitly needs editable saved state
- No admin CRUD for dummy data in v1

Static or semi-static arrays are the correct v1 choice.

### Avoid export mistakes
- Do not generate HTML tables and rename them `.xls`
- Do not mix page HTML with export headers
- Do not call `header()` after layout output
- Do not rely on default `fputcsv()` escaping behavior
- Do not introduce PhpSpreadsheet unless real `.xlsx` value exists

### Avoid maintainability traps
- Do not hardcode the same entitlement rule in dashboard, employee page, and export separately
- Do not store giant associative arrays inside page controllers
- Do not mix Bootstrap overrides into many inline `style` attributes

## Final Recommendation

For this exact project, build v1 with:

- **PHP 8.4.x**
- **Native procedural PHP pages + shared include files**
- **Bootstrap 5.3.8 local assets**
- **Custom CSS with a small token layer for enterprise-clean branding**
- **Vanilla JS only for minor UI interactions**
- **Separated local PHP array data files**
- **Pure calculation functions for leave logic**
- **CSV export via `fputcsv()` as the default Excel-compatible export**

That stack is the shortest path to a realistic, maintainable prototype without violating the project's explicit constraints.

## Sources

- PHP supported versions — https://www.php.net/supported-versions.php — **HIGH confidence**
- Bootstrap 5.3 introduction / current version docs — https://getbootstrap.com/docs/5.3/getting-started/introduction/ — **HIGH confidence**
- Composer introduction — https://getcomposer.org/doc/00-intro.md — **HIGH confidence**
- PHP `fputcsv()` manual — https://www.php.net/manual/en/function.fputcsv.php — **HIGH confidence**
- PHP `header()` manual — https://www.php.net/manual/en/function.header.php — **HIGH confidence**
- PhpSpreadsheet docs — https://phpspreadsheet.readthedocs.io/en/stable/ — **HIGH confidence**
- PhpSpreadsheet memory considerations — https://phpspreadsheet.readthedocs.io/en/stable/topics/memory_saving/ — **HIGH confidence**
