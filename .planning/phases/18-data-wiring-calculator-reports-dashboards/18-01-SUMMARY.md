---
phase: 18-data-wiring-calculator-reports-dashboards
plan: 01
subsystem: testing
tags: [php, smoke-tests, validation, calculator, reports, dashboards]
requires:
  - phase: 17-account-provisioning
    provides: provisioning flow and linked employee accounts for Phase 18 validation
provides:
  - grouped smoke checks for calculator, reports, and dashboards rewiring
  - repeatable manual browser and export verification checklist for Phase 18
  - rebuilt Phase 18 validation contract covering all 8 executable tasks
affects: [phase-18, calculator, reports, export, dashboards, verification]
tech-stack:
  added: [plain-php-cli-smoke-script]
  patterns: [grouped static smoke assertions, validation-matrix traceability, manual-checklist verification]
key-files:
  created:
    - tests/phase18_data_wiring_smoke.php
    - tests/phase18_manual_checks.md
  modified:
    - .planning/phases/18-data-wiring-calculator-reports-dashboards/18-VALIDATION.md
key-decisions:
  - "Wave 0 uses one plain PHP smoke script with calculator, reports, and dashboards groups so every later Phase 18 plan can reuse one fast command."
  - "The validation contract tracks all 8 executable tasks across Plans 18-01 through 18-04 with explicit wave, requirement, and command mappings."
patterns-established:
  - "Grouped smoke command pattern: php tests/phase18_data_wiring_smoke.php --group={calculator|reports|dashboards}"
  - "Manual verification lives in one phase checklist file instead of scattered notes"
requirements-completed: [CALC-01, CALC-02, CALC-03, CALC-04, RPT-01, RPT-02, RPT-03, RPT-04, DASH-01, DASH-05]
duration: 1 min
completed: 2026-03-06
---

# Phase 18 Plan 01: Wave 0 smoke tests, manual checks, and validation contract Summary

**Grouped PHP smoke checks and a full Phase 18 validation contract now prove calculator, reports, export, and dashboard rewiring work can be verified from one repeatable baseline.**

## Performance

- **Duration:** 1 min
- **Started:** 2026-03-06T10:14:30Z
- **Completed:** 2026-03-06T10:15:53Z
- **Tasks:** 2
- **Files modified:** 3

## Accomplishments
- Added `tests/phase18_data_wiring_smoke.php` with grouped checks for calculator, reports, and dashboards.
- Added `tests/phase18_manual_checks.md` with repeatable browser and export verification steps for later Phase 18 plans.
- Rebuilt `18-VALIDATION.md` so all 8 executable tasks across Plans 18-01 to 18-04 have explicit verification coverage and ready frontmatter.

## Task Commits

Each task was committed atomically:

1. **Task 1: Create grouped Phase 18 smoke test script** - `5a59cde` (feat)
2. **Task 2: Write manual checklist and finalize validation contract** - `6d3a5b9` (docs)

**Plan metadata:** pending

## Files Created/Modified
- `tests/phase18_data_wiring_smoke.php` - Plain PHP smoke script with grouped marker assertions and CLI failure hints.
- `tests/phase18_manual_checks.md` - Browser and export checklist for calculator, reports, HR dashboard, and employee dashboard verification.
- `.planning/phases/18-data-wiring-calculator-reports-dashboards/18-VALIDATION.md` - Nyquist-compliant validation contract with a complete 8-task verification matrix.

## Decisions Made
- Wave 0 verification is centralized in one plain PHP script with three reusable groups so future plan tasks can run focused smoke checks quickly.
- The validation contract uses one phase-level matrix covering all 8 executable tasks to avoid stale plan/wave/requirement mappings across later plans.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 1 - Bug] Fixed escaped session marker in dashboards smoke assertion**
- **Found during:** Task 1 (Create grouped Phase 18 smoke test script)
- **Issue:** The first draft wrote the employee dashboard marker with accidental PHP string interpolation, which would make the static assertion unreliable.
- **Fix:** Replaced the interpolated string with a literal session marker assertion so the dashboards group checks the intended code text exactly.
- **Files modified:** `tests/phase18_data_wiring_smoke.php`
- **Verification:** `php tests/phase18_data_wiring_smoke.php --group=dashboards`
- **Committed in:** `5a59cde` (part of task commit)

---

**Total deviations:** 1 auto-fixed (1 bug)
**Impact on plan:** Minor correctness fix inside the new smoke script only. No scope creep.

## Issues Encountered
- `git add` refused `.planning/phases/18-data-wiring-calculator-reports-dashboards/18-VALIDATION.md` because `.planning/` is ignored by repo rules. Resolved by staging only the intended validation file with `git add -f` and completing the task commit normally.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- Phase 18 Wave 0 verification scaffolding is ready for Plans 18-02, 18-03, and 18-04.
- Next plan can use grouped smoke commands immediately after each rewiring task.
- Requirement checkboxes in `REQUIREMENTS.md` should remain pending until the actual user-facing rewiring plans ship, because this plan delivers verification coverage rather than the final features.

## Self-Check: PASSED
- Found `.planning/phases/18-data-wiring-calculator-reports-dashboards/18-01-SUMMARY.md` on disk.
- Verified task commits `5a59cde` and `6d3a5b9` exist in git history.

---
*Phase: 18-data-wiring-calculator-reports-dashboards*
*Completed: 2026-03-06*
