# Feature Landscape

**Domain:** Internal HR leave entitlement dashboard prototype (frontend demo)
**Researched:** 2026-03-03

## Table Stakes

Features users expect. Missing = product feels incomplete.

| Feature | Why Expected | Complexity | Notes |
|---------|--------------|------------|-------|
| Landing page with clear product purpose | Internal users still expect a clear entry point and professional framing | Low | Must look enterprise-clean (Bootstrap + custom CSS), not generic template-only |
| Visual login split (HR vs Employee) | Users expect role-based entry, even in demo form | Low | Visual-only in v1 (no real auth/session hardening) |
| HR calculator input: employee + join year | Core HR task starts with identifying employee and service start year | Low | Keep form simple and deterministic for demo reliability |
| 8-year entitlement result table | Central business output; without it, product has no core value | Medium | Must always show 8 rows based on join year window |
| Rule enforcement: year 7 = 6 days, year 8 = 6 days | This is explicit product policy and must be visible and testable | Low | Hard-code this rule in v1 calculator logic to avoid ambiguity |
| Per-row status in entitlement table | HR needs quick interpretation, not just raw numbers | Medium | Recommended statuses: `Eligible`, `Not Yet Eligible`, `Policy Capped` (for years 7/8 = 6) |
| Employee self-view of entitlement | Employees expect transparency into their own leave profile | Low | Mirror HR output with read-only framing |
| Report list (dummy data) | HR expects reviewable history/list, not one-off calculations | Medium | Use local PHP arrays as source-of-truth in v1 |
| Excel export of all reports | Reporting/export is a standard expectation in HR workflows | Medium | Single-click export from report list; include all visible columns |

## Differentiators

Features that set product apart. Not always expected in minimal internal tools, but highly valuable.

| Feature | Value Proposition | Complexity | Notes |
|---------|-------------------|------------|-------|
| Transparent entitlement explanation panel | Builds trust by showing how each row is derived from join year + policy | Low | Simple side panel: formula + policy notes for years 7/8 |
| Visual status emphasis (badge colors + legends) | Faster HR scanning and fewer interpretation mistakes during demos | Low | Keep colors accessible and pair with text labels |
| Scenario preview mode (change join year quickly) | Useful for HR discussion during policy conversations and demos | Medium | No persistence needed; purely in-page recalculation |
| Export-ready formatting parity with on-screen table | Reduces “what I saw is not what I exported” confusion | Medium | Align column names/order/status text between UI and Excel |
| Simple role-focused UI shells (HR workspace vs employee card view) | Makes prototype feel productized rather than a single generic page | Low | Shared components, different emphasis per role |

## Anti-Features

Features to explicitly NOT build in v1.

| Anti-Feature | Why Avoid | What to Do Instead |
|--------------|-----------|-------------------|
| Real authentication, authorization, password flows | Out of scope; adds backend/security complexity that does not validate core UX | Keep visual-only login routing and clear “prototype” labeling |
| Database schema + persistence layer | Violates v1 constraint and slows delivery | Use local arrays/procedural data providers with seeded dummy records |
| Complex leave policy engine (carry-over, pro-rating, multi-country law variants) | High risk of scope explosion; not required for current validation | Keep fixed 8-year output + explicit year-7/year-8 rule |
| Full leave request/approval workflow | Different product problem (transaction workflow) than entitlement visibility prototype | Focus only on entitlement calculation + report listing/export |
| Advanced analytics dashboards (trends, forecasting, burnout scoring) | Not needed for first validation loop; low ROI for demo stage | Provide clear tabular outputs and export only |
| API integrations (payroll/HRIS/calendar) | Integration overhead without v1 validation benefit | Use static dummy data and local exports |

## Feature Dependencies

```text
Visual Login Split → Role-specific Pages (HR Dashboard, Employee Self-View)
Join Year Input → Entitlement Calculation Engine → 8-Year Table
Entitlement Calculation Engine → Rule Enforcement (Year 7 = 6, Year 8 = 6)
Entitlement Calculation Engine → Per-row Status Assignment
8-Year Table + Status Assignment → Report Record Shape
Report Record Shape → Report List
Report List → Excel Export
```

## MVP Recommendation

Prioritize:
1. **HR join-year calculator + deterministic 8-year table** (including fixed year-7/year-8 = 6 rule)
2. **Per-row status rendering with clear legend**
3. **Report list + Excel export from dummy local data**

Defer:
- **Scenario preview mode:** valuable but non-essential for first clickable prototype
- **Enhanced explanation UX:** helpful differentiator, but second pass after base calculation flow is stable

## Sources

- Project requirements and constraints: `.planning/PROJECT.md` (HIGH confidence, primary scope authority)
- Leapsome leave management guide (feature expectations context): https://www.leapsome.com/blog/leave-management (LOW confidence: vendor thought-leadership)
- Vacation Tracker overview of common leave-management capabilities: https://vacationtracker.io/blog/top-leave-management-software/ (LOW confidence: vendor comparison content)
- e-days feature checklist article: https://www.e-days.com/news/choosing-a-leave-management-system (LOW confidence: vendor content)
- TimeTrakGO PTO tracking mistakes: https://www.timetrakgo.com/2025/01/23/common-mistakes-in-employee-pto-tracking-and-how-to-avoid-them/ (LOW confidence: single vendor advisory)
