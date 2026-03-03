# Sicuti HRD Cuti Tracker

## What This Is

Sicuti HRD Cuti Tracker is a web application prototype for HR and employees to view leave entitlement flows with a realistic UI. In v1, the focus is frontend behavior using native procedural PHP pages, local dummy data, and no database. The product includes a landing page, visual login flow, HR dashboard for leave calculation, employee view, and leave report export to Excel.

## Core Value

HR can quickly calculate and present employee leave entitlement from join year with a clear 8-year table and export-ready reporting.

## Requirements

### Validated

- ✓ Build a primary landing page with enterprise-clean branding using Bootstrap plus native CSS. — Phase 1
- ✓ Provide visual-only login flow for HR and employee entry points without backend authentication. — Phase 1
- ✓ Enable HR to input employee join year and generate an 8-year leave entitlement table with per-row status. — Phase 2
- ✓ Show year-7 and year-8 entitlement as 6 leave days each in the calculation result. — Phase 2
- ✓ Provide employee-facing view to see their own leave entitlement output. — Phase 3

### Active

- [ ] Store and display leave reports using local PHP arrays (dummy data) and support export of all employee reports to Excel.

### Out of Scope

- Database integration - deferred to v2 to keep v1 fast and frontend-focused.
- Real authentication/session security - login is visual-only for v1 prototype scope.
- OOP/framework architecture for project code - implementation stays native procedural PHP. Packaged libraries (e.g., PhpSpreadsheet via Composer) are permitted where PHP lacks native capability.
- Leave policy rules beyond the 8-year output window - postponed until v2 policy expansion.

## Context

The project is starting greenfield in a PHP environment. User intent is a realistic demo website that looks production-like for internal HR usage while still running on dummy data. Workflow expectation is to prepare project planning artifacts now, then execute phase-by-phase with Phase 1 focused on setup and base UI.

## Constraints

- **Tech stack**: Native procedural PHP for project code. Third-party libraries via Composer are acceptable for capabilities PHP cannot handle natively (e.g., XLSX generation with PhpSpreadsheet). Composer autoloading is isolated to specific endpoints — not spread across the application.
- **Data layer**: No database in v1 - only local arrays and in-memory style demo data.
- **UI direction**: Enterprise clean - landing page is the primary visual entry and should not look generic.
- **Styling**: Bootstrap + native CSS - Bootstrap for baseline layout/components, custom CSS for identity.

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Build v1 as frontend-first prototype with dummy data | Validate UX flow quickly before backend investment | - Pending |
| Keep PHP implementation procedural only | Matches user constraint for both v1 and future v2 | - Pending |
| Scope calculator output to 8-year table | User requested clear, bounded entitlement view for initial release | - Pending |
| Phase 1 emphasizes setup, landing page, Bootstrap/native CSS foundation | User explicitly prioritized landing page and setup first | - Pending |
| Implemented deterministic engine in pure PHP function for testability | Ensures reuse in bulk processing later and independent testing | - Validated |
| Locked Year 7 and Year 8 to exactly 6 days per requirements | Hard constraint from HRC-04/HRC-05 | - Validated |
| Used 3-state UI (Empty, Error, Result) to guide user interaction | Improves UX by handling first load and invalid input explicitly | - Validated |
| Reused HR calculation engine for employee view | Guarantees data parity and avoids duplicate logic per EMP-02 | - Validated |
| Composer/PhpSpreadsheet kept for XLSX export | Pure PHP cannot produce branded multi-sheet XLSX; PhpSpreadsheet is isolated to hr/export.php only | - Validated |
---
*Last updated: 2026-03-04 after Phase 4.1*
