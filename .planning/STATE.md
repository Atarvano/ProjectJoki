---
gsd_state_version: 1.0
milestone: v3.0
milestone_name: beginner-style-php-rewrite-and-structure-cleanup
status: in_progress
last_updated: "2026-03-08T21:10:28Z"
last_activity: 2026-03-09 - Completed Phase 22 Plan 02 detail-first CRUD rhythm update
progress:
  total_phases: 4
  completed_phases: 1
  total_plans: 11
  completed_plans: 10
  percent: 91
---

# State: Sicuti HRD Cuti Tracker

## Project Reference

**Core Value:** HR creates employee data first, provisions login credentials, then employees log in with native PHP sessions to view their own leave data through an enforced, real backend flow.
**Current Focus:** Phase 22 is underway; next work should finish the last dashboard/report/sidebar shift toward employee-detail review.
**Roadmap:** See `.planning/ROADMAP.md` and `.planning/PROJECT.md`

## Current Position

**Phase:** 22 - HR Detail-First CRUD Flow
**Plan:** 03 next
**Status:** Plans 00 and 02 complete in this phase slice; Plan 03 remains for the dashboard/report/sidebar navigation shift
**Progress:** [███████░░░] 75%
**Last activity:** 2026-03-09 - Completed Phase 22 Plan 02 detail-first CRUD rhythm update

## Performance Metrics

| Metric | Value |
|--------|-------|
| Milestone | v3.0 |
| Roadmap phases | 4 |
| Completed phases | 0/4 |
| v3.0 requirements mapped | 14/14 |
| Current streak | - |
| Phase 21 P00 | 16 min | 2 tasks | 3 files |
| Phase 21 P01 | 8 min | 2 tasks | 15 files |
| Phase 21 P02 | 9 min | 2 tasks | 18 files |
| Phase 21 P04 | 1 min | 2 tasks | 5 files |
| Phase 21 P03 | 10 min | 2 tasks | 14 files |
| Phase 21 P05 | 1 min | 2 tasks | 9 files |
| Phase 21 P06 | 2 min | 2 tasks | 4 files |
| Phase 22 P00 | 20 min | 2 tasks | 5 files |
| Phase 22 P01 | 10 min | 2 tasks | 2 files |
| Phase 22 P02 | 1 min | 2 tasks | 4 files |

## Accumulated Context

### Key Decisions
| Decision | Rationale | Phase |
|----------|-----------|-------|
| Start v3.0 numbering at Phase 21 | Continues directly after shipped v2.0 phase range (14-20) recorded in milestone history | Roadmap |
| Use 4 phases for v3.0 | Structure, HR detail-first CRUD, calculator retirement/self-view, and copy cleanup are the natural delivery boundaries from current milestone requirements | Roadmap |
| Keep rewrite aligned to `thexdev/php-native-crud` style | User wants beginner-readable procedural PHP CRUD rather than a framework or OOP cleanup | Roadmap |
| Preserve security baseline while simplifying page style | Beginner-style rewrite must still keep session auth, `password_hash()` / `password_verify()`, and prepared statements for user input | Roadmap |
| Remove calculator flow only after replacement exists | Prevents dead-end leave journeys while shifting users to employee detail and self-view pages | Roadmap |
| Keep scope out of workflow/approval/framework expansion | v3.0 is a rewrite-and-cleanup milestone, not a new business workflow milestone | Roadmap |
| Use one plain PHP CLI smoke scaffold with folders, names, includes, and bridges groups | Later Phase 21 plans need cheap structure proof without adding test framework complexity | Phase 21 |
| Prefer final target paths in smoke assertions while tolerating temporary bridge files | Rename rollout spans multiple plans, so tests should point to final structure without going red too early | Phase 21 |
| Group shared includes into `includes/auth` and `includes/layout` while keeping flat filenames as bridge shims | Gives the codebase an obvious final shared-file home without breaking older paths during the rollout | Phase 21 |
| Point public pages directly at `includes/layout/...` paths as soon as the grouped files exist | Makes the new folder structure the visible main contract immediately instead of leaving it hidden behind shims | Phase 21 |
| Keep the employee dashboard on the same visible auth -> db -> logic -> layout recipe as HR pages | Makes both protected areas easy to scan and keeps the include contract recognizable for beginners | Phase 21 |
| Keep leave calculation inside the employee page-owned logic file so the route stays short and beginner-readable | Preserves a shallow split without introducing shared helper backends or hiding the dashboard data flow | Phase 21 |
| Use visible English HR route names immediately while keeping tiny redirect bridge files for old Indonesian paths during rollout | Makes the final beginner-readable route names visible from the `hr/` tree right away without breaking older links during the rename rollout | Phase 21 |
| Keep each protected HR page thin and move SQL/form/query work into page-owned logic files with matching views | Preserves the Absensi-style beginner CRUD feel without introducing shared backend abstraction layers | Phase 21 |
| Use reports.php as the final visible HR report route while keeping laporan.php only as a redirect bridge during rollout | Lets shared links move to the final English report name immediately without breaking older entry points during the rename sweep | Phase 21 |
| Keep report SQL and cuti calculation in hr/logic/reports.php so the new route stays thin and beginner-readable | Extends the same beginner route -> logic -> view pattern to the reports screen without hiding logic in shared handlers | Phase 21 |
| Keep edit, delete, and provision on the same visible auth -> db -> logic -> layout/action recipe as the other HR employee pages | Completes the beginner-readable route family without hiding request handling in shared controllers or helpers | Phase 21 |
| Use tiny bridge files for old Indonesian action routes so rollout safety stays simple and beginner-readable | Preserves older entry points temporarily while making the final English route names visible immediately | Phase 21 |
| Make the final grouped include paths visible in the last untouched HR protected routes instead of leaving bridge shims as the readable contract | Closes the final STRU-03 gap and makes the grouped include recipe the visible standard everywhere it matters | Phase 21 |
| Lock the remaining HR routes into the includes smoke group with both positive and negative path assertions so shim regressions go red immediately | Prevents a quiet fallback to temporary shim paths after Phase 21 is declared complete | Phase 21 |
| Keep Phase 22 verification as one cheap procedural PHP smoke file with named groups instead of adding PHPUnit or framework tooling | Matches the beginner-style PHP codebase and gives later rewiring plans one fast reusable command | Phase 22 |
| Assert final detail-first strings directly in source files so unfinished rewiring fails loudly before page behavior changes land | Makes the smoke check useful immediately as a red safety net instead of a placeholder test | Phase 22 |
| Use employee detail as the normal landing page after create and edit success so HR stays in one employee review flow | Keeps HR inside one employee review path instead of bouncing back to the list after every save | Phase 22 |
| Keep employees.php as the visible CRUD hub with literal Detail, Edit, Delete, and Provision actions instead of hidden row interactions | Matches the beginner-style Absensi-like flow and keeps the list easy to trace line by line | Phase 22 |

### Implementation Guardrails
- Native procedural PHP only (no OOP/framework)
- MySQLi via single `koneksi.php`
- Beginner-style direct CRUD pages with simple includes
- Prepared statements for user input queries
- Native PHP sessions for auth/role guard
- HR-first onboarding remains unchanged
- Remove calculator page/flow fully after replacement exists
- Remove demo-v1 references from landing and related copy
- Keep folder structure tidy and easy to read

### TODOs
- [x] Plan Phase 21.
- [ ] Execute the remaining beginner-style procedural rewrite plans in phases 21-24.
- [ ] Validate calculator removal, detail-first leave flow, and copy cleanup before milestone closure.

### Blockers
- `gsd-tools state advance-plan`, `state add-decision`, and `requirements mark-complete` did not apply because current planner files no longer match the tool parser assumptions; metadata was updated manually for this plan.

## Session Continuity

### Last Session
- **Date:** 2026-03-09
- **Activity:** Executed Phase 22 Plan 02 and committed the detail-first CRUD rhythm updates.
- **Outcome:** Successful create/edit now land on `employee-detail.php`, employees.php explains the list -> detail review flow in plain beginner-style copy, and the grouped `crud-flow` smoke check passes.
- **Next:** Execute Phase 22 Plan 03.

### Context for Next Session
- Phase 21 already left the codebase on grouped `includes/auth/` and `includes/layout/` folders, English HR CRUD routes, and the beginner-readable route -> logic -> view split.
- `hr/logic/employee-detail.php` and `hr/views/employee-detail.php` now support the detail-first review screen, while `employee-create.php` and `employee-edit.php` both return HR to that page after save.
- `hr/views/employees.php` now acts as the obvious beginner CRUD hub with visible Detail, Edit, Delete, and Provision actions plus helper copy pointing HR to the detail page.
- `tests/phase22_hr_detail_first_crud_smoke.php --group=crud-flow` is green and should stay green while Plan 03 shifts dashboard/report/sidebar emphasis.
- Plan 03 should focus on dashboard, reports, and sidebar wording/links so employee detail becomes the primary leave-review path outside the list page too.

---
*State initialized: 2026-03-05*
*Last updated: 2026-03-09 after Phase 22 Plan 02 completion was recorded*
