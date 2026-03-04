# Sicuti HRD Cuti Tracker

## What This Is

Sicuti HRD Cuti Tracker is a web application prototype for HR and employees to view leave entitlement flows with a realistic UI. In v1, the focus is frontend behavior using native procedural PHP pages, local dummy data, and no database. The product includes a landing page, visual login flow, HR dashboard for leave calculation, employee view, and leave report export to Excel.

## Core Value

HR can quickly calculate and present employee leave entitlement from join year with a clear 8-year table and export-ready reporting.

## Current State

Shipped **v1.0 milestone** on 2026-03-04. The product now includes:
- Public landing and demo-only access flow for HR/Employee.
- Deterministic HR leave calculator with 8-year output and enforced Year-7/8 rules.
- Employee entitlement self-view that reuses the same calculation engine for parity.
- Session-backed reporting and Excel-compatible export.
- Gap-closure improvements for report parity, traceability, and shared navigation consistency.

## Next Milestone Goals

- Define post-v1 requirement set (new `REQUIREMENTS.md`) with explicit milestone boundary.
- Decide whether remaining UI redesign phases (9-11) are still in-scope, re-scoped, or replaced.
- Close accepted tech debt: seeded-report dead path, demo-route guard strategy, and routing clarity hardening.

## Requirements

### Validated

- ✓ Build a primary landing page with enterprise-clean branding using Bootstrap plus native CSS. — Phase 1
- ✓ Provide visual-only login flow for HR and employee entry points without backend authentication. — Phase 1
- ✓ Enable HR to input employee join year and generate an 8-year leave entitlement table with per-row status. — Phase 2
- ✓ Show year-7 and year-8 entitlement as 6 leave days each in the calculation result. — Phase 2
- ✓ Provide employee-facing view to see their own leave entitlement output. — Phase 3
- ✓ Store and display leave reports using local PHP arrays (dummy data) and support export of all employee reports to Excel. — Phase 4/12

### Active

- [ ] Define next milestone requirement set (v1.1+), including decision on redesign continuation and debt payoff priorities.

### Out of Scope

- Database integration - deferred to v2 to keep v1 fast and frontend-focused.
- Real authentication/session security - login is visual-only for v1 prototype scope.
- OOP/framework architecture for project code - implementation stays native procedural PHP. Packaged libraries (e.g., PhpSpreadsheet via Composer) are permitted where PHP lacks native capability.
- Leave policy rules beyond the 8-year output window - postponed until v2 policy expansion.

## Context

v1.0 is now shipped as a realistic demo website for internal HR usage. The codebase remains native procedural PHP with dummy/session data for runtime behavior and isolated Composer usage for XLSX generation. Milestone audit reports all v1 requirements satisfied with non-blocking tech debt tracked for next milestone planning.

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
| Use getReports() for list/export parity and guard empty export with flash redirect | Prevents reseed mismatch and broken export UX after reset | - Validated |
| Keep EMP closure metadata in Phase 3 summary and link it from Phase 13 verification | Restores auditable three-source requirement closure chain | - Validated |
---
*Last updated: 2026-03-04 after v1.0 milestone completion*
