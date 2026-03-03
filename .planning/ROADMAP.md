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
**Plans**: TBD

### Phase 4: Reports & Excel-Compatible Export
**Goal**: HR can persist demo reports in local arrays, review consolidated employee leave reports, and export all reports to an Excel-compatible file.
**Depends on**: Phase 2
**Requirements**: RPT-01, RPT-02, RPT-03
**Success Criteria** (what must be TRUE):
  1. HR can save leave calculation outputs into a report list backed by local PHP array dummy data.
  2. HR can view a consolidated report table containing leave report records across employees.
  3. HR can export all employee leave reports in one action to an Excel-compatible output file.
  4. Exported report columns and values are consistent with what HR sees in the consolidated report table.
**Plans**: TBD

## Progress

| Phase | Plans Complete | Status | Completed |
|-------|----------------|--------|-----------|
| 1. Foundation, Landing & Demo Access | 2/2 | Complete   | 2026-03-03 |
| 2. HR Entitlement Calculator | 1/1 | Complete | 2026-03-03 |
| 3. Employee Entitlement View | 0/TBD | Not started | - |
| 4. Reports & Excel-Compatible Export | 0/TBD | Not started | - |
