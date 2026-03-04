# Project Retrospective

*A living document updated after each milestone. Lessons feed forward into future planning.*

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
| v1.0 | 16 | 11 shipped + 3 deferred planning slots | Added explicit gap-closure phases for parity and traceability before completion |

### Cumulative Quality

| Milestone | Tests | Coverage | Zero-Dep Additions |
|-----------|-------|----------|-------------------|
| v1.0 | verification artifacts + manual UI checks | 16/16 milestone requirements satisfied | N/A (used isolated PhpSpreadsheet dependency for XLSX only) |

### Top Lessons (Verified Across Milestones)

1. Requirement traceability quality depends on disciplined metadata hygiene in each phase summary.
2. Shared data access paths across list/export surfaces prevent integration regressions.
