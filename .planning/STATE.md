---
gsd_state_version: 1.0
milestone: v3.0
milestone_name: beginner-style-php-rewrite-and-structure-cleanup
status: in_progress
last_updated: "2026-03-08T19:00:12.053Z"
last_activity: 2026-03-08 - Completed Phase 21 Plan 04 employee dashboard split and employee include smoke proof
progress:
  total_phases: 4
  completed_phases: 0
  total_plans: 6
  completed_plans: 3
  percent: 50
---

# State: Sicuti HRD Cuti Tracker

## Project Reference

**Core Value:** HR creates employee data first, provisions login credentials, then employees log in with native PHP sessions to view their own leave data through an enforced, real backend flow.
**Current Focus:** Executing Phase 21 beginner CRUD structure foundation plans with grouped shared includes and beginner-readable route cleanup.
**Roadmap:** See `.planning/ROADMAP.md` and `.planning/PROJECT.md`

## Current Position

**Phase:** 21 - Beginner CRUD Structure Foundation
**Plan:** 04
**Status:** Plan 04 complete
**Progress:** [█████-----] 50%
**Last activity:** 2026-03-08 - Completed Phase 21 Plan 04 employee dashboard split and employee include smoke proof

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
| Phase 21 P04 | 1 min | 2 tasks | 5 files |

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
- None.

## Session Continuity

### Last Session
- **Date:** 2026-03-08
- **Activity:** Executed Phase 21 Plan 04 and committed the employee dashboard route/logic/view split plus updated employee include smoke proof.
- **Outcome:** The employee dashboard now matches the grouped auth -> db -> logic -> layout recipe used by protected pages, and smoke tests assert the final employee-side include contract.
- **Next:** Execute `21-05-PLAN.md`.

### Context for Next Session
- Phase 21 now has grouped `includes/auth/` and `includes/layout/` folders plus employee dashboard route -> logic -> view files, so the remaining structure plans can finish the visible route cleanup on the same contract.
- Public pages already use grouped layout includes directly, and employee protected pages now use the final grouped route recipe too.
- Phase 22 should deliver the HR detail-first CRUD flow before calculator retirement is considered complete.
- Phase 23 should move employee leave viewing to self-view pages and finish calculator removal.
- Phase 24 should refresh landing/dashboard/report copy so the app no longer reads like demo-v1.

---
*State initialized: 2026-03-05*
*Last updated: 2026-03-08 after Phase 21 Plan 04 completion*
