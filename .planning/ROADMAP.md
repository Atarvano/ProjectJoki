# Roadmap: Sicuti HRD Cuti Tracker

## Overview

This roadmap delivers a frontend-first v1 demo in native procedural PHP, starting with shared foundation and a high-quality landing experience, then shipping the core HR entitlement workflow, employee visibility, and finally dummy-data reporting with Excel-compatible export.

## Phases

**Phase Numbering:**
- Integer phases (1, 2, 3): Planned milestone work
- Decimal phases (2.1, 2.2): Urgent insertions (marked with INSERTED)

- [x] **Phase 1: Foundation, Landing & Demo Access** - Establish project/UI foundation and deliver the top-priority landing plus visual-only role-based login entry. (completed 2026-03-03)
- [x] **Phase 2: HR Entitlement Calculator** - Deliver deterministic 8-year leave calculation flow for HR from join year input. (completed 2026-03-03)
- [ ] **Phase 3: Employee Entitlement View** - Deliver employee self-view using the same entitlement output rules as HR.
- [ ] **Phase 4: Reports & Excel-Compatible Export** - Deliver local-array report storage, consolidated reporting table, and full export of all employee reports.
- [x] **Phase 12: Gap Closure - Reports Verification & Parity** - Close report blockers by aligning list/export data behavior and completing pending report verification evidence. (completed 2026-03-04)
- [ ] **Phase 13: Gap Closure - Traceability & Navigation Consistency** - Close remaining traceability partials and shared navigation/include consistency debt.

## Phase Details

### Phase 1: Foundation, Landing & Demo Access
**Goal**: Users can enter the product through an enterprise-clean landing page and reach the correct demo role flow from a clearly demo-only visual login experience.
**Depends on**: Nothing (first phase)
**Requirements**: PUB-01, PUB-02, PUB-03, ACCS-01, ACCS-02, ACCS-03
**Success Criteria** (what must be TRUE):
  1. User can open a landing page as the primary entry point before any login interaction.
  2. Landing page visibly reflects enterprise-clean branding using Bootstrap plus custom native CSS.
  3. User can clearly choose HR or Employee route from the landing/login flow and land in the correct destination view.
  4. Login screens and flow visibly communicate that v1 access is demo-only (not real authentication).
**Plans**: 2 plans
- [x] 01-01-PLAN.md — Shared UI foundation (includes, CSS design system) + landing page
- [ ] 01-02-PLAN.md — Mock login with role toggle + HR/Employee dashboard destinations

### Phase 2: HR Entitlement Calculator
**Goal**: HR can generate and review a complete, rule-correct 8-year leave entitlement output from employee join year input.
**Depends on**: Phase 1
**Requirements**: HRC-01, HRC-02, HRC-03, HRC-04, HRC-05
**Success Criteria** (what must be TRUE):
  1. HR can submit an employee join year and trigger entitlement calculation from the dashboard.
  2. System displays a full 8-year entitlement table derived from the provided join year.
  3. Each row in the 8-year output shows a visible status label/badge for that year.
  4. Year 7 and Year 8 rows always display fixed entitlement values of 6 leave days each.
**Plans**: 1 plan
Plans:
- [x] 02-01-PLAN.md — Calculator engine + dashboard form/table + visual verification

### Phase 3: Employee Entitlement View
**Goal**: Employees can view their own entitlement result in a dedicated dashboard that stays consistent with HR calculation outputs.
**Depends on**: Phase 2
**Requirements**: EMP-01, EMP-02
**Success Criteria** (what must be TRUE):
  1. Employee can open a dedicated self-view dashboard for entitlement results.
  2. For the same input data, employee entitlement output matches the same calculation rules shown in HR view.
  3. Employee-facing result presents the 8-year entitlement output in a readable, verification-friendly format.
**Plans**: 1 plan
Plans:
- [ ] 03-01-PLAN.md — Employee entitlement dashboard with preset profiles, 3-state UI, and shared calculator engine

### Phase 4: Reports & Excel-Compatible Export
**Goal**: HR can persist demo reports in local arrays, review consolidated employee leave reports, and export all reports to an Excel-compatible file.
**Depends on**: Phase 2
**Requirements**: RPT-01, RPT-02, RPT-03
**Success Criteria** (what must be TRUE):
  1. HR can save leave calculation outputs into a report list backed by local PHP array dummy data.
  2. HR can view a consolidated report table containing leave report records across employees.
  3. HR can export all employee leave reports in one action to an Excel-compatible output file.
  4. Exported report columns and values are consistent with what HR sees in the consolidated report table.
**Plans**: 2 plans
Plans:
- [ ] 04-01-PLAN.md — Report data layer, save flow, consolidated table, filters, modals, stat cards
- [ ] 04-02-PLAN.md — XLSX export endpoint with branded multi-sheet output + visual verification

## Progress

| Phase | Plans Complete | Status | Completed |
|-------|----------------|--------|-----------|
| 1. Foundation, Landing & Demo Access | 2/2 | Complete   | 2026-03-03 |
| 2. HR Entitlement Calculator | 1/1 | Complete | 2026-03-03 |
| 3. Employee Entitlement View | 0/1 | Planned | - |
| 4. Reports & Excel-Compatible Export | 0/TBD | Not started | - |
| 12. Gap Closure - Reports Verification & Parity | 1/1 | Complete   | 2026-03-04 |
| 13. Gap Closure - Traceability & Navigation Consistency | 0/1 | Planned | - |

### Phase 04.1: ini kenapa malah ada composer sih ??? (INSERTED)

**Goal:** Resolve Composer/PhpSpreadsheet repo hygiene — fix .gitignore, track composer.lock, and document the justified dependency exception in PROJECT.md.
**Depends on:** Phase 4
**Requirements:** RPT-03
**Plans:** 1/1 plans complete

Plans:
- [ ] 04.1-01-PLAN.md — Git hygiene (.gitignore, composer.lock) + document Composer exception in PROJECT.md + annotate export.php

### Phase 5: redesign landing page

**Goal:** Landing page has elevated visual polish with gradient mesh backgrounds, inline SVG illustrations, ambient animations, gradient-accented cards, bolder typography, and new narrative content sections — delivering a comprehensive showcase of all v1 features.
**Depends on:** Phase 4
**Requirements:** LANDING-REDESIGN
**Plans:** 2 plans

Plans:
- [ ] 05-01-PLAN.md — CSS design system extension + hero section redesign with inline SVG illustration
- [ ] 05-02-PLAN.md — Remaining sections (Benefits, How It Works, Stats, Demo Info, CTA) with gradient cards and SVGs + visual verification

### Phase 6: redesign login page

**Goal:** Login page has elevated visual polish with a split-panel layout (40% form / 60% illustration), co-branding, character-based SVG illustration, role-colored CTA, value bullets, and responsive mobile collapse — extending Phase 5 design language in a calmer login-appropriate tone.
**Depends on:** Phase 5
**Requirements:** ACCS-01, ACCS-02, ACCS-03
**Plans:** 1 plan

Plans:
- [ ] 06-01-PLAN.md — Split-panel layout with co-branding, character SVG illustration, CSS section, and visual verification

### Phase 7: redesign dashboard hr dan karyawan

**Goal:** Both HR and Employee dashboards have an enterprise-grade shell with collapsible sidebar, topbar with breadcrumb/profile/demo cues, and elevated visual polish — HR splits into 3 pages (overview, calculator, reports) while Employee stays single-page — all existing calculator, report, and export behavior preserved unchanged.
**Depends on:** Phase 6
**Requirements:** ACCS-02, ACCS-03, HRC-01, HRC-02, HRC-03, HRC-04, HRC-05, EMP-01, EMP-02, RPT-01, RPT-02, RPT-03
**Plans:** 3 plans

Plans:
- [ ] 07-01-PLAN.md — Shared dashboard shell (sidebar + topbar + layout includes) and dashboard-scoped CSS design system extension
- [ ] 07-02-PLAN.md — HR dashboard split into overview, kalkulator, and laporan pages with preserved business logic
- [ ] 07-03-PLAN.md — Employee dashboard rebuild inside shared shell with single-page UX preserved

### Phase 8: 9 redesign/improve svg login page

**Goal:** Login page illustration system is upgraded with richer, role-distinct human-style SVG scenes and calm layered right-panel atmosphere, while preserving demo-only access behavior, role routing, and mobile form-first collapse.
**Depends on:** Phase 7
**Requirements:** ACCS-01, ACCS-02, ACCS-03
**Plans:** 1 plan

Plans:
- [ ] 08-01-PLAN.md — Upgrade role-conditional SVG scenes and login panel atmosphere with behavior-preserving visual QA

### Phase 9: redesign/improve svg login page

**Goal:** [To be planned]
**Depends on:** Phase 8
**Plans:** 0 plans

Plans:
- [ ] TBD (run /gsd:plan-phase 9 to break down)

### Phase 10: redesign/improve svg login page

**Goal:** [To be planned]
**Depends on:** Phase 9
**Plans:** 0 plans

Plans:
- [ ] TBD (run /gsd:plan-phase 10 to break down)

### Phase 11: redesign/improve svg login page

**Goal:** [To be planned]
**Depends on:** Phase 10
**Plans:** 0 plans

Plans:
- [ ] TBD (run /gsd:plan-phase 11 to break down)

### Phase 12: Gap Closure - Reports Verification & Parity

**Goal:** Close v1 report blockers by making laporan/export behavior consistent and completing pending verification evidence so report requirements can be marked satisfied.
**Depends on:** Phase 4, Phase 04.1
**Requirements:** RPT-01, RPT-02, RPT-03
**Gap Closure:** Closes audit gaps for report requirement blockers, list/export integration mismatch, and broken list-to-export parity flow.
**Plans:** 1/1 plans complete

Plans:
- [ ] 12-01-PLAN.md — Fix export/laporan data parity (initReports→getReports), empty-state UX (disabled export, accurate reset copy), and 6-moment verification evidence

### Phase 13: Gap Closure - Traceability & Navigation Consistency

**Goal:** Close remaining audit partials by restoring full EMP traceability evidence and aligning shared navigation/include wiring.
**Depends on:** Phase 12
**Requirements:** EMP-01, EMP-02
**Gap Closure:** Closes audit gaps for EMP traceability partials and shared navigation/include consistency debt.
**Plans:** 1 plan

Plans:
- [ ] 13-01-PLAN.md — Add missing requirements-completed metadata for Phase 3 summary, update footer HR route links, align login shared include usage, and re-verify wiring
