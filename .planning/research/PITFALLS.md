# Pitfalls Research

**Domain:** Adding database (`koneksi.php`), employee CRUD, and native PHP session-auth into an existing demo-first procedural PHP HR app
**Researched:** 2026-03-04
**Confidence:** MEDIUM (official PHP/OWASP/MySQL docs used; Context7 unavailable due API key error)

## Critical Pitfalls

### Pitfall 1: Split-Brain Data (dummy/session arrays and DB both active)

**What goes wrong:**
Some pages still read old in-memory/session demo arrays while new pages write to MySQL. HR sees one dataset in dashboard but another in login/report/export behavior.

**Why it happens:**
Feature-addition into existing procedural code often patches per-page quickly instead of introducing one canonical data access path.

**How to avoid:**
- Define one repository seam early (`EmployeeRepository`-style procedural functions) and route all reads/writes through it.
- Use an explicit feature flag/migration mode: `demo`, `dual-read`, `db-only`.
- Add parity checks between old and new paths during migration, then remove demo source of truth.

**Warning signs:**
- Same employee appears in one page but missing in another.
- Login succeeds for users not visible in HR CRUD list.
- Export totals differ from on-screen totals after DB rollout.

**Phase to address:**
Phase 1 — Backend foundation & migration strategy (before CRUD/auth coding).

---

### Pitfall 2: Fragile `koneksi.php` bootstrap (include-order/path coupling)

**What goes wrong:**
DB connection not initialized consistently; some endpoints fail only in nested directories or after refactor. Intermittent "undefined variable $conn/$pdo" or duplicate include side effects.

**Why it happens:**
Procedural apps rely on ad-hoc `include` patterns and relative paths; connection bootstrap becomes implicit instead of contract-driven.

**How to avoid:**
- Standardize one bootstrap include (e.g., `bootstrap.php`) that loads config + `koneksi.php` once (`require_once`), using `__DIR__`-based absolute paths.
- Return one canonical handle type (recommend PDO only), never mixed mysqli/PDO.
- Fail fast on connection error (exceptions), not silent fallbacks.

**Warning signs:**
- Connection works in `/hr/*` but fails in `/employee/*`.
- Multiple DB APIs co-exist (`mysqli_query` and `$pdo->prepare`).
- Different files instantiate independent connections with different settings.

**Phase to address:**
Phase 1 — Backend foundation (bootstrap + include conventions).

---

### Pitfall 3: Silent SQL failure due weak PDO defaults/config

**What goes wrong:**
Queries fail quietly or behave differently than expected (emulated prepares, wrong type handling, hidden SQL errors), causing data corruption or false-success UX.

**Why it happens:**
Default PDO behavior is often left unchanged in fast migrations.

**How to avoid:**
- Set PDO options centrally in `koneksi.php`:
  - `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`
  - `PDO::ATTR_EMULATE_PREPARES => false`
- Use prepared statements for all variable input.
- Add centralized exception logging and user-safe error messages.

**Warning signs:**
- UI says "saved" but row missing.
- SQL warnings in logs without corresponding app errors.
- Edge-case inputs produce inconsistent query behavior.

**Phase to address:**
Phase 1 — Connection hardening and query conventions.

---

### Pitfall 4: Charset/collation mismatch (`utf8mb3` drift, garbled names)

**What goes wrong:**
Employee names/notes with multibyte characters become corrupted or rejected; search and uniqueness checks behave inconsistently.

**Why it happens:**
DB/table/connection charset are configured inconsistently during incremental migration.

**How to avoid:**
- Use `utf8mb4` at DB/table/connection level.
- Set `charset=utf8mb4` in PDO DSN.
- Standardize collation for user identifiers and text search fields.

**Warning signs:**
- "????" characters in list/export.
- Duplicate-looking names with hidden encoding differences.
- Filtering by name fails for non-ASCII characters.

**Phase to address:**
Phase 1 — Schema + connection baseline.

---

### Pitfall 5: Authentication stored as plaintext or reversible values

**What goes wrong:**
Passwords leak in DB dumps/logs; one breach compromises all user accounts.

**Why it happens:**
Demo-first systems often start with placeholder credentials and rush to "make login work".

**How to avoid:**
- Hash passwords with `password_hash()` and verify with `password_verify()` only.
- Never store raw passwords in any table, log, or session.
- Add migration rules for legacy demo credentials (force reset or one-time setup).

**Warning signs:**
- `password` column readable as plain text.
- Login compares `$_POST['password'] == $row['password']`.
- Debug logs include credential fields.

**Phase to address:**
Phase 3 — Auth implementation.

---

### Pitfall 6: Session fixation and weak session-cookie hardening

**What goes wrong:**
Attacker reuses or sets a victim session ID; authentication hijacking risk increases.

**Why it happens:**
Native PHP sessions are added but hardening steps are skipped.

**How to avoid:**
- Call `session_regenerate_id(true)` after successful login / privilege change.
- Enforce secure session ini/cookie settings: `use_strict_mode=On`, `cookie_httponly=On`, `cookie_secure=On` (HTTPS), `cookie_samesite=Lax/Strict`, `use_only_cookies=On`, `use_trans_sid=Off`.
- Set and enforce idle + absolute timeout policy.

**Warning signs:**
- Same session ID before and after login.
- Session IDs accepted from URL parameters.
- Session remains valid indefinitely across long inactivity.

**Phase to address:**
Phase 3 — Auth hardening and security controls.

---

### Pitfall 7: Broken auth flow from output-before-session/header behavior

**What goes wrong:**
Random login/logout redirect failures and "headers already sent" errors; protected routes become bypass-prone due defensive hacks.

**Why it happens:**
Existing pages echo HTML before `session_start()`/`header()`; adding auth later collides with old rendering order.

**How to avoid:**
- Enforce request lifecycle order: bootstrap -> session start -> auth guard -> controller -> render.
- Add preflight checks in dev (`headers_sent()` diagnostics).
- Remove BOM/whitespace before `<?php` and avoid early output in includes.

**Warning signs:**
- Intermittent redirect failures only on some pages.
- "Cannot modify header information" warnings.
- Teams adding JS redirects as workaround for auth redirects.

**Phase to address:**
Phase 2 — Route guards and request lifecycle refactor.

---

### Pitfall 8: IDOR/authorization gaps in employee CRUD

**What goes wrong:**
Any authenticated employee can edit/delete other employee records by changing `id` query parameters.

**Why it happens:**
Team conflates authentication (logged in) with authorization (allowed action).

**How to avoid:**
- Enforce role checks server-side on every CRUD action (not UI-only).
- Scope employee self-access to own record only.
- Add deny-by-default guard functions and integration tests for cross-user access attempts.

**Warning signs:**
- URLs like `/employee/edit.php?id=7` work for non-HR users.
- No server-side role check in POST handlers.
- Role is trusted from query param/client state.

**Phase to address:**
Phase 2 (CRUD) and Phase 3 (authz enforcement).

---

### Pitfall 9: Partial writes without transactions during onboarding

**What goes wrong:**
Employee row is created but auth linkage/profile rows fail (or opposite), leaving orphan/inconsistent records.

**Why it happens:**
Multi-step insert logic is added page-by-page without explicit transaction boundaries.

**How to avoid:**
- Wrap onboarding writes in DB transactions (`beginTransaction/commit/rollBack`).
- Keep DDL out of runtime transactions.
- Add foreign keys + unique constraints to block invalid states.

**Warning signs:**
- Rows in `employees` with no corresponding login account (or vice versa).
- Duplicate usernames/NIKs despite UI validation.
- Manual DB cleanups become common.

**Phase to address:**
Phase 2 — CRUD + schema integrity.

---

### Pitfall 10: No brute-force/rate-limit controls on native login

**What goes wrong:**
Credential stuffing and password guessing become trivial against internal app endpoints.

**Why it happens:**
Native session-auth implementation often stops at "works" (hash + session set) without abuse controls.

**How to avoid:**
- Add per-account and per-IP throttling with temporary lockouts.
- Log failed login attempts and suspicious patterns.
- Use generic login failure messages (no username enumeration).

**Warning signs:**
- Unlimited rapid login attempts.
- Different error messages for "user not found" vs "wrong password".
- No audit trail for auth failures.

**Phase to address:**
Phase 3 — Auth hardening.

---

## Technical Debt Patterns

Shortcuts that seem reasonable but create long-term problems.

| Shortcut | Immediate Benefit | Long-term Cost | When Acceptable |
|----------|-------------------|----------------|-----------------|
| Keep demo arrays as hidden fallback forever | Faster rollout, fewer immediate refactors | Permanent split-brain bugs and reporting distrust | Only temporary during explicit migration window |
| Mix mysqli in old pages and PDO in new pages | Less rewrite now | Two query idioms, inconsistent security/error behavior | Never |
| Hardcode DB creds in `koneksi.php` committed to repo | Quick local setup | Secret leakage and environment lock-in | Never |
| Add auth checks only in UI menu visibility | Fast perceived security | Direct URL privilege bypass (IDOR) | Never |
| Skip DB constraints because "validated in form" | Faster CRUD coding | Duplicate/orphan data and manual cleanup burden | Never |

## Integration Gotchas

Common mistakes when connecting old demo flow with new backend/auth flow.

| Integration | Common Mistake | Correct Approach |
|-------------|----------------|------------------|
| Existing role demo entry -> real login | Keep `?role=` as hidden auth bypass | Remove/disable demo entry in prod mode; server-side guard all routes |
| HR onboarding -> employee login enablement | Allow employee login before HR creates canonical record | Enforce HR-first lifecycle and account activation state |
| Report/export using old session data | Export still reads legacy session arrays | Route export through same DB-backed repository as dashboard |
| Shared navigation includes | Some pages include guards, others do not | Central auth middleware include required by all protected entry points |
| DB migration rollout | Direct cutover without reconciliation | Dual-read reconciliation checklist + one-way final cutover |

## Performance Traps

Patterns that work in demo scale but fail after real usage.

| Trap | Symptoms | Prevention | When It Breaks |
|------|----------|------------|----------------|
| N+1 employee enrichment queries in list view | HR employee page gets slower per row | Fetch with JOINs/batched queries; paginate | ~500+ records |
| Session file lock blocking concurrent requests | App appears to hang on parallel tab/AJAX requests | `session_write_close()` after session writes when safe | Even at low concurrency |
| No indexes on lookup fields (email/NIK/status) | Slow search/login checks | Add indexes based on query paths; verify explain plans | ~1k–10k records |

## Security Mistakes

Domain-specific security issues for this migration.

| Mistake | Risk | Prevention |
|---------|------|------------|
| Accepting SID via URL / transparent SID | Session leakage + fixation | `session.use_only_cookies=On`, `session.use_trans_sid=Off` |
| Missing cookie flags (`HttpOnly`, `Secure`, `SameSite`) | XSS/CSRF/session theft exposure | Configure secure cookie attributes centrally |
| No CSRF protection on HR CRUD POST forms | Forced state-changing requests | CSRF token per form + server-side validation |
| Over-privileged DB user (DDL/admin rights) | SQLi blast radius and accidental schema damage | Least-privilege DB account for app runtime |
| Credential/PII in logs | Internal data exposure | Structured redaction and safe logging policy |

## UX Pitfalls

Common user-experience failures when upgrading demo auth/data flow.

| Pitfall | User Impact | Better Approach |
|---------|-------------|-----------------|
| Employee suddenly cannot log in after migration | Trust loss and support burden | Show explicit account status: pending HR onboarding vs active |
| Generic DB/auth errors shown raw | Confusing and alarming messages | User-safe messages + internal correlation ID for logs |
| Session timeout without graceful handling | Data entry loss and frustration | Idle timeout warning + redirect with "session expired" context |

## "Looks Done But Isn't" Checklist

Things that appear complete but are missing critical pieces.

- [ ] **`koneksi.php`:** Connection works locally, but env separation/secrets are missing — verify `.env`/deployment-safe config exists.
- [ ] **Employee CRUD:** Create/read/update/delete works, but constraints absent — verify unique + foreign keys exist in schema.
- [ ] **Login flow:** Credentials validate, but session hardening absent — verify `session_regenerate_id(true)` and cookie/session ini settings.
- [ ] **Route protection:** Menu hidden, but endpoints still open — verify unauthorized direct URL access returns deny/redirect.
- [ ] **Logout:** Session variable cleared only — verify server session destroyed and cookie invalidated.
- [ ] **Migration complete:** New pages use DB, old pages may still use demo arrays — verify no production path reads dummy data.

## Recovery Strategies

When pitfalls occur despite prevention, how to recover.

| Pitfall | Recovery Cost | Recovery Steps |
|---------|---------------|----------------|
| Split-brain data sources | HIGH | Freeze writes, identify source-of-truth, run reconciliation script, remove legacy read paths |
| Plaintext password storage discovered | HIGH | Force password reset, hash migration, incident review, rotate any leaked creds |
| Authorization bypass in CRUD | HIGH | Hotfix server-side guards, audit access logs, patch all endpoints, add regression tests |
| Session/header lifecycle breakage | MEDIUM | Centralize bootstrap/session order, remove early output, add guard tests |
| Charset mismatch corruption | MEDIUM | Backup, convert schema/connection to utf8mb4, repair corrupted rows where possible |

## Pitfall-to-Phase Mapping

How future phases should address these pitfalls.

| Pitfall | Prevention Phase | Verification |
|---------|------------------|--------------|
| Split-brain data | Phase 1: Backend foundation & migration plan | All production reads come from one repository path |
| Fragile `koneksi.php` bootstrap | Phase 1: Bootstrap hardening | Every protected entrypoint uses the same bootstrap include |
| Silent SQL failure | Phase 1: PDO hardening | Forced bad query throws/logs exception in test env |
| Charset mismatch | Phase 1: Schema baseline | utf8mb4 set at DB/table/connection; multibyte test passes |
| IDOR in CRUD | Phase 2: CRUD security | Cross-user edit/delete test cases are denied |
| Partial writes/orphans | Phase 2: Transaction + constraints | Failed multi-step onboarding leaves zero partial rows |
| Header/session lifecycle bugs | Phase 2: Request pipeline refactor | No "headers already sent" in auth/redirect paths |
| Password/session weaknesses | Phase 3: Auth implementation + hardening | Hashes, regeneration, timeout, cookie flags verified |
| Brute-force exposure | Phase 3: Auth abuse controls | Rate limit and lockout behavior validated in tests |

## Sources

- PHP Manual — `session_start()`: https://www.php.net/manual/en/function.session-start.php **(HIGH)**
- PHP Manual — `session_regenerate_id()`: https://www.php.net/manual/en/function.session-regenerate-id.php **(HIGH)**
- PHP Manual — Securing Session INI settings: https://www.php.net/manual/en/session.security.ini.php **(HIGH)**
- PHP Manual — `password_hash()`: https://www.php.net/manual/en/function.password-hash.php **(HIGH)**
- PHP Manual — `password_verify()`: https://www.php.net/manual/en/function.password-verify.php **(HIGH)**
- PHP Manual — PDO prepared statements: https://www.php.net/manual/en/pdo.prepared-statements.php **(HIGH)**
- PHP Manual — PDO transactions: https://www.php.net/manual/en/pdo.transactions.php **(HIGH)**
- PHP Manual — PDO attributes (`ERRMODE`, `EMULATE_PREPARES`): https://www.php.net/manual/en/pdo.setattribute.php **(HIGH)**
- PHP Manual — PDO MySQL DSN (`charset`): https://www.php.net/manual/en/ref.pdo-mysql.connection.php **(HIGH)**
- OWASP Session Management Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html **(MEDIUM-HIGH)**
- OWASP SQL Injection Prevention Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html **(MEDIUM-HIGH)**
- MySQL Reference Manual — `utf8mb4`: https://dev.mysql.com/doc/refman/8.0/en/charset-unicode-utf8mb4.html **(HIGH)**

---
*Pitfalls research for: database + session-auth feature-addition in procedural PHP demo-first HR app*
*Researched: 2026-03-04*