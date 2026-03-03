# Project Research Summary

**Project:** Sicuti HRD Cuti Tracker
**Domain:** Internal HR leave entitlement dashboard prototype (procedural PHP, frontend-first demo)
**Researched:** 2026-03-03
**Confidence:** HIGH

## Executive Summary

Sicuti HRD Cuti Tracker should be delivered as a **procedural PHP v1 demo** optimized for speed, clarity, and stakeholder trust: a polished landing page first, visual login (explicitly demo-only), an HR leave calculator that renders a deterministic 8-year entitlement table, an employee self-view, and a report list with Excel-compatible export. Research strongly converges on a **server-rendered modular monolith** (page controllers + shared includes + pure domain functions + array-backed repositories), not framework/OOP architecture.

The recommended implementation approach is to keep logic centralized and reusable: one canonical leave calculation module, one canonical report-shaping schema, and one dedicated CSV export endpoint (`fputcsv`) that matches on-screen tables. This gives the fastest path to a realistic enterprise-clean prototype while preserving a clean seam for v2 migration (DB/auth/policy expansion) without rewriting core flows.

The biggest risks are not technical complexity but **misinterpretation and drift**: stakeholders mistaking visual login for real security, users reading simplified policy as legally complete, and export values diverging from UI values. Mitigation must be built into v1: persistent prototype disclaimers, explicit policy scope/version labeling, and strict UI/export parity from shared transformers.

## Key Findings

### Recommended Stack

Research consensus: keep stack intentionally small and deterministic for a frontend demo with dummy data.

**Core technologies:**
- **PHP 8.4.x**: runtime baseline — stable modern default for procedural greenfield prototype work.
- **Native procedural PHP**: page controllers, includes, and function modules — aligns with hard project constraint (no framework/OOP in v1).
- **Bootstrap 5.3.8 (local assets)**: rapid enterprise-clean UI scaffolding with offline/internal demo reliability.
- **Custom native CSS layer**: visual identity and consistency (tokens for color/type/spacing) beyond generic Bootstrap defaults.
- **Vanilla JS (minimal)**: only for lightweight UI interactions (role toggle behavior, filters, small UX affordances).
- **Local PHP array data files**: deterministic dummy employee/report data with zero DB overhead.
- **CSV export via `fputcsv()`**: default Excel-compatible export for v1; defer PhpSpreadsheet until true `.xlsx` requirements exist.

**Critical version/implementation notes:**
- Prefer PHP **8.4.x** unless environment already standardizes on 8.5.
- Use Bootstrap **5.3.8** and store CSS/JS locally.
- In `fputcsv`, pass the escape argument explicitly (avoid deprecated/default ambiguity).

### Expected Features

Feature research aligns tightly with PROJECT.md scope.

**Must have (table stakes) for v1:**
- Landing page with enterprise-clean branding and clear product positioning.
- Visual login split (HR vs Employee) with explicit demo-mode language.
- HR calculator input (employee + join year).
- Deterministic **8-year entitlement table**.
- Explicit rule enforcement: **year 7 = 6 days, year 8 = 6 days**.
- Per-row status labels/badges.
- Employee self-view (read-only entitlement visibility).
- Report list from dummy local data.
- One-click Excel-compatible export of reports.

**Should have (high-value differentiators, if time permits in v1.1):**
- Entitlement explanation panel (why each row/status appears).
- Visual status legend and stronger parity cues.
- Scenario preview mode (quick join-year what-if recalculation).

**Defer (v2+):**
- Real auth/authorization/password flows.
- DB-backed persistence and integrations.
- Broader leave-policy engine (carry-over, prorating, multi-jurisdiction complexity).
- Full request/approval workflow and analytics dashboards.

### Architecture Approach

Use a **page-oriented modular monolith**: thin page controllers orchestrate; shared includes render shell; domain functions own all entitlement/report logic; repositories isolate array/file data access; export logic stays in a dedicated endpoint/service with zero template output.

**Major components:**
1. **Page controllers** — input handling, validation, orchestration, and view data assembly.
2. **Domain modules** — leave rules, 8-year calculation, status labeling, canonical table/export shaping.
3. **Repositories** — employee/report read-write abstraction over local arrays/files.
4. **Shared includes/layout** — consistent shell, navigation, alerts, and design-tokenized UI.
5. **Export module/endpoint** — CSV stream generation aligned to canonical report schema.

### Critical Pitfalls

1. **Policy logic presented as final legal truth** — Prevent with visible policy scope/version + explicit assumptions and boundary tests.
2. **Visual login mistaken for secure auth** — Prevent with persistent demo-mode banners/copy and simple explicit placeholder guards.
3. **UI/export mismatch** — Prevent by reusing one canonical transformer + shared column schema for table and export.
4. **UI inconsistency across landing/dashboard/reports** — Prevent with early design tokens and shared reusable UI partials.
5. **Procedural sprawl blocking v2 migration** — Prevent with strict file boundaries (domain/reporting/demo-auth/repo) and stable array contracts.

## Implications for Roadmap

Based on cross-file dependencies, the roadmap should be phase-ordered around **shared foundation first, reusable logic second, user flows third, export parity last**.

### Phase 1: Foundation + Landing + Visual Login
**Rationale:** Landing page is explicit priority; all later pages depend on shared UI shell and copy conventions.
**Delivers:** Bootstrap/local assets wiring, design tokens, shared includes, enterprise landing page, demo-only visual login split.
**Addresses:** Landing page, visual login table stakes.
**Avoids:** Fake-auth confusion, inconsistent UI baseline, over-polished compliance language.

### Phase 2: Entitlement Engine + HR Calculator
**Rationale:** Core product value is calculation logic; downstream employee/reports/export all depend on this.
**Delivers:** Canonical calculator functions, 8-year deterministic output, year-7/8 = 6 rule, status logic, HR calculator page.
**Addresses:** Core table stakes around calculation and policy rule visibility.
**Avoids:** Duplicated policy logic, date boundary errors, policy-scope ambiguity.

### Phase 3: Employee View + Report Repository + Report List
**Rationale:** Reuses phase-2 outputs while introducing persistent demo artifacts (dummy reports) needed for export.
**Delivers:** Employee read-only entitlement view, report data shape/contracts, report listing/filter shell.
**Addresses:** Employee transparency + report history table stakes.
**Avoids:** Array-key drift, procedural sprawl, inconsistent cross-page interpretation.

### Phase 4: Export Parity + QA Hardening
**Rationale:** Export should ship only after report schema stabilizes to guarantee UI/export parity.
**Delivers:** CSV export endpoint, parity-locked column schema, filename policy versioning, regression checks, responsive/error polish.
**Addresses:** Excel export table stakes and trust-critical reliability.
**Avoids:** Header/output corruption, column drift, non-traceable exports.

### Phase Ordering Rationale

- Foundation before features prevents redesign churn and UI inconsistency.
- Shared domain/repository modules before additional pages prevent duplicated rules and drift.
- Export last ensures it reflects final report schema and avoids trust-damaging mismatches.

### Research Flags

Phases likely needing deeper `/gsd-research-phase` during planning:
- **Phase 2 (calculator logic):** date boundary fixtures and policy wording/disclaimer precision for HR-safe interpretation.
- **Phase 4 (export hardening):** Excel compatibility nuances (UTF-8/BOM/date formatting parity) if stakeholders push beyond plain CSV.

Phases with standard patterns (can likely skip deeper research):
- **Phase 1 (foundation/landing/login):** Bootstrap + procedural include patterns are well-documented and low-risk.
- **Phase 3 (employee/report list basics):** straightforward reuse of established modules and array-backed repositories.

## Confidence Assessment

| Area | Confidence | Notes |
|------|------------|-------|
| Stack | HIGH | Strong alignment with project constraints; based primarily on official PHP/Bootstrap docs and direct prototype fit. |
| Features | MEDIUM | Core scope is high-confidence from PROJECT.md; some broader “market expectation” references are vendor-content and lower confidence. |
| Architecture | HIGH | Pattern recommendations are consistent with procedural PHP constraints and clear dependency flow. |
| Pitfalls | MEDIUM-HIGH | Key risks are credible and actionable; policy-specific legal examples are jurisdiction-dependent and need local validation. |

**Overall confidence:** HIGH

### Gaps to Address

- **Policy/legal scope specificity:** Current rule set is intentionally simplified; add explicit “not legal/compliance complete” wording in requirements and UI copy.
- **Status taxonomy finalization:** Confirm exact status vocabulary (`Eligible`, `Not Yet Eligible`, `Policy Capped`, etc.) before implementation lock.
- **Export format expectation:** Validate early whether CSV is sufficient or stakeholder expects true `.xlsx` formatting.
- **Dummy data contract freeze:** Lock key names and report shape before Phase 3 to avoid downstream drift.

## Sources

### Primary (HIGH confidence)
- `.planning/PROJECT.md` — product scope, constraints, priorities.
- PHP documentation (`supported versions`, `header`, `fputcsv`, `require_once`, arrays/datetime docs).
- Bootstrap 5.3 documentation.
- PhpSpreadsheet official docs (used for v2/`.xlsx` fallback guidance).

### Secondary (MEDIUM confidence)
- OWASP Authentication Cheat Sheet (prototype-auth communication risk framing).
- GOV.UK / ACAS leave guidance (pitfall framing and entitlement communication boundaries).

### Tertiary (LOW confidence)
- Vendor blogs/checklists cited in FEATURES.md (useful directional context, not authoritative for core scope decisions).

---
*Research completed: 2026-03-03*
*Ready for roadmap: yes*
