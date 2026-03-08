---
phase: 21-beginner-crud-structure-foundation
plan: 01
subsystem: infra
tags: [php, mysqli, includes, layout, auth, smoke-test]
requires:
  - phase: 21-00
    provides: Phase 21 structure verification baseline and rename-safe smoke testing direction
provides:
  - Grouped shared include files under includes/auth and includes/layout
  - Flat include bridge shims that keep old include filenames readable during transition
  - Public pages wired directly to grouped layout include paths
  - Phase 21 structure smoke test coverage for folder regroup and include rewiring
affects: [phase-21, hr-pages, employee-pages, shared-includes]
tech-stack:
  added: []
  patterns: [Beginner-readable grouped includes, direct require_once bridge shims, manual include paths]
key-files:
  created: [includes/auth/auth-guard.php, includes/layout/dashboard-layout.php, includes/layout/header.php, includes/layout/footer.php, includes/layout/dashboard-sidebar.php, includes/layout/dashboard-topbar.php, tests/phase21_structure_smoke.php]
  modified: [includes/auth-guard.php, includes/dashboard-layout.php, includes/header.php, includes/footer.php, includes/dashboard-sidebar.php, includes/dashboard-topbar.php, index.php, login.php]
key-decisions:
  - "Use includes/auth and includes/layout as the final shared-file home, while keeping flat filenames as tiny require_once shims."
  - "Point index.php and login.php straight at includes/layout paths so grouped files become the visible main contract immediately."
patterns-established:
  - "Grouped shared include pattern: auth files live in includes/auth and shared page shell files live in includes/layout."
  - "Compatibility shim pattern: old flat include files stay as one-line require_once bridges during the transition."
requirements-completed: [STRU-01, STRU-03]
duration: 8 min
completed: 2026-03-08
---

# Phase 21 Plan 01: Shared Include Regroup Summary

**Grouped auth and layout includes with readable bridge shims and direct public-page rewiring to the final layout paths.**

## Performance

- **Duration:** 8 min
- **Started:** 2026-03-08T18:43:00Z
- **Completed:** 2026-03-08T18:51:37Z
- **Tasks:** 2
- **Files modified:** 15

## Accomplishments
- Moved the real shared auth and layout implementations into `includes/auth/` and `includes/layout/`.
- Replaced the old flat include filenames with tiny beginner-readable bridge shims instead of leaving duplicate implementations behind.
- Updated `index.php` and `login.php` to use the grouped layout paths directly, and added a dedicated Phase 21 smoke script to verify the regroup.

## Task Commits

Each task was committed atomically:

1. **Task 1: Create grouped include folders and leave bridge shims behind** - `d1077e6` (feat)
2. **Task 2: Point shared and public pages at the grouped include locations** - `3ea3744` (feat)

**Plan metadata:** `dc6eee3` (docs)

## Files Created/Modified
- `includes/auth/auth-guard.php` - Final grouped auth guard implementation.
- `includes/layout/dashboard-layout.php` - Final grouped protected-page shell.
- `includes/layout/header.php` - Final grouped public/shared header include.
- `includes/layout/footer.php` - Final grouped public/shared footer include.
- `includes/layout/dashboard-sidebar.php` - Final grouped sidebar partial.
- `includes/layout/dashboard-topbar.php` - Final grouped topbar partial.
- `includes/auth-guard.php` - Tiny compatibility shim pointing to `includes/auth/auth-guard.php`.
- `includes/dashboard-layout.php` - Tiny compatibility shim pointing to `includes/layout/dashboard-layout.php`.
- `includes/header.php` - Tiny compatibility shim pointing to `includes/layout/header.php`.
- `includes/footer.php` - Tiny compatibility shim pointing to `includes/layout/footer.php`.
- `includes/dashboard-sidebar.php` - Tiny compatibility shim pointing to `includes/layout/dashboard-sidebar.php`.
- `includes/dashboard-topbar.php` - Tiny compatibility shim pointing to `includes/layout/dashboard-topbar.php`.
- `index.php` - Public landing page now includes grouped layout files directly.
- `login.php` - Login page now includes grouped layout files directly.
- `tests/phase21_structure_smoke.php` - Verifies grouped folders, bridge shims, and direct include rewiring.

## Decisions Made
- Used `includes/auth/` and `includes/layout/` as the permanent shared include folders because they are obvious to beginners and match the locked phase direction.
- Kept old flat include filenames as one-line shims so later route rewiring can continue safely without duplicate logic copies.
- Added one dedicated smoke script for this phase because the plan’s required verification file was missing and the regroup needed a cheap repeatable check.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 3 - Blocking] Added the missing Phase 21 structure smoke test**
- **Found during:** Task 1 (Create grouped include folders and leave bridge shims behind)
- **Issue:** The plan required `php tests/phase21_structure_smoke.php`, but that file did not exist yet, so task verification could not run.
- **Fix:** Created `tests/phase21_structure_smoke.php` with `folders` and `includes` groups to verify grouped include placement, bridge shims, and direct public-page include paths.
- **Files modified:** `tests/phase21_structure_smoke.php`
- **Verification:** `php tests/phase21_structure_smoke.php --group=folders` and `php tests/phase21_structure_smoke.php --group=includes`
- **Committed in:** `d1077e6` (part of task commit)

---

**Total deviations:** 1 auto-fixed (1 blocking)
**Impact on plan:** The auto-fix was required so the planned verification could actually run. No scope creep beyond making the plan executable.

## Issues Encountered
- The required smoke script path in the plan was missing from the repository. This was resolved by adding the minimal structure smoke test needed for Phase 21 verification.

## User Setup Required
None - no external service configuration required.

## Next Phase Readiness
- The final grouped include structure is now in place for the remaining Phase 21 route renames.
- Later plans can keep using the grouped include paths directly while old flat include filenames still resolve through bridge shims.
- No blockers found for `21-02-PLAN.md`.

---
*Phase: 21-beginner-crud-structure-foundation*
*Completed: 2026-03-08*

## Self-Check: PASSED
- FOUND: `.planning/phases/21-beginner-crud-structure-foundation/21-01-SUMMARY.md`
- FOUND: task commits `d1077e6` and `3ea3744` in git history
