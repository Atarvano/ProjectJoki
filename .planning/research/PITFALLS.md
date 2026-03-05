# Pitfalls Research

**Domain:** Incremental backend integration for existing native procedural PHP HR app (DB + CRUD + auth/session/role)
**Researched:** 2026-03-05
**Confidence:** HIGH

## Critical Pitfalls

### Pitfall 1: Split-Brain Data (Session Demo Data vs Database Canonical Data)

**What goes wrong:**
Existing pages still read/write session-array demo data while new pages read/write MySQL, so HR list, employee dashboard, and cuti outputs diverge.

**Why it happens:**
Incremental migration is done page-by-page without a hard rule for “single source of truth” per feature.

**How to avoid:**
Define canonical ownership early:
- `employees/users/leave_reports/leave_report_rows` must be DB-owned.
- Session only stores auth/session metadata (user id, role, flash messages), not business records.
- Add a temporary compatibility layer that reads DB first and blocks writes to legacy session arrays.
- Add a migration checklist per page: remove legacy write path before enabling production route.

**Warning signs:**
- Same employee count differs between two pages.
- Export result differs from dashboard result for same input.
- “Works after refresh” bugs caused by mixed read paths.

**Phase to address:**
Phase 14 (DATA foundation) and Phase 15 (EMP CRUD migration).

---

### Pitfall 2: Manual Schema Changes Without Repeatable Migration Discipline

**What goes wrong:**
Local DBs (Laragon/XAMPP) drift. One developer has extra column/index, another doesn’t. Features pass locally but fail elsewhere.

**Why it happens:**
Schema is changed directly via phpMyAdmin/manual SQL, not tracked through repeatable scripts and `schema_migrations`.

**How to avoid:**
- Treat `DATA-03` as mandatory gate: all schema changes via versioned SQL files.
- Maintain one bootstrap script: create DB objects in deterministic order, insert migration record atomically.
- Add startup/admin check: show pending/applied migrations.
- Never edit production schema ad-hoc.

**Warning signs:**
- “Unknown column” / “Table doesn’t exist” errors on teammate machine.
- Feature only works after someone manually runs SQL snippet from chat.

**Phase to address:**
Phase 14 (DATA-01..03).

---

### Pitfall 3: Non-Atomic HR-First Provisioning Flow

**What goes wrong:**
`employees` row gets created but `users` row fails (or vice versa), producing orphan or unusable accounts and broken FLOW-01..03.

**Why it happens:**
Provisioning is treated as two independent CRUD actions without transaction boundaries.

**How to avoid:**
- Wrap multi-step onboarding in transaction (`begin_transaction`/`commit`/`rollback`).
- Keep clear state model: `employee` exists first, `user` status = inactive/active/provisioned.
- Enforce DB constraints (unique employee identifier and unique user login).
- On failure, rollback and show actionable HR error.

**Warning signs:**
- Employee exists but cannot login and no clear status reason.
- Duplicate account creation attempts for same employee.

**Phase to address:**
Phase 16 (FLOW + AUTH orchestration), with schema prerequisites in Phase 14.

---

### Pitfall 4: Foreign-Key / Delete Strategy Mismatch (Hard Delete vs Business History)

**What goes wrong:**
Deleting employee records breaks linked users/reports or silently removes historical leave data required for audit/reporting.

**Why it happens:**
CRUD “delete” is implemented as immediate physical delete without explicit business policy (soft delete / deactivate / cascade rules).

**How to avoid:**
- Decide one policy per entity before coding EMPCRUD-04:
  - `employees`: prefer deactivate/soft-delete for history retention.
  - `users`: disable login rather than destructive delete when history exists.
  - `leave_reports`: immutable historical records.
- Encode policy in SQL constraints + app logic (not only UI wording).
- Add guard rails: if linked historical data exists, block hard delete and offer deactivate.

**Warning signs:**
- Delete action intermittently fails with integrity errors.
- Historical cuti reports disappear after employee cleanup.

**Phase to address:**
Phase 14 (schema constraints) and Phase 15 (EMPCRUD-04 behavior).

---

### Pitfall 5: “Prepared Statements Some Places” Anti-Pattern

**What goes wrong:**
Most CRUD uses prepared statements, but one legacy search/filter/login query still concatenates user input, leaving injection holes.

**Why it happens:**
Incremental refactor patches only new files; old helper files/routes stay untouched.

**How to avoid:**
- Create a mandatory query policy: all user-influenced SQL must be prepared.
- Run grep/code-review checklist for risky patterns (`"SELECT ... $_GET"`, string concatenation around request vars).
- Refactor shared DB helpers first so old pages inherit secure defaults.
- Keep error mode strict during development for early failures.

**Warning signs:**
- Mixed query style in codebase (prepare + raw concatenated SQL).
- Security fixes repeatedly appear on similar endpoints.

**Phase to address:**
Phase 15 (SEC-02 in CRUD) and Phase 16 (AUTH query paths).

---

### Pitfall 6: Password/Auth Migration Trap (Hashing Correctly but Breaking Existing Flow)

**What goes wrong:**
Logins fail after migration because passwords are stored/compared incorrectly (plaintext compare, double-hash, truncated storage, wrong column length assumptions).

**Why it happens:**
Legacy mindset expects reversible/plaintext behavior; migration forgets that `password_hash()` output includes algorithm metadata and can vary in length.

**How to avoid:**
- Store only hash from `password_hash()`, verify only with `password_verify()`.
- Use sufficient column size (e.g., varchar(255)) for algorithm agility.
- Never hash at login before `password_verify()`; pass raw password to verify.
- Add migration tests: create account via provisioning, then login success/failure matrix.

**Warning signs:**
- Freshly provisioned account cannot login despite correct password.
- Hash values appear to be transformed twice or truncated.

**Phase to address:**
Phase 16 (AUTH-01, SEC-01).

---

### Pitfall 7: Session Lifecycle Bugs in Legacy Routing

**What goes wrong:**
Users remain effectively logged in after logout, session fixation risk remains, or session breaks in subpaths due to cookie parameter mismatch.

**Why it happens:**
Existing app started with simple demo sessions; migration adds auth flags but not full session lifecycle hardening.

**How to avoid:**
- On login: regenerate session ID.
- On logout: clear `$_SESSION`, delete session cookie (matching params), destroy session.
- Set session cookie parameters before session start (HttpOnly, Secure on HTTPS, SameSite, correct path/domain).
- Enable strict mode (`session.use_strict_mode=On`) in runtime config.

**Warning signs:**
- Back button shows privileged page after logout.
- Session behaves differently across folders/routes.
- Random “new session each request” issues on specific path casing.

**Phase to address:**
Phase 16 (AUTH-02..04 hardening pass).

---

### Pitfall 8: UI-Only Role Checks (No Server-Side Authorization Boundary)

**What goes wrong:**
Menu/button is hidden for employee, but direct URL still opens HR endpoints.

**Why it happens:**
Role checks implemented in view template only, not in route/controller guard include.

**How to avoid:**
- Create centralized guard include functions:
  - `require_login()`
  - `require_role('hr')`
- Apply guards at top of every protected endpoint before DB/business logic.
- Add deny-by-default for unknown role/session state.
- Test with direct URL access and modified requests.

**Warning signs:**
- Employee can hit HR URL if typed manually.
- Authorization behavior differs between GET and POST handler for same feature.

**Phase to address:**
Phase 16 (AUTH-03 + FLOW constraints).

---

### Pitfall 9: Session Lock Contention Causing “App Hang” in Multi-Request Pages

**What goes wrong:**
Dashboard feels frozen or sequential when multiple requests occur (parallel tabs/AJAX/polling) due to session file lock contention.

**Why it happens:**
Script keeps session open for full request duration, including slow DB/export work.

**How to avoid:**
- Write needed session values early, then `session_write_close()` before long-running tasks.
- Use read-only session mode (`read_and_close`) when no session mutation needed.
- Avoid storing large mutable payloads in session.

**Warning signs:**
- Request B waits until Request A finishes for same user session.
- Intermittent timeout under normal multi-tab use.

**Phase to address:**
Phase 16 (auth/session stabilization) and export/report integration tasks.

---

### Pitfall 10: Cuti Output Regression During Data Source Migration

**What goes wrong:**
Years 6/7/8 output (target business focus) changes after backend migration, undermining trust even if auth/CRUD work.

**Why it happens:**
Migration alters input mapping, rounding/order, or row sourcing from new tables without parity tests against v1 behavior.

**How to avoid:**
- Define parity fixtures for representative employees and service-year edges.
- Compare old engine output vs DB-backed output before cutover.
- Keep calculator engine unchanged; only replace data access adapter.
- Add acceptance tests specifically for year 6/7/8 rows and exports.

**Warning signs:**
- HR reports “numbers look different” for known historical cases.
- Export totals mismatch dashboard totals for same employee.

**Phase to address:**
Phase 15 (DB-backed report rows) and Phase 16 (final auth/flow rollout gate).

## Technical Debt Patterns

| Shortcut | Immediate Benefit | Long-term Cost | When Acceptable |
|----------|-------------------|----------------|-----------------|
| Keep dual-write (session + DB) “temporarily” | Fast migration feel | Persistent inconsistency and hard debugging | Only behind short-lived feature flag with explicit removal date |
| Hard delete employee records | Simpler CRUD code | Broken history/audit/report integrity | Rarely; only for never-used test data |
| Copy-paste auth checks per file | Quick endpoint protection | Drift/inconsistency; missed routes | Never for milestone with role guard requirement |
| Manual phpMyAdmin schema edits | Fast local unblock | Environment drift and failed deployments | Never after Phase 14 starts |

## Integration Gotchas

| Integration | Common Mistake | Correct Approach |
|-------------|----------------|------------------|
| Existing frontend demo routes + new backend routes | Enabling backend while old routes still writable | Freeze old write paths and route all writes through DB-backed handlers |
| MySQLi + migration scripts | Assuming DDL can be rolled back like DML | Separate schema migration steps from transactional data migration; design idempotent scripts |
| Session cookie config on localhost subpaths | Setting inconsistent cookie path/domain between pages | Set consistent cookie params before `session_start()` and validate across all app routes |

## Performance Traps

| Trap | Symptoms | Prevention | When It Breaks |
|------|----------|------------|----------------|
| N+1 queries in employee list/details | Slow HR list with growing employees | Fetch list with needed joins/aggregates in one query path | ~1k+ employees or heavy report screens |
| Repeated prepare/execute per row for bulk operations | Provisioning/import feels sluggish | Reuse prepared statements for repeated execution where appropriate | Medium batch operations (100s rows) |
| Long request keeps session lock | Parallel tabs appear blocked | Close session early (`session_write_close`) after mutations | As soon as users open multiple pages/actions concurrently |

## Security Mistakes

| Mistake | Risk | Prevention |
|---------|------|------------|
| Comparing password hashes manually/string compare | Login bypass/failure patterns | Use `password_hash()` + `password_verify()` only |
| Not regenerating session ID after login | Session fixation window | Regenerate session ID on authentication/privilege elevation |
| Relying on hidden menu for authorization | Broken access control | Enforce server-side route guards for every protected endpoint |
| Accepting session id from URL/post | Session injection/leak | Use cookies only + strict mode, disable trans-sid in normal app paths |

## UX Pitfalls

| Pitfall | User Impact | Better Approach |
|---------|-------------|-----------------|
| Generic “Login gagal” for all causes | HR and employee cannot distinguish wrong password vs not provisioned | Return safe but specific state for HR troubleshooting (e.g., account not provisioned/disabled) |
| Delete action with no linkage warning | HR accidentally removes active employee context | Show dependency-aware confirmation (linked user/report counts) |
| Silent provisioning failures | HR assumes account created when it is not | Explicit success/failure receipt with employee ID + username status |

## "Looks Done But Isn't" Checklist

- [ ] **DB migration:** Often missing idempotency — verify migration script can run repeatedly without corrupting state.
- [ ] **Auth login:** Often missing session ID regeneration — verify session ID changes after successful login.
- [ ] **Logout:** Often missing cookie invalidation — verify session cookie removed, not only `session_destroy()` called.
- [ ] **Role guard:** Often missing on POST/action endpoints — verify direct URL and form endpoint both enforce role.
- [ ] **Provisioning flow:** Often missing transaction rollback — verify partial failure leaves no orphan account/employee state.
- [ ] **Cuti parity:** Often missing year 6/7/8 regression fixture — verify outputs match baseline before rollout.

## Recovery Strategies

| Pitfall | Recovery Cost | Recovery Steps |
|---------|---------------|----------------|
| Split-brain data | HIGH | Freeze writes, choose DB as canonical, run reconciliation script, remove legacy write paths, backfill missing rows |
| Broken provisioning atomicity | MEDIUM | Identify orphan records, reconcile with transactional repair script, add transaction wrapper and retries |
| Session lifecycle flaws | MEDIUM | Patch login/logout/session config centrally, invalidate active sessions, retest all protected routes |
| Output parity regression | HIGH | Roll back reporting cutover, run fixture-based diff, patch adapter mapping, re-release with parity checks |

## Pitfall-to-Phase Mapping

| Pitfall | Prevention Phase | Verification |
|---------|------------------|--------------|
| Split-brain data | Phase 14-15 | No business writes to session arrays; DB row counts and screens consistent |
| Schema drift/manual SQL | Phase 14 | Fresh environment setup works via migration scripts only |
| Non-atomic provisioning | Phase 16 | Failed mid-flow leaves zero partial records (transaction rollback proven) |
| Delete strategy mismatch | Phase 14-15 | EMPCRUD-04 behavior matches policy; history preserved |
| Mixed prepared/raw SQL | Phase 15-16 | Security review finds no user-input SQL concatenation |
| Password/auth migration errors | Phase 16 | Provision->login->logout test matrix passes |
| Session lifecycle bugs | Phase 16 | Session ID regenerated on login; logout fully invalidates cookie/session |
| UI-only role checks | Phase 16 | Direct URL and POST access tests deny unauthorized roles |
| Session lock contention | Phase 16 | Parallel requests no longer serialize unnecessarily |
| Cuti output regression | Phase 15-16 | Golden fixtures for year 6/7/8 match expected outputs |

## Sources

- PHP Manual — `session_start()` (HIGH): https://www.php.net/manual/en/function.session-start.php
- PHP Manual — Session management/security basics (HIGH): https://www.php.net/manual/en/features.session.security.management.php
- PHP Manual — Securing session INI settings (HIGH): https://www.php.net/manual/en/session.security.ini.php
- PHP Manual — `session_regenerate_id()` (HIGH): https://www.php.net/manual/en/function.session-regenerate-id.php
- PHP Manual — `session_destroy()` (HIGH): https://www.php.net/manual/en/function.session-destroy.php
- PHP Manual — `session_set_cookie_params()` (HIGH): https://www.php.net/manual/en/function.session-set-cookie-params.php
- PHP Manual — `session_write_close()` (HIGH): https://www.php.net/manual/en/function.session-write-close.php
- PHP Manual — MySQLi prepared statements quickstart (HIGH): https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php
- PHP Manual — MySQLi transaction API support (HIGH): https://www.php.net/manual/en/mysqli.quickstart.transactions.php
- PHP Manual — `password_hash()` (HIGH): https://www.php.net/manual/en/function.password-hash.php
- PHP Manual — `password_verify()` (HIGH): https://www.php.net/manual/en/function.password-verify.php
- MySQL 8.0 Reference — statements causing implicit commit (HIGH): https://dev.mysql.com/doc/refman/8.0/en/implicit-commit.html
- Supplemental ecosystem signals (LOW, unverified quality): web search results on legacy PHP migration/session pitfalls (2024–2026)

---
*Pitfalls research for: Sicuti HRD v2.0 backend native procedural PHP incremental integration*
*Researched: 2026-03-05*