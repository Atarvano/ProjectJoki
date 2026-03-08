# Project Retrospective

*A living document updated after each milestone. Lessons feed forward into future planning.*

## Milestone: v2.0 — Backend Native PHP + HR-First Employee Onboarding

**Shipped:** 2026-03-08
**Phases:** 7 | **Plans:** 21 | **Sessions:** 21

### What Was Built
- Native procedural PHP + MySQL-backed employee, user, and auth foundation replaced the frontend-only v1 demo flow.
- HR can now create, edit, inspect, delete, and provision employee accounts through live CRUD pages and one-time credential handoff.
- Calculator, reports, export, and both dashboards now run from live database state instead of demo/session fixtures.

### What Worked
- Sequential dependency planning from DB -> auth -> CRUD -> provisioning -> data wiring kept implementation straightforward.
- Shared planning artifacts and per-plan summaries made milestone traceability, rollout stats, and archive creation fast.
- Reusing the v1 leave engine limited business-logic churn while enabling a meaningful backend milestone.

### What Was Inefficient
- Validation and Nyquist artifacts drifted behind the shipped implementation and turned the final audit into `tech_debt` instead of `passed`.
- Some milestone closure evidence had to be reconciled late across audit, validation, and summary files after behavior was already working.

### Patterns Established
- For procedural PHP milestones, one shared connection contract and one shared auth guard are the right integration spine.
- Browser-proof closure can be centralized in one canonical validation artifact, then propagated outward to audits and older verification docs.

### Key Lessons
1. Keep validation-document state synchronized as each phase closes, not only at milestone end.
2. Late audit cleanup is much cheaper when each summary already captures requirement coverage, tasks, and final runtime evidence clearly.

### Cost Observations
- Model mix: still dominated by lightweight implementation and documentation passes rather than heavy research.
- Sessions: 21 plan summaries shipped for v2.0.
- Notable: backend delivery was efficient, but closure quality suffered when verification docs lagged behind real implementation progress.

---

## Milestone: v1.0 — milestone

**Shipped:** 2026-03-04
**Phases:** 11 shipped phases | **Plans:** 16 | **Sessions:** 16

### What Was Built
- Frontend-first HR/employee leave entitlement demo with enterprise landing, shared login, and role-specific dashboards.
- Deterministic 8-year calculator engine reused across HR and employee views for parity.
- Session-backed report save/list/detail flow plus Excel-compatible export using isolated PhpSpreadsheet integration.

### What Worked
- Phase-by-phase summaries and verification artifacts kept implementation and requirement mapping auditable.
- Shared include and engine reuse patterns reduced duplication and made gap-closure work fast.

### What Was Inefficient
- Traceability closure needed a late cleanup phase due to missing summary metadata linkage.
- Export/report parity required follow-up gap-closure after initial implementation.

### Patterns Established
- Requirement closure should always maintain a three-source chain: REQUIREMENTS traceability, VERIFICATION evidence table, SUMMARY frontmatter metadata.
- Report list/export flows should use a single non-mutating data accessor (`getReports()`) to avoid parity drift.

### Key Lessons
1. Keep requirements-completed metadata accurate in summaries during initial phase execution to avoid late audit debt.
2. Treat UX parity checks (empty-state behavior, redirect feedback, export/list consistency) as first-class acceptance checks, not post-phase fixes.

### Cost Observations
- Model mix: dominated by sonnet-class executions in this repository workflow.
- Sessions: 16 plan summaries shipped for v1.
- Notable: milestone moved quickly from MVP to visual polish, but generated closure debt that required two dedicated gap phases.

---

## Cross-Milestone Trends

### Process Evolution

| Milestone | Sessions | Phases | Key Change |
|-----------|----------|--------|------------|
| v2.0 | 21 | 7 | Shifted from frontend demo work to dependency-driven backend delivery with milestone archival discipline |
| v1.0 | 16 | 11 shipped + 3 deferred planning slots | Added explicit gap-closure phases for parity and traceability before completion |

### Cumulative Quality

| Milestone | Tests | Coverage | Zero-Dep Additions |
|-----------|-------|----------|-------------------|
| v2.0 | verification artifacts + browser walkthrough + smoke checks | 36/36 milestone requirements satisfied | Core backend delivered with native PHP/MySQLi and no framework adoption |
| v1.0 | verification artifacts + manual UI checks | 16/16 milestone requirements satisfied | N/A (used isolated PhpSpreadsheet dependency for XLSX only) |

### Top Lessons (Verified Across Milestones)

1. Requirement traceability quality depends on disciplined metadata hygiene in each phase summary.
2. Shared data access paths across list/export surfaces prevent integration regressions.
3. Milestone audits stay cheap only when validation docs are updated continuously alongside shipped behavior.
