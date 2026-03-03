# Architecture Patterns

**Domain:** HR leave entitlement dashboard prototype (procedural PHP, no DB)
**Researched:** 2026-03-03

## Recommended Architecture

Use a **page-oriented modular monolith** in native procedural PHP:

- Each page has a thin controller section at top (read input, call domain functions, prepare view data).
- Shared UI shell is centralized in includes (`head`, `header/nav`, `footer`, flash messages).
- Domain logic is centralized in pure procedural functions (leave calculation, report shaping, status labeling).
- Data access is abstracted behind procedural repository functions that read/write local arrays/JSON files, so v2 DB swap is isolated.

This keeps v1 simple, while preventing logic duplication across HR dashboard, employee view, reports, and export.

## Page/Module Structure

```text
/public
  index.php                    # Landing page
  login.php                    # Visual login role picker
  hr-dashboard.php             # HR input + 8-year calculation table
  employee-view.php            # Employee entitlement display
  reports.php                  # List/search dummy reports
  export-reports.php           # CSV/XLS-compatible download endpoint

/src
  /includes
    bootstrap.php              # global require_once wiring
    head.php                   # <head> + CSS/Bootstrap includes
    layout-top.php             # opening body/container/nav
    layout-bottom.php          # closing layout/footer/scripts
    alerts.php                 # reusable status/error/info rendering

  /domain
    leave_rules.php            # entitlement year mapping, status rules
    leave_calculator.php       # generate 8-year rows from join year
    formatters.php             # consistent labels, date, numbers

  /repositories
    employee_repo.php          # read employee dummy array/file
    report_repo.php            # read/write report records

  /data
    employees.php              # seed arrays (or JSON files)
    reports.php                # initial persisted-like dataset
    reports.generated.json     # optional append-only prototype storage

  /exports
    csv_export.php             # stream export rows using fputcsv

/assets
  /css
    app.css                    # custom enterprise styling
```

## Component Boundaries

| Component | Responsibility | Communicates With |
|-----------|---------------|-------------------|
| Page controllers (`*.php`) | Accept GET/POST, invoke domain/repo functions, select template data | Includes, Domain, Repositories, Export service |
| Includes (`src/includes/*`) | Shared layout and repeated UI blocks | Page controllers |
| Domain (`src/domain/*`) | Leave entitlement logic and output shaping | Page controllers, Repositories |
| Repositories (`src/repositories/*`) | Read/write dummy data from arrays/files | Domain, Page controllers |
| Export service (`src/exports/csv_export.php`) | Build downloadable CSV stream for reports | Reports repo, PHP header/fputcsv |

## Shared Include Strategy (Procedural PHP)

Use one bootstrap include at the top of every page:

```php
<?php
require_once __DIR__ . '/src/includes/bootstrap.php';
```

Inside `bootstrap.php`:

- `require_once` all domain/repository/include helper files (prevents duplicate loads).
- Define app constants (`APP_ROOT`, `DATA_DIR`, `ASSET_BASE`) using `__DIR__`-based paths.
- Start lightweight request setup (timezone, optional output buffering for safer headers).

Page rendering convention:

1. Controller logic at top of page
2. `require` `head.php`
3. `require` `layout-top.php`
4. Page-specific HTML
5. `require` `layout-bottom.php`

Why: `header()` for export/redirect must run before output; centralized loading reduces header-sent mistakes and inconsistent paths.

## Data Flow

### Flow A: HR calculation + report creation

```text
HR Dashboard Form (join year, employee)
  -> controller validates input
  -> leave_calculator.php computes 8-year entitlement rows
  -> report_repo.php stores result into dummy report storage
  -> controller renders table + status badges
```

### Flow B: Employee view

```text
Employee view request (employee id)
  -> employee_repo fetches employee profile
  -> report_repo fetches latest entitlement result
  -> formatter/domain normalizes rows/status
  -> controller renders read-only entitlement table
```

### Flow C: Reports listing + export

```text
Reports page
  -> report_repo loads all dummy reports
  -> controller applies filter/search/sort in-memory
  -> render table + Export button

Export endpoint
  -> load filtered/all reports
  -> send download headers
  -> stream rows via fputcsv to php://output
  -> exit
```

## Dummy Data Organization

Recommended approach for v1:

1. **Seed arrays in PHP files** (`employees.php`, `reports.php`) for deterministic demo startup.
2. **Optional generated JSON** (`reports.generated.json`) when HR submits new calculations during demo.

Rules:

- Repositories own file format knowledge; pages never read files directly.
- Keep IDs stable (`EMP001`, `RPT0001`) so employee view and reports link reliably.
- Keep report shape explicit:

```php
[
  'report_id' => 'RPT0001',
  'employee_id' => 'EMP001',
  'employee_name' => '...',
  'join_year' => 2020,
  'generated_at' => '2026-03-03 09:15:00',
  'rows' => [
    ['year' => 1, 'entitlement_days' => 0, 'status' => 'Not eligible'],
    // ... up to year 8 (year 7 & 8 = 6 days)
  ]
]
```

## Export Flow (Excel-Compatible in v1)

For prototype scope, implement **CSV download that opens in Excel**:

1. Endpoint: `export-reports.php` (no HTML output)
2. Set headers first:
   - `Content-Type: text/csv; charset=UTF-8`
   - `Content-Disposition: attachment; filename="leave-reports-YYYYMMDD.csv"`
3. Open `php://output`
4. Write UTF-8 BOM once (improves Excel UTF-8 handling)
5. Write header row and each report row with `fputcsv($stream, $fields, ',', '"', '')`
6. `exit;`

Important guardrails:

- No whitespace/output before headers.
- Keep export logic in dedicated service file (not embedded in reports page template).
- Explicit empty escape argument for `fputcsv` to avoid future default-change issues and improve RFC-style compatibility.

## Patterns to Follow

### Pattern 1: Thin page controller, fat procedural domain
**What:** Keep decision/business logic in `src/domain`, not mixed through template markup.
**When:** All pages with calculation/report logic.
**Example:**

```php
$input = hr_dashboard_input($_POST);
$errors = validate_join_year($input['join_year']);

if (!$errors) {
    $rows = calculate_leave_table((int)$input['join_year']);
    save_report($input['employee_id'], $rows);
}
```

### Pattern 2: Repository boundary for file/array data
**What:** One access layer for dummy data now, DB adapter later.
**When:** Employee lookup, report list/save, export source queries.

### Pattern 3: Shared layout includes
**What:** One source for page shell and nav to keep visual consistency.
**When:** All non-export pages.

## Anti-Patterns to Avoid

### Anti-Pattern 1: Duplicating leave rules in multiple pages
**What:** Hardcoding entitlement in HR dashboard, employee page, and report export separately.
**Why bad:** Drift/bugs (especially year 7 and 8 = 6 days rule).
**Instead:** Single `calculate_leave_table()` in domain module.

### Anti-Pattern 2: Mixing HTML output with export endpoint logic
**What:** Reusing reports page and conditionally echoing CSV.
**Why bad:** Breaks headers/download, causes corrupted output.
**Instead:** Separate `export-reports.php` endpoint with zero template output.

### Anti-Pattern 3: Direct file reads/writes from pages
**What:** `file_get_contents` inside each page controller.
**Why bad:** Path bugs, inconsistent formats, hard v2 migration.
**Instead:** Repository functions + constants from bootstrap.

## Scalability Considerations (Prototype-Realistic)

| Concern | At 100 users | At 10K users | At 1M users |
|---------|--------------|--------------|-------------|
| Data storage | Arrays/files acceptable | File contention and memory load become brittle | Requires DB + indexing (v2) |
| Leave computation | On-request compute is trivial | Still cheap; add memoization for repeated joins | Move to service/domain with caching |
| Export | Full in-memory CSV okay | Stream row-by-row; avoid loading all rows | Async/background export jobs + object storage |
| Routing | Flat pages manageable | Add front controller/router map | Move toward framework/service boundaries |

## Build-Order Implications (Roadmap Guidance)

Recommended implementation order for this exact prototype:

1. **Foundation shell first**
   - Bootstrap includes + `app.css` + shared layout partials
   - Why first: all pages depend on this; prevents redesign churn

2. **Domain + repository core second**
   - `leave_calculator`, `leave_rules`, `employee_repo`, `report_repo`
   - Why second: calculation/report logic reused by 3+ pages and export

3. **Landing + visual login third**
   - Fast visible progress; low logic risk

4. **HR dashboard fourth**
   - First integration point of input validation + calculation + persistence

5. **Employee view fifth**
   - Reuse existing report/domain modules, minimal new logic

6. **Reports + export sixth**
   - Depends on saved report records and stable report schema

7. **Polish and hardening last**
   - Error banners, empty states, responsive adjustments, export filename filters

Dependency summary:

```text
Includes/Foundation -> Domain/Repo -> HR Dashboard -> Employee View -> Reports -> Export
Landing/Login can be built in parallel after Foundation.
```

## Confidence Notes

- **HIGH:** Procedural include/loading patterns, header ordering constraints, and CSV writing behavior based on PHP manual.
- **MEDIUM:** Excel UTF-8 BOM practical compatibility guidance (widely used practice; partly from community notes).
- **HIGH:** Build-order/dependency recommendations for this project scope, derived from PROJECT.md constraints.

## Sources

- Project requirements/context: `.planning/PROJECT.md`
- PHP `require_once` manual: https://www.php.net/manual/en/function.require-once.php (accessed 2026-03-03)
- PHP `header` manual: https://www.php.net/manual/en/function.header.php (accessed 2026-03-03)
- PHP `fputcsv` manual: https://www.php.net/manual/en/function.fputcsv.php (accessed 2026-03-03)
- PHP `file_put_contents` manual: https://www.php.net/manual/en/function.file-put-contents.php (accessed 2026-03-03)
- Bootstrap 5.3 Grid docs: https://getbootstrap.com/docs/5.3/layout/grid/ (v5.3.8 page)
