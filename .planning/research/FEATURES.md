# Feature Research

**Domain:** Small internal HR web app (HR-first employee onboarding + employee login)
**Researched:** 2026-03-04
**Confidence:** MEDIUM

## Feature Landscape

### Table Stakes (Users Expect These)

Features users assume exist for this milestone. Missing these = onboarding/auth feels incomplete.

| Feature | Why Expected | Complexity | Notes |
|---------|--------------|------------|-------|
| HR creates employee master record first (name, employee ID, work email, join date, status) | Internal HR systems are employer-controlled systems of record; account lifecycle starts from HR data entry | MEDIUM | Must be the gating step before employee access. Aligns with OrangeHRM-style flow where employee is added before/with login details. |
| Optional/linked login credential setup per employee record | HR expects ability to enable/disable login tied to employee status, not standalone user account | MEDIUM | Implement as one-to-one linkage between `employees` and `user_credentials` (or equivalent). Prevent orphan logins. |
| Real backend login (replace visual-only demo) with session persistence | Users expect login to protect pages and preserve authenticated state across pages | MEDIUM | Use PHP native sessions + route/page guards. Preserve role split: HR vs Employee. |
| Secure password handling (`password_hash`, `password_verify`) | Baseline security expectation even in internal apps | LOW | Use PHP-native password APIs, never plaintext/reversible storage. |
| Session hardening at login/logout (`session_regenerate_id`, secure cookie params) | Standard practice to reduce session fixation/hijacking risk | MEDIUM | Set cookie flags (`HttpOnly`, `Secure` in HTTPS env, `SameSite`), regenerate session ID on successful login, destroy session on logout. |
| Account state controls (active/inactive) with clear login failure behavior | HR needs to disable access immediately (termination/leave/etc.) | LOW | Login must check employee/account status each attempt; return generic invalid credentials messaging to avoid user enumeration detail leakage. |
| First-login handoff to existing employee self-view | New login must immediately unlock value from existing v1 features | LOW | After auth, employee lands on existing self-view entitlement page; no duplicate entitlement logic. |

### Differentiators (Competitive Advantage)

Not mandatory for v2.0 launch, but high value for internal HR usability.

| Feature | Value Proposition | Complexity | Notes |
|---------|-------------------|------------|-------|
| Onboarding readiness checklist (per employee) | Gives HR operational visibility: "record complete, credential created, first login done" | MEDIUM | Lightweight status model (not full workflow engine). Great fit for procedural PHP + simple DB tables. |
| Invite-first activation flow (HR creates employee, employee sets password via first-use token) | Avoids HR setting reusable passwords and improves security posture | HIGH | Token lifecycle, expiry, one-time use, and audit trail needed. Good v2.x candidate if v2.0 time is tight. |
| HR onboarding dashboard metrics (pending activation, first-login completion rate) | Converts onboarding from admin task into measurable process | MEDIUM | Can reuse session-backed/reporting UI patterns but now DB-backed. Keep metrics simple initially. |
| Minimal auth audit trail (created by HR, login success/fail, disabled account attempts) | Improves traceability for internal governance and troubleshooting | MEDIUM | Start with append-only events table; avoid full SIEM ambitions. |

### Anti-Features (Commonly Requested, Often Problematic)

Features that sound attractive but are poor fit for this milestone/scope.

| Feature | Why Requested | Why Problematic | Alternative |
|---------|---------------|-----------------|-------------|
| Self-signup / open employee registration | Feels "faster" for account creation | Conflicts with HR-first requirement and creates identity/data integrity risk | Keep HR-provisioned accounts only; employee access enabled only after HR record exists |
| Social login / SSO integration (Google/Microsoft/OIDC) in v2.0 | Perceived convenience | High integration/security complexity; out of proportion for procedural internal v2.0 foundation | Defer to later milestone after stable local auth + role model |
| Full onboarding suite (e-signatures, LMS, hardware provisioning, 30-60-90 workflows) | Mimics enterprise HR platforms | Massive scope expansion; delays core backend/auth foundation | Keep v2.0 to HR record + auth + enablement lifecycle; add modules incrementally |
| Aggressive account lockout without recovery design | Security-driven request | Can create internal lockout incidents and HR support burden | Use modest throttling + admin reset path + clear support process |
| Password rules that prioritize complexity over length/usability (legacy regex-heavy policies) | Traditional security habit | Poor UX, weak real-world outcomes, higher reset/support overhead | Use reasonable minimum length, block weak/common passwords if possible, and secure hashing/session controls |

## Feature Dependencies

```text
[Existing v1 Employee Self-View]
    └──requires login context from──> [Session-backed Employee Authentication]
                                            └──requires──> [Credential record linked to employee]
                                                                └──requires──> [HR creates employee master record]

[Existing v1 Report Save/List/Export]
    └──enhances──> [HR role login + page guards]

[Open self-signup] ──conflicts──> [HR-first onboarding gate]
```

### Dependency Notes

- **Session-backed employee authentication requires linked credential record:** employee access must resolve to a valid employee entity, otherwise self-view/report attribution breaks.
- **Linked credential record requires HR-created employee record:** this enforces milestone requirement that onboarding data precedes employee login.
- **Existing self-view requires authentication context:** v1 already has entitlement rendering; v2.0 should reuse it, now gated by authenticated employee identity.
- **Existing report flows are enhanced by HR role login:** report list/export should be tied to authenticated HR sessions instead of demo entry points.
- **Open self-signup conflicts with HR-first onboarding:** it bypasses source-of-truth employee creation and should stay out of scope.

## MVP Definition

### Launch With (v2.0)

- [ ] HR creates employee records in DB (including active/inactive status) — core system-of-record gate
- [ ] HR can create/enable linked employee login credentials — required for controlled access lifecycle
- [ ] Native PHP session-based login/logout with role-aware guards — replaces visual-only auth and protects pages
- [ ] Employee successful login lands in existing self-view entitlement feature — immediate user-visible value
- [ ] Basic failed-login handling (generic errors) + session hardening — minimum safe auth baseline

### Add After Validation (v2.x)

- [ ] Invite-token first-time password setup — add when HR no longer wants manual initial passwords
- [ ] Onboarding readiness checklist/status tracking — add when HR volume makes manual tracking painful
- [ ] Auth/onboarding audit timeline UI — add when traceability requests increase

### Future Consideration (v3+)

- [ ] SSO/OIDC integration — defer until local auth and role/access model are stable
- [ ] Expanded onboarding modules (documents/e-sign/LMS provisioning) — defer until core CRUD+auth proves reliable

## Feature Prioritization Matrix

| Feature | User Value | Implementation Cost | Priority |
|---------|------------|---------------------|----------|
| HR employee master record + status | HIGH | MEDIUM | P1 |
| Linked credential creation/enabling | HIGH | MEDIUM | P1 |
| Session-backed login/logout + guards | HIGH | MEDIUM | P1 |
| Secure password/session handling baseline | HIGH | LOW/MEDIUM | P1 |
| First-login password activation token | MEDIUM | HIGH | P2 |
| Onboarding checklist/status board | MEDIUM | MEDIUM | P2 |
| SSO/OIDC | LOW (for current scope) | HIGH | P3 |

**Priority key:**
- P1: Must have for v2.0 launch
- P2: Should have after core validation
- P3: Nice to have / future scope

## Competitor Feature Analysis

| Feature | OrangeHRM pattern | ADP guidance pattern | Our Approach (v2.0) |
|---------|-------------------|----------------------|---------------------|
| Employee record before login | Add employee in PIM, optionally create login during add flow | Employer sets profile/system-of-record and provisions access in onboarding responsibilities | Mirror this HR-first gate as non-negotiable |
| Onboarding process breadth | Supports broad HR profile administration | Recommends broader 90-day process and checklists | Keep only backend foundation subset (record + auth + enablement) |
| Authentication expectations | Role-linked user account management (ESS/Admin) | Emphasizes preparation, credentials readiness, and structured flow | Role-aware session auth replacing demo-only login |

## Sources

- OrangeHRM Help Center — “How to Add an Employee” (employee-first + optional login creation): https://starterhelp.orangehrm.com/hc/en-us/articles/360018588500-How-to-Add-an-Employee (**MEDIUM** confidence; official vendor help doc, older date but workflow still canonical)
- ADP Resource — “Onboarding” (updated Feb 20, 2026): https://www.adp.com/resources/articles-and-insights/articles/o/onboarding.aspx (**MEDIUM** confidence; official vendor guidance, broad but current)
- PHP Manual — `password_hash`: https://www.php.net/manual/en/function.password-hash.php (**HIGH** confidence)
- PHP Manual — `password_verify`: https://www.php.net/manual/en/function.password-verify.php (**HIGH** confidence)
- PHP Manual — `password_needs_rehash`: https://www.php.net/manual/en/function.password-needs-rehash.php (**HIGH** confidence)
- PHP Manual — `session_regenerate_id`: https://www.php.net/manual/en/function.session-regenerate-id.php (**HIGH** confidence)
- PHP Manual — `session_set_cookie_params`: https://www.php.net/manual/en/function.session-set-cookie-params.php (**HIGH** confidence)
- OWASP Authentication Cheat Sheet (generic errors, TLS, throttling, secure auth controls): https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html (**HIGH** confidence)

---
*Feature research for: HR-first onboarding + employee login milestone (v2.0 backend foundation)*
*Researched: 2026-03-04*