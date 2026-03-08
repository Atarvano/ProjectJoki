# Requirements: Sicuti HRD Cuti Tracker

**Defined:** 2026-03-09
**Core Value:** HR creates employee data first, provisions login credentials, then employees log in with native PHP sessions to view their own leave data through an enforced, real backend flow.

## v1 Requirements

Requirements for milestone v3.0. Each maps to exactly one roadmap phase.

### Structure

- [x] **STRU-01**: Admin can navigate a tidier beginner-style project structure with clearly grouped folders for shared includes, HR pages, employee pages, assets, and database files.
- [x] **STRU-02**: Developer can follow explicit beginner-readable page naming for the main CRUD screens without framework-style abstraction layers.
- [x] **STRU-03**: Protected pages can load shared includes through a consistent include contract that stays readable and survives the folder cleanup.

### CRUD and Detail Flow

- [x] **CRUD-01**: HR can move through employee list, add, detail, edit, and delete pages using direct page-to-page CRUD navigation.
- [x] **CRUD-02**: HR can open an employee detail page that shows both employee profile data and leave entitlement details in one screen.
- [x] **CRUD-03**: Employee can open their own page and see direct leave entitlement details from their session-linked account.
- [x] **CRUD-04**: HR can move from dashboard and report screens into employee detail pages instead of a calculator-first flow.

### Leave View Replacement

- [x] **LEAV-01**: HR can view leave entitlement details directly from employee detail pages without using a standalone calculator page.
- [x] **LEAV-02**: Employee can view their leave entitlement directly from their own authenticated page without using a standalone calculator page.
- [x] **LEAV-03**: Users no longer see the calculator as part of the main navigation or primary action path.
- [x] **LEAV-04**: The standalone calculator page is removed after the detail-based replacement flow is in place.

### Product Messaging

- [ ] **COPY-01**: Visitor no longer sees demo-v1 wording or calls to action on the landing page.
- [ ] **COPY-02**: Visitor sees landing-page messaging that reflects the real HR-first leave management app rather than a calculator demo.
- [ ] **COPY-03**: HR sees dashboard and report wording that matches the new detail-first leave flow.

## v2 Requirements

Deferred to a later milestone.

### Workflow Expansion

- **FLOW-01**: HR can manage leave request and approval workflows.
- **FLOW-02**: Employee can submit leave requests directly in the app.

### Broader Cleanup

- **ARCH-01**: Developer can refactor the app into a more reusable or framework-like architecture.
- **ARCH-02**: Developer can add deeper UI modernization beyond the milestone's copy and flow cleanup.

## Out of Scope

Explicitly excluded for milestone v3.0.

| Feature | Reason |
|---------|--------|
| Laravel / OOP / MVC refactor | Conflicts with the milestone goal of keeping the rewrite beginner-style and procedural |
| Public employee signup | Conflicts with the HR-first provisioning model |
| Leave request / approval workflow | New business workflow expansion, not part of this rewrite milestone |
| Keeping calculator as a main feature | The milestone explicitly replaces calculator-first flow with detail-first leave views |
| Heavy frontend tooling or SPA rewrite | Adds complexity that does not fit the simple native PHP CRUD direction |

## Traceability

Which phases cover which requirements. Updated during roadmap creation.

| Requirement | Phase | Status |
|-------------|-------|--------|
| STRU-01 | Phase 21 | Complete |
| STRU-02 | Phase 21 | Complete |
| STRU-03 | Phase 21 | Complete |
| CRUD-01 | Phase 22 | Complete |
| CRUD-02 | Phase 22 | Complete |
| CRUD-03 | Phase 23 | Complete |
| CRUD-04 | Phase 22 | Complete |
| LEAV-01 | Phase 22 | Complete |
| LEAV-02 | Phase 23 | Complete |
| LEAV-03 | Phase 23 | Complete |
| LEAV-04 | Phase 23 | Complete |
| COPY-01 | Phase 24 | Pending |
| COPY-02 | Phase 24 | Pending |
| COPY-03 | Phase 24 | Pending |

**Coverage:**
- v1 requirements: 14 total
- Mapped to phases: 14
- Unmapped: 0 ✓

---
*Requirements defined: 2026-03-09*
*Last updated: 2026-03-09 after Phase 22 Plan 03 completion*
