---
phase: 18-data-wiring-calculator-reports-dashboards
plan: 02
subsystem: api
tags: [php, mysqli, calculator, hr, employee-data]
requires:
  - phase: 17-account-provisioning
    provides: employee records, provisioned account status, employee detail workflow
provides:
  - DB-backed HR calculator with employee dropdown and GET-based employee context
  - Employee detail shortcut into calculator with selected karyawan context
  - Calculator smoke checks updated for Phase 18 employee-first behavior
affects: [reports, dashboards, phase-18]
tech-stack:
  added: []
  patterns: [procedural PHP page-level DB query, GET-based employee context, derived join-year passed into unchanged leave engine]
key-files:
  created: []
  modified: [hr/kalkulator.php, hr/karyawan-detail.php, tests/phase18_data_wiring_smoke.php]
key-decisions:
  - "Calculator now uses GET karyawan_id selection so HR can refresh and bookmark a selected employee context."
  - "Join year is derived from karyawan.tanggal_bergabung and passed into hitungHakCuti() without changing the cuti engine."
  - "Employee detail is the main HR entry point into leave entitlement via a simple query-string link."
patterns-established:
  - "Employee-first calculator pages should load DB identity first, then derive leave values from stored dates."
  - "Phase 18 smoke checks should assert final rewired markers once a plan replaces the old demo/session flow."
requirements-completed: [CALC-01, CALC-02, CALC-04]
duration: 2 min
completed: 2026-03-06
---

# Phase 18 Plan 02: Employee-first HR Calculator Wiring Summary

**DB-backed HR calculator with employee selection, derived join-year leave results, and employee-detail entry into the same calculator context**

## Performance

- **Duration:** 2 min
- **Started:** 2026-03-06T10:19:29Z
- **Completed:** 2026-03-06T10:22:23Z
- **Tasks:** 2
- **Files modified:** 3

## Accomplishments
- Replaced the old manual year/save-report calculator flow with a real karyawan dropdown sourced from the database.
- Kept the existing leave engine unchanged by deriving `$tahun_bergabung` from `tanggal_bergabung` before calling `hitungHakCuti()`.
- Added a direct "Lihat Hak Cuti" action on employee detail so HR can open leave entitlement from the employee record.

## Task Commits

Each task was committed atomically:

1. **Task 1: Replace manual calculator inputs with DB-backed employee context** - `99f4b30` (feat)
2. **Task 2: Anchor calculator access from employee detail** - `330937f` (feat)

**Plan metadata:** pending

## Files Created/Modified
- `hr/kalkulator.php` - Rewritten as an employee-first calculator page with DB dropdown, prepared statement lookup, empty state, and leave table driven by stored join date.
- `hr/karyawan-detail.php` - Adds a simple HR action button linking directly to the calculator with the current employee preselected.
- `tests/phase18_data_wiring_smoke.php` - Updates the calculator smoke group to validate the new rewired behavior instead of the removed demo/session flow.

## Decisions Made
- Used `GET` with `karyawan_id` as the primary calculator input so selected employee context is easy to refresh, share internally, and bookmark.
- Preserved the stable calculator engine by deriving the year from the employee row instead of changing `includes/cuti-calculator.php`.
- Kept the employee detail page layout intact and added the calculator action near provisioning controls so employee records remain the HR source of truth.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 3 - Blocking] Updated stale calculator smoke expectations after rewiring**
- **Found during:** Task 1 (Replace manual calculator inputs with DB-backed employee context)
- **Issue:** The Phase 18 calculator smoke group still asserted old baseline markers (`tahun_bergabung` input and `save_report` flow), so verification failed after the planned rewiring removed those markers.
- **Fix:** Replaced the smoke assertions with final-state checks for `karyawan_id`, `tanggal_bergabung`, engine usage, and removal of manual-year/save-report behavior.
- **Files modified:** `tests/phase18_data_wiring_smoke.php`
- **Verification:** `php tests/phase18_data_wiring_smoke.php --group=calculator`
- **Committed in:** `99f4b30` (part of Task 1 commit)

---

**Total deviations:** 1 auto-fixed (1 blocking)
**Impact on plan:** Verification contract was aligned with the intended rewired calculator behavior. No scope creep.

## Issues Encountered
None.

## User Setup Required
None - no external service configuration required.

## Next Phase Readiness
- Calculator flow is now anchored to real employee records and ready for the reports/export rewiring in Plan 18-03.
- Employee-first linking is in place, so later Phase 18 pages can continue treating the employee record as the source of truth.

---
*Phase: 18-data-wiring-calculator-reports-dashboards*
*Completed: 2026-03-06*

## Self-Check: PASSED
- Found summary file on disk.
- Verified task commits `99f4b30` and `330937f` exist in repository history.
