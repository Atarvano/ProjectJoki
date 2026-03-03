# Domain Pitfalls

**Domain:** Greenfield HR leave entitlement dashboard prototype (procedural PHP, frontend-first, no DB)
**Researched:** 2026-03-03

## Critical Pitfalls

### Pitfall 1: Misleading Policy Logic Presented as “Final Truth”
**What goes wrong:** The 8-year entitlement output (including year-7/year-8 = 6 days) is shown without clear policy framing, so users assume it is legally complete for all scenarios.
**Why it happens:** Prototype teams optimize UI/demo speed and skip policy boundaries (jurisdiction, accrual model, carry-over, part-year edge cases).
**Consequences:** Wrong HR decisions, trust loss, and costly rework when real policy rules are added in v2.
**Warning signs:**
- Screens use absolute wording like “official entitlement” or “system-approved leave”.
- No policy/version metadata shown near calculated results.
- No disclaimer on excluded rules (carry-over, irregular hours, join/exit proration).
**Prevention:**
- Display a visible “Prototype Policy Scope” block on calculator and report pages.
- Include policy version + assumptions in UI and exported files.
- Add acceptance tests for boundaries (join date edges, leap year, year-window limits).
**Suggested phase mapping:**
- **Phase 1 (UI foundation):** Add scope disclaimer component and policy version badge.
- **Phase 2 (calculator logic):** Implement explicit rule boundaries + test matrix.
- **Phase 3 (report/export):** Stamp policy version and assumptions into exports.

### Pitfall 2: Fake Login Confusion (Demo Auth mistaken for Real Auth)
**What goes wrong:** Visual-only login is interpreted as secure authentication by stakeholders or pilot users.
**Why it happens:** Enterprise-clean UI makes prototype behavior look production-ready; missing “demo-only” cues.
**Consequences:** Security misunderstanding, risky demo usage expectations, and future compliance friction.
**Warning signs:**
- Login copy says “Sign in securely” while no backend auth/session checks exist.
- HR and Employee routes are directly reachable by URL with no demo notice.
- Stakeholders ask for “real user onboarding” using the v1 flow.
**Prevention:**
- Label login and role selection as “Visual Demo Mode (No Authentication)”.
- Add a persistent banner in protected-looking pages: “Prototype only, local dummy data.”
- Keep route guards simple but explicit (demo role flag) to prepare auth seam for v2.
**Suggested phase mapping:**
- **Phase 1 (landing/login):** Add demo-mode wording and legal-safe disclaimers.
- **Phase 2 (role flows):** Add minimal guard wrapper and centralized auth-placeholder function.
- **Phase 4 (v2 prep):** Replace placeholder with real auth provider/session controls.

### Pitfall 3: Export Mismatch (UI Table vs Excel Output Drift)
**What goes wrong:** The report shown on screen differs from exported Excel (different columns, ordering, rounding, totals, or date formats).
**Why it happens:** UI rendering and export mapping are coded separately with duplicated transformation logic.
**Consequences:** HR loses confidence quickly; exported evidence becomes non-defensible.
**Warning signs:**
- Export function has its own hardcoded column labels and computations.
- Date/number formatting differs between HTML and spreadsheet.
- “Why does Excel not match what I see?” appears in demo feedback.
**Prevention:**
- Use one canonical data-shaping function for both table render and export.
- Define a single column schema (id, label, formatter, order) used by UI + export.
- Add snapshot tests comparing rendered rows and exported rows from same dummy dataset.
- For PhpSpreadsheet, set explicit datatypes and date formats to avoid silent coercion.
**Suggested phase mapping:**
- **Phase 2 (report table):** Build canonical transformer + schema.
- **Phase 3 (Excel export):** Reuse schema, enforce explicit cell typing/formatting.
- **Phase 3.5 (QA hardening):** Add parity regression checks.

### Pitfall 4: UI Inconsistency Across Landing, HR, and Employee Flows
**What goes wrong:** Enterprise-clean landing page quality does not carry into dashboard/report pages; prototype feels stitched together.
**Why it happens:** Bootstrap defaults and ad-hoc CSS overrides accumulate without design tokens or component conventions.
**Consequences:** Perceived quality drop, stakeholder skepticism, and expensive restyling later.
**Warning signs:**
- Different button sizes/colors for similar actions.
- Inconsistent spacing, typography scale, and status badges across pages.
- Multiple CSS files redefining same utility classes.
**Prevention:**
- Create a tiny design system early: color tokens, type scale, spacing scale, button variants.
- Use shared partials for header, cards, tables, badges, alert styles.
- Enforce a “no page-level one-off styles unless documented” rule.
**Suggested phase mapping:**
- **Phase 1 (landing + base UI):** Establish tokens and shared components.
- **Phase 2 (dashboard pages):** Consume shared components only.
- **Phase 3 (report/export UI):** Run visual consistency pass before export polish.

### Pitfall 5: Procedural PHP Sprawl That Blocks v2 Migration
**What goes wrong:** Business rules, HTML output, and request handling become tightly interleaved across multiple PHP files.
**Why it happens:** Fast prototype coding in procedural style without boundary conventions.
**Consequences:** Hard migration to DB/auth in v2, duplicated rules, and brittle bug fixes.
**Warning signs:**
- Same leave formula duplicated in multiple pages.
- Superglobals (`$_GET`, `$_POST`) read directly inside view templates.
- No dedicated function file for policy logic or report transformation.
**Prevention:**
- Keep procedural style, but enforce layered files:
  - `policy.php` (leave logic)
  - `reporting.php` (table/export shaping)
  - `demo_auth.php` (placeholder role/session)
  - page controllers + view partials
- Use associative-array contracts with documented keys for all record shapes.
- Add lightweight contract checks (required keys, type guards) before render/export.
**Suggested phase mapping:**
- **Phase 1 (project skeleton):** Create function-layer boundaries and include conventions.
- **Phase 2 (calculator/report):** Centralize logic; ban copy-paste formulas.
- **Phase 4 (v2 migration prep):** Replace array-backed repositories with DB adapters behind same function signatures.

## Moderate Pitfalls

### Pitfall 6: Date Boundary and Rounding Errors in Entitlement Calculations
**What goes wrong:** Off-by-one errors around anniversaries, leap years, part-day rounding, and part-year accrual.
**Prevention:**
- Normalize all date math using `DateTimeImmutable` and explicit timezone.
- Define rounding policy once (display vs storage) and apply consistently.
- Add boundary fixtures: leap day join, end-of-month join, year transition.
**Warning signs:**
- Same employee input yields different outputs on different pages.
- Decimal leave values are rounded differently in table vs export.
**Suggested phase mapping:**
- **Phase 2:** Lock calculation policy + edge-case test suite.

### Pitfall 7: Array-Key Drift in Dummy Data Structures
**What goes wrong:** Inconsistent associative array keys across pages (`join_year` vs `joinYear`) cause silent missing fields and wrong exports.
**Prevention:**
- Define one data dictionary (`employee_id`, `join_year`, `entitlement_rows`, etc.).
- Validate required keys before rendering or exporting.
**Warning signs:**
- PHP warnings/empty cells only in certain pages.
- Quick “fixes” that rename keys in one page but not others.
**Suggested phase mapping:**
- **Phase 1:** Publish key schema.
- **Phase 2-3:** Enforce schema checks in transformers and export pipeline.

## Minor Pitfalls

### Pitfall 8: Over-Polished Demo Copy That Implies Compliance Certification
**What goes wrong:** Marketing-style wording implies legal/compliance completeness for a prototype.
**Prevention:**
- Use precise labels: “Prototype”, “Simulated”, “Not for production decisions”.
**Warning signs:**
- Copy includes “compliant”, “secure”, “official” without supporting controls.
**Suggested phase mapping:**
- **Phase 1:** Content review gate before UI sign-off.

### Pitfall 9: Export Filename and Versioning Ambiguity
**What goes wrong:** Users cannot trace which policy/version generated a file.
**Prevention:**
- Include timestamp + policy version in filename and first worksheet metadata.
**Warning signs:**
- Multiple files named `report.xlsx` with conflicting values.
**Suggested phase mapping:**
- **Phase 3:** Add naming/version convention for all exports.

## Phase-Specific Warnings

| Phase Topic | Likely Pitfall | Mitigation |
|-------------|---------------|------------|
| Landing + visual login | Fake auth interpreted as real auth | Persistent demo-mode banners and explicit copy |
| Leave calculator build | Policy oversimplification and date edge bugs | Scope boundaries + edge-case fixtures + policy versioning |
| Report table + Excel export | UI/export data mismatch | Single transformer + shared column schema |
| Styling and page expansion | Design inconsistency across modules | Tokenized design system + shared components |
| v2 readiness | Procedural code sprawl blocking DB/auth migration | Function-layer boundaries and stable data contracts |

## Sources

- GOV.UK holiday entitlement guidance (statutory rules, accrual contexts): https://www.gov.uk/holiday-entitlement-rights **(MEDIUM confidence for jurisdiction-specific policy examples)**
- GOV.UK holiday calculator (explicit calculation framing): https://www.gov.uk/calculate-your-holiday-entitlement **(MEDIUM confidence)**
- ACAS holiday entitlement guidance (rounding, accrual, part-time treatment): https://www.acas.org.uk/checking-holiday-entitlement **(MEDIUM confidence)**
- PhpSpreadsheet docs (explicit typing/formatting behavior for Excel generation): https://phpspreadsheet.readthedocs.io/en/latest/topics/recipes/ **(HIGH confidence)**
- PHP manual — arrays (key casting/overwrite behavior, array pitfalls): https://www.php.net/manual/en/language.types.array.php **(HIGH confidence)**
- PHP manual — datetime parsing/overflow caveats: https://www.php.net/manual/en/datetime.formats.php **(HIGH confidence)**
- OWASP Authentication Cheat Sheet (why visual login can create security misunderstandings): https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html **(MEDIUM confidence for prototype communication implications)**
