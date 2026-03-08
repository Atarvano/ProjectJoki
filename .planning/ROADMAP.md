# Roadmap: Sicuti HRD Cuti Tracker

## Milestones

- ✅ `v1.0` - Frontend demo milestone shipped 2026-03-04 (archive: `.planning/milestones/v1.0-ROADMAP.md`)
- ✅ `v2.0` - Backend Native PHP + HR-First Employee Onboarding shipped 2026-03-08 (archive: `.planning/milestones/v2.0-ROADMAP.md`)
- 🟡 `v3.0` - Beginner-Style PHP Rewrite & Structure Cleanup (in planning)

## Current Milestone

**Milestone:** `v3.0`
**Goal:** Rework the app into a tidier beginner-style native PHP CRUD codebase, replace calculator-first leave flow with direct detail views, and refresh product messaging so the app no longer feels like a leftover demo.
**Phase range:** 21-24
**Granularity:** Standard (default; no explicit override in `config.json`)

## Phases

- [x] **Phase 21: Beginner CRUD Structure Foundation** - Tidy the folder/page structure and lock a readable include contract for the rewrite. (completed 2026-03-08)
- [ ] **Phase 22: HR Detail-First CRUD Flow** - Make HR employee management revolve around direct CRUD pages and a useful employee detail screen.
- [ ] **Phase 23: Employee Leave View and Calculator Retirement** - Move employees onto direct self-view leave pages and fully remove calculator-first navigation.
- [ ] **Phase 24: Product Messaging and Final Copy Cleanup** - Refresh landing, dashboard, and report copy so the product story matches the rewritten app.

## Progress Table

| Phase | Plans Complete | Status | Completed |
|-------|----------------|--------|-----------|
| 21. Beginner CRUD Structure Foundation | 7/7 | Complete   | 2026-03-08 |
| 22. HR Detail-First CRUD Flow | 3/4 | In Progress |  |
| 23. Employee Leave View and Calculator Retirement | 0/0 | Not started | - |
| 24. Product Messaging and Final Copy Cleanup | 0/0 | Not started | - |

## Phase Details

### Phase 21: Beginner CRUD Structure Foundation
**Goal**: Developers and maintainers can follow a tidier beginner-style procedural PHP project structure without breaking shared page loading.
**Depends on**: Nothing (first phase)
**Requirements**: STRU-01, STRU-02, STRU-03
**Success Criteria** (what must be TRUE):
  1. Developers can locate shared includes, HR pages, employee pages, assets, and database files in obvious grouped folders without hunting through mixed locations.
  2. Developers can open the main CRUD screens and see beginner-readable file names that directly match page purpose rather than hidden abstraction layers.
  3. Protected HR and employee pages load successfully after the cleanup through one consistent include pattern that remains readable in-page.
**Plans**: 7 plans
Plans:
- [x] 21-00-PLAN.md — Create the Phase 21 smoke-test baseline and make older smoke checks resilient to route/include renames.
- [x] 21-01-PLAN.md — Regroup shared include files into beginner-readable auth/layout folders with compatibility shims.
- [x] 21-02-PLAN.md — Rename and split the main HR list/create/detail pages into English route, logic, and view files.
- [x] 21-03-PLAN.md — Finish the HR employee action-route renames for edit, delete, and provisioning with page-owned logic files.
- [x] 21-04-PLAN.md — Apply the same route/logic/view contract to the employee dashboard and lock employee-side include proof.
- [x] 21-05-PLAN.md — Rename the HR reports route and sweep shared navigation/tests onto the final English names.
- [x] 21-06-PLAN.md — Close the remaining grouped-include contract gap on legacy protected HR routes and enforce it in smoke coverage.

### Phase 22: HR Detail-First CRUD Flow
**Goal**: HR can manage employees through direct page-to-page CRUD and use employee detail pages as the primary leave-information destination.
**Depends on**: Phase 21
**Requirements**: CRUD-01, CRUD-02, CRUD-04, LEAV-01
**Success Criteria** (what must be TRUE):
  1. HR can move from the employee list into add, detail, edit, and delete actions through direct CRUD page links and return paths.
  2. HR can open an employee detail page and see profile information together with leave entitlement details on the same screen.
  3. HR can move from dashboard and report views into the relevant employee detail page instead of being pushed into a calculator-first flow.
  4. HR can review leave entitlement information directly from employee detail pages without needing a standalone calculator page for the normal workflow.
**Plans**: 4 plans
Plans:
- [x] 22-00-PLAN.md — Create the Phase 22 smoke-test scaffold for detail-first CRUD flow verification.
- [x] 22-01-PLAN.md — Expand the employee detail page into the main profile-plus-leave review screen.
- [x] 22-02-PLAN.md — Rewire create/edit/list CRUD rhythm so normal HR flow lands on employee detail pages.
- [ ] 22-03-PLAN.md — Shift dashboard/report/sidebar navigation toward employee detail review instead of calculator-first checking.

### Phase 23: Employee Leave View and Calculator Retirement
**Goal**: Employees can view their own leave entitlement directly from their authenticated area, and the calculator flow is fully removed once the replacement path exists.
**Depends on**: Phase 22
**Requirements**: CRUD-03, LEAV-02, LEAV-03, LEAV-04
**Success Criteria** (what must be TRUE):
  1. An authenticated employee can open their own page and see leave entitlement details tied to their session-linked account without entering a separate calculator flow.
  2. HR and employee users no longer see the calculator in main navigation or as the primary route for checking leave information.
  3. The standalone calculator page is removed only after the direct detail/self-view replacement path is available and working.
  4. Users follow a detail-first leave journey end-to-end without encountering dead calculator links or fallback demo-style routes.
**Plans**: TBD

### Phase 24: Product Messaging and Final Copy Cleanup
**Goal**: Visitors and authenticated users see product copy that matches the real HR-first leave management app and its new detail-first flow.
**Depends on**: Phase 23
**Requirements**: COPY-01, COPY-02, COPY-03
**Success Criteria** (what must be TRUE):
  1. Visitors no longer see demo-v1 wording or calculator-demo calls to action on the landing page.
  2. Visitors can understand from the landing page that the product is a real HR-first leave management app with HR-managed employee data and employee login access.
  3. HR sees dashboard and report wording that clearly matches the new detail-first leave flow and direct employee-detail navigation.
**Plans**: TBD

---
*Last updated: 2026-03-09 after Phase 22 Plan 01 completion*
