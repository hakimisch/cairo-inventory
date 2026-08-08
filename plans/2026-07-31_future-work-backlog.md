# CAIRO Inventory — Future Work Backlog

**Date:** 31 July 2026 (session close)
**Source:** `C:\Hakimi\Stuff\assess\ACTION_PLAN.md` (15/15 findings verified), `COMPILED_ASSESSMENT_2026-07-31.md`, and this session's execution record in `plans/2026-07-31_current-system-state.md` §10.

**Baseline at close (verified):** suite = **116 pass / 2 fail / 482 assertions** (2 fails = stale tests, Item 17 below); `npm run build` ✅; live DB: 2 suppliers (canonical), 156 master_items, 15 assets (pipeline-exercised), 2 receivings, 0 demo data; migration `2026_07_31_000002` applied.

**Run commands (WSL + Herd):**
```bash
powershell.exe -Command "C:\Users\cairo\.config\herd\bin\php.bat artisan test"
powershell.exe -Command "C:\Users\cairo\.config\herd\bin\php.bat artisan <cmd>"
npm run build
```

---

## Phase 2 — Clerk UX & consistency (≈half day)

### 13. Enforce OCR confidence at import
- **What:** confidence is computed in `bin/cairo-ocr` main() (L2014-2030, shallow heuristics: desc >15 chars, brand, item_code) but nothing downstream acts on it — a 0-confidence extraction imports silently.
- **Files:** `bin/cairo-ocr` (keep calc), `ImportController.php` / `PdfImportController.php` (pass confidence to preview), `resources/js/Pages/Import/Index.jsx` + `PdfImport/Index.jsx` (show score; warn + require review below threshold ~50; log low-confidence imports).
- **Verify:** feed a garbage PDF → preview shows warning and blocks/requires acknowledgment.

### 14. Validate `acceptReceiving()` fields
- **What:** `AssetController.php:51-105` validates only `photo`; category/campus/location/custodian_name/warranty/model/brand/serial_number/saga_id/budget_vot flow raw from the request.
- **Fix:** mirror `update()` rules (L332-362); use `$request->safe()->only([...])`.
- **Verify:** POST garbage values → 422, nothing persisted.

### 15. Threshold → config (Known Issue #1)
- **What:** `AssetController.php:61` hardcodes `$price >= 2000`; everything else uses `config('cairo.asset_threshold', 2000)`.
- **Fix:** one line — `$price >= config('cairo.asset_threshold', 2000)`.
- **Verify:** set config 5000, accept RM3000 receiving → classified `inventory`.

### 16. Sync Verify-button gating to permission
- **What:** `config/roles.php` grants `user` role `verify` (RBAC Fix #3, morning session), but `Verification/Index.jsx:891` hides the Verify button for non-`['admin','superadmin']` — staff can verify via direct POST but see "Menunggu pengesahan pentadbir" with no button. UI/backend mismatch.
- **Fix:** pass `can_verify` from `VerificationController::index()` (e.g. `auth()->user()->can('verify')` or check `config("roles.{role}.can")`) and gate the button on that prop.
- **Verify:** log in as `user` role → Verify button visible on `diterima` items.

---

## Phase 3 — Tests & documentation (≈half day)

### 17. Fix the 2 stale tests (make suite green)
- **`EndToEndPipelineTest.php:88-95`** — asserts `Receiving` exists right after `receive()` (old pipeline: PA-1 at Receive). PA-1 is now created at **Verify** (`VerificationController::verify()`). Move the Receiving assertions (L88-95) to **after** the verify step (after L108), and fix the step-4 comment "PA-1 created".
- **`RbacTest.php:123-135` `test_staff_cannot_verify_items`** — asserts staff is blocked (pre-RBAC-fix). `user` role now HAS `verify`. Either:
  - (a) rename/rewrite to `test_staff_can_verify_items` — assert status → `verified`, asset created, redirect back; or
  - (b) if the RBAC fix was unintended, remove `verify` from `config/roles.php` `user` entry first.
  - Also remove the stale comment "AdminMiddleware redirects non-admin" (middleware is dead).
- **Verify:** `artisan test` → **118 passed / 0 failed**.

### 18. README overhaul (doc debt from the audit)
- **Counts:** 170 → **156** master items; 56 → **58** migrations; 20 → **21** models (+Location); 15 → **16** components; "7-Section Sidebar" → **8** NavSections; assets "38 columns" → 41; tests "117/538" → **118 tests / ~485 assertions** (2 currently failing).
- **DB state table:** assets **15** (not 0/10), receivings **2** (not 0/12), all 14 demo tables **0** (DB was reset; `DatabaseSeeder` would re-seed), media **1** (not 2), activity_log ~988 (≈fine), `locations` 0 rows.
- **Remove stale claims:** "suppliers 5 (needs dedup: 3 canonical)" → **2 canonical** (dedup executed); "Pipeline ready but awaiting production asset entry" → **exercised 2026-07-27** (15 assets, 2 PA-1s); "PA-4/5/8 React-only, no Blade" → blades exist (`kewpa4/5/8.blade.php`) + Future Considerations item "add PA-4/5/8 blades" → drop; "AdminUserSeeder pre-seeds all 5 accounts" → actually creates only `admin@cairo.test`.
- **Add new files:** `config/cairo.php` (threshold/prefixes/registration), `AssetTagGenerator` service, `DedupMasterItems`/`DedupSuppliers` commands, `ResetPipeline` command, `Location` model/table/seeder, `MasterItemSeeder` (156), `receive_meta` migration, `AddRejectionFields` migration, `REGISTRATION_ENABLED` env (default off).
- **Verify:** every claim in the README traces to a file/DB value.

### 19. Decide fate of the 2026-07-27 pipeline assets
- **What:** 14 assets (ids 1-14, `AST-20260727-000001…014`) were created per-S/N from a **qty=1** master item (mi=3, "Single Core", 14 S/Ns) under the old split logic. Current `verify()`/scanner code creates ONE asset with concatenated S/Ns when qty=1. Source data also contains duplicate S/N `BZMKCH4` (twice in the same list).
- **Options:** (a) collapse 14 → 1 asset (concatenate S/Ns, sum quantity), (b) keep as-is if the 14 S/Ns are legitimately 14 separate units (verify against the source DO).
- **Verify:** registry + PA-2/PA-3 reflect the business rule consistently.

### 20. Dead-code cleanup
- **Remove:** `ItemsController.php` (imported in `routes/web.php:31`, zero routes) + `resources/js/Pages/Items/Index.jsx`; orphaned `resources/js/Pages/Scanner/Index.jsx` + `History.jsx` (scanner UI lives in Verification; only `/scanner/lookup|scan` API routes exist); `app/Http/Middleware/Adminmiddleware.php` (no alias registered — `bootstrap/app.php` registers only `role` + `permission`); unreferenced `DummyKewpaSeeder.php` + `DashboardTestSeeder.php`.
- **Verify:** `git grep` each name → zero references; `artisan route:list` clean.

---

## Phase 4 — Performance (defer until data grows past a few thousand)

### 21. DB-level pagination
- `VerificationController::index()` registry tab loads ALL master_items + assets into memory (L104-175); `AssetController::index()` does `get()` + 7 count queries (L13-29).
- **Fix:** paginate at DB level (UNION or separate paginated queries); lazy-load the registry tab via AJAX; cache stats or use one conditional-aggregate query.

### 22. `orWhereYear` precedence
- `ReportController.php:196-199` (kewpa12) + kewpa20 equivalent: `->whereYear(...)->orWhereYear(...)` ungrouped. Currently **benign** (no preceding where), latent footgun if a filter is added.
- **Fix:** wrap in `->where(fn ($q) => $q->whereYear(...)->orWhereYear(...))`.

---

## OCR follow-ups (found during Phase 1 verification)

| # | Issue | Proposed fix |
|---|-------|--------------|
| F1 | `total_amount` still None on scanned DO docs — Tesseract text contains **no** JUMLAH/TOTAL/GRAND/RM markers (verified line-by-line on Acknowledment Goods DOTCOM; last page OCR'd as garbage fragments `si`/`6`/`a‘`) | Multi-line pattern (label line → amount line); improve OCR preprocessing (bitonal/PSM options); confirm against a digital-text PDF where the total is known |
| F2 | Supplier capture includes OCR noise (`r'(DOTCOM\s+TELECOMMUNICATIONS[^\n]*)'` grabs to EOL: "Dotcom Telecommunications Sdn BAG Â« 1.::co10:19486719-Â«)") | Tighten capture class to `[A-Za-z0-9 .&()\-]+` in `_extract_supplier()` (bin/cairo-ocr L617-618) |
| F3 | JADUAL HARGA DOTCOM.pdf takes 10+ min to OCR (multi-page scan) — blocks the PHP worker when invoked via web | Add `--max-pages`/page-range option; consider async processing or Tesseract tuning |

---

## Housekeeping

| # | Item | Action |
|---|------|--------|
| H1 | `ocr_total_debug_tmp.py` at repo root (OCR debug helper from this session) | Delete (needs approval — rm was blocked) |
| H2 | All Phase 0/1 changes uncommitted (35+ modified, 9 new) | Commit in logical groups: Phase 0 (security), Phase 1 (data integrity), or per-feature; `public/build` regenerated |
| H3 | `REGISTRATION_ENABLED` not present in live `.env` → registration is **off** | If dev demo needs `/register`, set `REGISTRATION_ENABLED=true` in `.env` (documented in `.env.example`) |
| H4 | `locations` table exists but 0 rows (`LocationSeeder` never ran this cycle) | `php artisan db:seed --class=LocationSeeder` if the locations lookup is needed |

---

## Suggested execution order (next session)

1. **Item 17** (stale tests) — 30 min, makes the suite green and unblocks everything else.
2. **Items 14, 15, 16** (validation + threshold + verify button) — ~1 h, all small and independent.
3. **Item 13** (confidence) + OCR follow-ups F1/F2 — half day, needs the slow PDFs (run in background).
4. **Items 18-20** (README, asset fate, dead code) — half day, docs + cleanup.
5. **Housekeeping H1-H4** — anytime (H2 before closing the next session).
6. **Phase 4** — only when asset count approaches a few thousand.
