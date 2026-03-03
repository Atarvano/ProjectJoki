# Requirements: Sicuti HRD Cuti Tracker

**Defined:** 2026-03-03
**Core Value:** HR can quickly calculate and present employee leave entitlement from join year with a clear 8-year table and export-ready reporting.

## v1 Requirements

### Public Experience

- [x] **PUB-01**: User can open a landing page as the main entry point before login.
- [x] **PUB-02**: Landing page presents enterprise-clean branding using Bootstrap and custom native CSS.
- [x] **PUB-03**: Landing page clearly routes users to HR view and Employee view.

### Access Flow

- [x] **ACCS-01**: User can open a visual-only login page without backend authentication.
- [x] **ACCS-02**: HR and employee demo access flows are visually distinct and route to the correct dashboard.
- [x] **ACCS-03**: Application clearly indicates that v1 login is demo-only.

### HR Leave Calculator

- [x] **HRC-01**: HR can input employee join year and run leave entitlement calculation.
- [x] **HRC-02**: System shows an 8-year entitlement table derived from the join year.
- [x] **HRC-03**: Table shows per-row status for each year in the 8-year output.
- [x] **HRC-04**: Year 7 entitlement is fixed at 6 leave days.
- [x] **HRC-05**: Year 8 entitlement is fixed at 6 leave days.

### Employee Self View

- [ ] **EMP-01**: Employee can view their own leave entitlement result in a dedicated dashboard view.
- [ ] **EMP-02**: Employee view uses the same calculation rule output as the HR dashboard.

### Reports and Export

- [ ] **RPT-01**: HR can save calculation outputs into a leave report list using local PHP array-based dummy data.
- [ ] **RPT-02**: HR can view consolidated leave reports for all employees in a report table.
- [ ] **RPT-03**: HR can export all employee leave reports to an Excel-compatible file.

## v2 Requirements

### Backend and Data

- **DATA-01**: Application persists employees and leave reports in a database.
- **DATA-02**: Application supports CRUD operations for employee master data with durable storage.

### Authentication and Security

- **AUTH-01**: User can authenticate with real credential validation and role-based authorization.
- **AUTH-02**: User sessions are secured and managed server-side.

### Policy Expansion

- **POL-01**: HR can configure leave policy rules without editing code.
- **POL-02**: System supports policy variants beyond the initial 8-year output.

## Out of Scope

| Feature | Reason |
|---------|--------|
| Real database integration | Deferred to v2 to keep v1 frontend-first and fast to validate |
| Real authentication/security | v1 login is intentionally visual-only prototype |
| OOP/framework architecture | User constraint is native procedural PHP only |
| Multi-tenant/company setup | Not required for initial single-instance prototype |
| Advanced approvals/workflows | Focus stays on calculation + report + export core value |

## Traceability

| Requirement | Phase | Status |
|-------------|-------|--------|
| PUB-01 | Phase 1 | Complete |
| PUB-02 | Phase 1 | Complete |
| PUB-03 | Phase 1 | Complete |
| ACCS-01 | Phase 1 | Complete |
| ACCS-02 | Phase 1 | Complete |
| ACCS-03 | Phase 1 | Complete |
| HRC-01 | Phase 2 | Complete |
| HRC-02 | Phase 2 | Complete |
| HRC-03 | Phase 2 | Complete |
| HRC-04 | Phase 2 | Complete |
| HRC-05 | Phase 2 | Complete |
| EMP-01 | Phase 3 | Pending |
| EMP-02 | Phase 3 | Pending |
| RPT-01 | Phase 4 | Pending |
| RPT-02 | Phase 4 | Pending |
| RPT-03 | Phase 4 | Pending |

**Coverage:**
- v1 requirements: 16 total
- Mapped to phases: 16
- Unmapped: 0 ✓

---
*Requirements defined: 2026-03-03*
*Last updated: 2026-03-03 after roadmap mapping*
