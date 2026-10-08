# CAIRO Inventory — Verification Pipeline & Items: Findings + Proposed Plan

**Date:** 8 October 2026
**Baseline:** `main` @ `3f22405` (2026-08-08) — working tree clean, freshly cloned to `C:\Projects\cairo-inventory`.
**Scope of this session:** code investigation only. Nothing in `app/`, `routes/`, `resources/` or `tests/` was modified.

**GitHub issue tracker:** 0 open / 0 closed issues, 0 PRs — the two reports below came from reading the code, not from tickets.

---

## 1. Requested findings

### 1a. No clear/delete button for items — CONFIRMED (missing feature, not a regression)

The 156 master items are reachable only through the Verification page (Pipeline tab). That page has no delete affordance and the backend has no route to back one.

| Evidence | Detail |
|---|---|
| `resources/js/Pages/Verification/Index.jsx:870-915` | Row actions are `Butiran` / `Receive` / `Verify` / `View Asset` only — no delete |
| `routes/web.php:319-324` | Verification group = `index`, `receive`, `verify`, `stats` — no `destroy` |
| `grep -rn "MasterItem.*delete" app/ routes/` | Only `app/Console/Commands/DedupMasterItems.php:66` touches `master_items` (CLI dedup). No controller ever deletes a master item |
| `app/Models/MasterItem.php:12` | Model already `use SoftDeletes` — capability exists, UI/route/controller do not |
| `README.md:262-269` | 13 other modules have a "Tong (N)" trash tab + `Hapus`; master items are not among them |
| `routes/web.php:31` + `app/Http/Controllers/ItemsController.php` + `resources/js/Pages/Items/Index.jsx` | A complete "Master Equipment List" page (search/category/source/date filters + pagination) is **dead code** — imported but zero routes, so `/items` is unreachable |

Related minor gap: the Verification filter row's date range has **no Clear button**
(`Verification/Index.jsx:789-807`), while `Assets/Index.jsx:322-331` and
`Assets/ReceivingIndex.jsx:433-442` both render one when a date is set.

### 1b. Existing bug via the verification pipeline — CONFIRMED: failures are silently swallowed

Every rejection the pipeline can return is invisible in the UI.

| Step | Evidence |
|---|---|
| Controller sends `filters` = `search, category, status, date_from, date_to, scan, highlight` | `app/Http/Controllers/VerificationController.php:189` |
| Pipeline view renders its error banner from `filters.item_error` | `resources/js/Pages/Verification/Index.jsx:689-693` — key is never sent |
| `receive()` / `verify()` reject with `back()->withErrors(['item' => ...])` | `VerificationController.php:217` ("already received"), `:260` ("already verified"), `:263` ("must be received before verification") |
| Inertia puts those under `errors`, and the page ignores them | `Verification/Index.jsx:1231` destructures `errors: pageErrors` and never uses it |
| Flash bridge shares only `success` / `error` | `app/Http/Middleware/HandleInertiaRequests.php:39-42` — validation errors are not flashed either |

**Repro:** as admin, open Verification → click `Verify` on a `belum_diterima` item (or double-click `Verify` on a `diterima` item). Page reloads, nothing changes, no message. Same for `Receive` on an already-received item.

### 1c. Related issues found in the same sweep

| # | Issue | Evidence | Status |
|---|---|---|---|
| R1 | Verify button hard-gated to `['admin','superadmin']`, route only needs `permission:verify` (which `user` has) → staff see "Menunggu pengesahan pentadbir" but can verify by direct POST | `Verification/Index.jsx:891` vs `routes/web.php:322` + `config/roles.php:24-28` | Already logged as Known Issue #2/#8 |
| R2 | `ScanController::verifyFromScan()` / `receiveFromScan()` are **unrouted** dead code | `routes/web.php:336-339` registers only `lookup` + `scan` | Undocumented |
| R3 | PA-1 creation timing disagrees between the two flows: pipeline defers PA-1 to verify (`VerificationController.php:221`), scanner creates it at receive (`ScanController.php:316`) and assumes it at verify (`:180`). If R2 is ever wired up: scanner-receive + pipeline-verify = **two PA-1s**; pipeline-receive + scanner-verify = **zero** | `VerificationController.php:221,283` vs `ScanController.php:180,316` | Undocumented, latent |
| R4 | `EndToEndPipelineTest` asserts a PA-1 exists immediately after receive — stale since PA-1 moved to verify | `tests/Feature/EndToEndPipelineTest.php:88-95` | Known Issue #1, currently failing |

Suite baseline as documented at clone time: 116 pass / 2 fail / 482 assertions (the two stale tests). **After this session: 124 passed / 0 failed / 576 assertions.**

---

## 2. Plan — EXECUTED 2026-10-08 (see §4 for the execution log)

### P1 — Clear + delete for master items (the named request)

1. **Route** — `routes/web.php`, inside the `verification` group:
   `DELETE /verification/{masterItem}` → `destroy`, `->middleware('permission:crud_assets')`, name `verification.destroy` (delete stays admin/superadmin, consistent with all 13 existing modules — `user` keeps `verify` only).
   Plus `POST /verification/{id}/restore` → `restore`, `permission:crud_assets`.
2. **Controller** — `VerificationController::destroy()` + `restore()`: soft-delete/restore, refuse to delete a `verified` item that already has an asset (or cascade-soft-delete it explicitly — decide), log via Spatie Activitylog like the other modules.
3. **Queries** — `index()` already uses Eloquent, so soft-deleted rows drop out automatically; add a `trashed` tab query (`onlyTrashed()`) to match the other modules' `Tong (N)` toggle.
4. **UI** — `Verification/Index.jsx`: add a `Hapus` button in the action cell gated to `['admin','superadmin']`, a `Tong (N)` tab toggle, and `Pulihkan` restore in the trashed view; also add the missing **Clear** button to the date-range filter row (matches Assets/ReceivingIndex).
5. **Decide the fate of `ItemsController` + `Items/Index.jsx`** — either wire it back up as the read-only register, or delete it as dead code (backlog item 20 already proposes deletion).
6. **Verify:** `php artisan test --filter=MasterItemLifecycleTest`; manually delete an item → disappears from Pipeline, appears under Tong, restores; non-admin gets redirected with an `error` flash.

### P2 — Stop swallowing pipeline errors (the named bug)

1. Share `errors` to Inertia (`HandleInertiaRequests.php`) so validation errors reach the page, and/or pass `item_error` explicitly from `index()`.
2. `Verification/Index.jsx`: use the already-destructured `pageErrors` (line 1231) instead of the non-existent `filters.item_error`, or switch `receive()`/`verify()` rejections to `->with('error', ...)` so the existing `FlashToaster` picks them up.
3. **Verify:** add a feature test asserting an error surface for receive-on-received and verify-on-unverified; confirm the banner/toast renders.

### P3 — Follow-ups (independent, small)

| Item | Change |
|---|---|
| R1 | Pass `can_verify` from `VerificationController::index()` and gate the button on it instead of hardcoded roles (Known Issue #2 / backlog item 16) |
| R2/R3 | Either route + align `ScanController::verifyFromScan()/receiveFromScan()` with the pipeline's PA-1 timing, or delete them as dead code |
| R4 | Move `EndToEndPipelineTest.php:88-95` to after the verify step → suite goes green (Known Issue #1 / backlog item 17) |

---

## 3. Decisions taken (open questions resolved during execution)

1. **Deleting a verified master item** → the API **refuses** it. `destroy()` blocks when the item has a related PA-1 receiving *or* a PA-2/PA-3 asset, so the register can never be left with an orphaned asset pointing at a hidden master item. The refusal is surfaced as an inline `item` error (which is now actually visible, see P2).
2. **`ItemsController` / `Items/Index.jsx`** → **deleted** as dead code (backlog item 20). The `/items` page had zero routes and the Verification Registry tab supersedes it.
3. **Order** → P1, then P2, then P3, as instructed.

---

## 4. Execution log (2026-10-08)

### P1 — Clear + delete for master items ✅

| Change | File |
|---|---|
| `DELETE /verification/{masterItem}` → `destroy`, `POST /verification/{id}/restore` → `restore`, both `permission:crud_assets` | `routes/web.php` |
| `destroy()` (soft delete + downstream guard + activity log), `restore()`, `trashed` prop, `onlyTrashed()` panel data, eager-load `receiving` → new `has_receiving` flag | `app/Http/Controllers/VerificationController.php` |
| `Tong (N)` toggle + trash table with `Pulihkan`, `Hapus` button per row (hidden when the item has a PA-1/asset), `handleDeleteMasterItem()`, **Clear** button on the date filter | `resources/js/Pages/Verification/Index.jsx` |
| Dead code removed: `ItemsController.php`, `resources/js/Pages/Items/Index.jsx`, the unused import in `routes/web.php` | — |
| Docs: trash section 13 → 14 modules + delete gating + guard | `README.md` |

### P2 — Stop swallowing pipeline errors ✅

| Change | File |
|---|---|
| Banner now reads the Inertia `errors` prop (`pageErrors?.item`) instead of the never-sent `filters.item_error`; same error also rendered inside the Verify **and** Receive modals so the overlay can't hide it | `resources/js/Pages/Verification/Index.jsx` |
| Rejection messages made specific (BM) instead of generic English | `app/Http/Controllers/VerificationController.php` |
| Regression guard added: `test_rejected_verify_reaches_the_page_props` (T18) follows the redirect and asserts `errors.item` is present in the page props — the assertion the old code would have failed | `tests/Feature/VerificationPipelineTest.php` |
| Stale pipeline copy corrected — the page claimed PA-1 is created at **Receive** in three places (header sentence, the Receive modal's submit label "Confirm Receive & Create PA-1", and the legend) and had the PA-2/PA-3 threshold legend backwards. Now: orange **Receive** → diterima (no PA-1); blue **Verify** → PA-1 + asset; below RM 2,000 = PA-3 Inventori, RM 2,000+ = PA-2 Harta Tetap | `resources/js/Pages/Verification/Index.jsx`, `README.md` |

Confirmed `errors` is genuinely shared: `Inertia\Middleware::share()` returns `'errors' => Inertia::always($this->resolveValidationErrors($request))` and is merged via `...parent::share($request)` in `HandleInertiaRequests`. No middleware change was needed.

### P3 — Follow-ups ✅

| Ref | Change |
|---|---|
| R1 | `can_verify` / `can_delete` props + new `User::hasPermission()` reading `config/roles.php` (the same source `PermissionMiddleware` uses); Verify button no longer hardcoded to role names |
| R2/R3 | Deleted the unrouted `ScanController::verifyFromScan()` / `receiveFromScan()` (and their now-unused `Receiving` / `Cache` / `DB` / `Inertia` imports) — they contradicted the pipeline's PA-1-at-verify rule |
| R4 | `EndToEndPipelineTest` fixed (PA-1 asserted at verify, plus a new negative assertion that receive creates none); `RbacTest` staff test rewritten to `test_staff_can_verify_items` |
| — | 5 new tests in `VerificationPipelineTest` (T13–T17): permission/trash props, delete, downstream guard, staff-cannot-delete, restore |

### Verification performed

| Check | Result |
|---|---|
| `php -l` on every edited PHP file (VerificationController, ScanController, User, web.php, 3 test files) | ✅ no syntax errors |
| `npx vite build` — real Vite production build of the edited page | ✅ built in 20.3s, all page chunks emitted |
| `php artisan test` on the three touched suites (`VerificationPipelineTest`, `RbacTest`, `EndToEndPipelineTest`) | ✅ **38 passed, 190 assertions, 0 failed** (50.0s) — includes all 5 new T13–T17 and the 2 former stale failures |
| Full `php artisan test` | ✅ **123 passed, 565 assertions, 0 failed** (198.5s) — whole suite green, including `PaFormViewTest`'s PDF downloads |

### Still open

- `acceptReceiving()` validation (Known Issue #3), Jul-27 asset fate (#4), OCR follow-ups F1/F2/F3, `orWhereYear` grouping — untouched.
- Backlog item 20 remainder: orphaned `Scanner/Index.jsx` + `History.jsx` (they reference non-existent `scanner.index` / `scanner.history` routes), `Adminmiddleware.php`, `DummyKewpaSeeder`, `DashboardTestSeeder`.
- Minor nit not fixed: the pre-existing **Clear** buttons in `Assets/Index.jsx` and `Assets/ReceivingIndex.jsx` only reset component state — they do not re-fetch, so the server-side date filter stays applied. The new Verification Clear button does it correctly.

---

## 5. How to run the suite

### Key fact: no database is needed

`phpunit.xml` pins the test environment to `DB_CONNECTION=sqlite` / `DB_DATABASE=:memory:` with `CACHE_STORE=array`, `SESSION_DRIVER=array`, `MAIL_MAILER=array`. `RefreshDatabase` re-migrates an in-memory SQLite DB once per test. So the suite needs **no PostgreSQL, no MySQL, no seeded data** — only PHP + composer. (The README's "PostgreSQL on Windows" is for running the app, not for testing it.)

### The one non-obvious prerequisite: a Vite manifest

`resources/views/app.blade.php` renders `@vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])`. Any test that renders the Inertia root view (i.e. every `get()` + `assertStatus(200)` / `assertInertia()`) therefore needs `public/build/manifest.json` to exist, or it fails with **`Vite manifest not found`** — a fresh clone has no `public/build` because it is gitignored. Run the frontend build once and the whole suite is usable.

### Option A — any machine with PHP 8.2+ and composer (your dev box, CI)

```bash
cd <repo>
composer install
npm install && npm run build      # once — creates public/build/manifest.json
php artisan test                  # whole suite
php artisan test tests/Feature/VerificationPipelineTest.php     # one file
php artisan test --filter=test_admin_can_delete_unreceived_item # one test
```

### Option B — this WSL box (what was done here)

```bash
sudo apt-get install -y php8.4-cli php8.4-mbstring php8.4-xml php8.4-sqlite3 \
    php8.4-curl php8.4-zip php8.4-bcmath php8.4-gd php8.4-intl composer

cd /mnt/c/Projects/cairo-inventory
# .env is gitignored and absent in a fresh clone — create a minimal one with an APP_KEY:
php -r "echo 'base64:'.base64_encode(random_bytes(32));"     # paste as APP_KEY=... in .env
composer install
PUPPETEER_SKIP_DOWNLOAD=true npm install && npx vite build
php artisan test
```

### Caveats specific to this box

- `PUPPETEER_SKIP_DOWNLOAD=true` avoids downloading Chromium. This turned out not to matter: `PaFormViewTest`'s three PDF-download tests still pass (41s each) in the full run.
- Running from `/mnt/c` (drvfs) is slow and the terminal tool caps a single foreground call at ~420s; `php artisan test` can exceed that. Prefix long runs with `--without-tty`, or run them as a background process and read the output when it lands.
- `.env`, `vendor/`, `node_modules/` and `public/build/` are all gitignored, so setting this up adds nothing to what you commit.

---

## 6. Follow-up round (2026-10-08, later) — permanent delete + audit coverage

Reported by the owner: *"when a page deletes an item, it goes to a bin, but was not delete-able, not even by superadmin"* + *"make sure the audit page able to log all the new functions (and existing ones)"*.

### Findings

| # | Finding | Evidence |
|---|---|---|
| D1 | **Nothing in the app could permanently delete anything.** Every one of the 14 soft-delete models had a "Tong" with only `Pulihkan` — no purge path existed for any role | `grep -rn "forceDelete\|force_delete" app/ routes/ resources/js/` → zero hits |
| D2 | The Tong entries written in the first round had a **null `event`** — `activity()->log('deleted')` sets the description only, not the event column, so the audit page rendered an unnamed badge and the event filter (a hardcoded `['created','updated','deleted','restored']`) could not match them | `AuditLogController.php:51`; `VerificationController::destroy()/restore()` |
| D3 | The **event filter was hardcoded**, so any event type outside those four was invisible to filtering | `AuditLogController.php:51` |
| D4 | The pipeline's own actions — **Receive and Verify** — wrote no master-item audit entry at all. (`Asset`/`Receiving` are `LogsActivity` models so the *outputs* were logged, but the item-level action was not.) | `MasterItem` has no `LogsActivity`; no `activity()` in `receive()`/`verify()` |
| D5 | `MasterItem` is one of the few models **without** `LogsActivity` — because it's bulk-imported, so a `created` entry per row would flood the log | `app/Models/MasterItem.php` |

### Changes

| Change | File |
|---|---|
| New `force_delete` permission, granted to **superadmin only** | `config/roles.php` |
| `DELETE /verification/{id}/force` → `forceDelete()`, gated `permission:force_delete` | `routes/web.php` |
| `forceDelete($id)` — captures a snapshot, force-deletes, then logs a `force_deleted` event with the subject + snapshot (MasterItem has no `LogsActivity`, so this is the only entry) | `VerificationController.php` |
| `can_force_delete` prop → `Padam Kekal` button in the Tong; the UI requires typing `PADAM` to confirm | `Verification/Index.jsx`, `VerificationController.php` |
| Explicit named events on every pipeline action: `received`, `verified`, `deleted`, `restored`, `force_deleted` (fixes D2) | `VerificationController.php` |
| Event filter now derived from the data (`distinct()->pluck('event')`) instead of hardcoded (fixes D3) | `AuditLogController.php` |
| Central `EVENT_STYLES` map — badge colours + BM labels for every event, used by both the table and the filter dropdown | `AuditLog/Index.jsx` |
| T19 (superadmin-only purge), T20 (Tong ops audited), T21 (receive/verify audited) | `VerificationPipelineTest.php` |

### Verified

| Check | Result |
|---|---|
| `php artisan test tests/Feature/VerificationPipelineTest.php` | ✅ 21 passed / 105 assertions |
| `npm run build` | ✅ 21.3s |
| Full `php artisan test` | ✅ **127 passed / 593 assertions / 0 failed** (202s) |

### Deliberately not done (see HANDOVER Technical Debt #16/#17)

- **Permanent delete for the other 13 trash bins.** The user's report was generic, but the pattern is now proven on Master Items; rolling it out is `DELETE /{resource}/{id}/force` + `forceDelete($id)` + one `Padam Kekal` button per trash table across 13 controllers and 13 JSX pages. Left for a follow-up rather than landing 26 unreviewed files at once.
- **`LogsActivity` on `MasterItem`** — would flood the audit log with one `created` entry per imported row (156 per import).
