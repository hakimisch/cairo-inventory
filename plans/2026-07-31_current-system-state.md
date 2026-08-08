# CAIRO Inventory — System State & Changes

**Date:** 31 July 2026
**Updated:** 31 July 2026 (afternoon) — Phase 0 + Phase 1 executed, see §10
**Branch:** main → origin/main
**Build:** `npm run build` ✓ clean

---

## 1. RBAC — 5 Roles

| Role | Priority | Permissions |
|------|----------|-------------|
| `superadmin` | 100 | `manage_users`, `verify`, `crud_assets`, `view_reports`, `view_admin` |
| `admin` | 80 | `verify`, `crud_assets`, `view_reports`, `view_admin` |
| `manager` | 60 | `view_reports` |
| `auditor` | 40 | `view_reports` |
| `user` | 20 | `verify` |

### Permission Gates

| Permission | What It Protects |
|------------|-----------------|
| `view_admin` | `/admin/*` (dashboard + user management) |
| `manage_users` | `/admin/users` CRUD (superadmin only) |
| `verify` | `POST /verification/{item}/verify` button |
| `crud_assets` | All DELETE routes, `POST/PUT /assets`, restore routes |
| `view_reports` | `/reports/*` (PA-4, PA-5, PA-7, PA-8, PA-12, PA-20) |

### First Login

- ~~Registration open at `/register` → defaults to `user` role~~ — **now gated**: `config('cairo.registration_enabled')` (env `REGISTRATION_ENABLED`, default **false**). When disabled the routes are not registered and the Register link auto-hides (`Route::has('register')`).
- `MustVerifyEmail` enforced; existing users bulk-verified
- Login → `DashboardController::index()`:
  - `role === 'admin'` → redirect to Admin Dashboard (`/admin/dashboard`)
  - All others → User Dashboard (`/dashboard`) showing "Aset Saya" (assets matched by custodian name)
- `superadmin` gets admin dashboard (sidebar checks `['admin','superadmin'].includes(user.role)`)
- Welcome page `/` is public (no auth middleware); no sidebar link to it

---

## 2. Data Pipeline — 3 Steps

```
/import  ──→  /verification  ──→  /receivings + /assets
  │                │                       │
  ▼                ▼                       ▼
master_items     Receive:                PA-1 (KEW.PA-1)
(status:         belum_diterima          created atomically
belum_diterima)  → diterima             with Asset at verify
                 (stores receiver        time
                 metadata as JSON        
                 in receive_meta)        
                 │
                 Verify:                
                 diterima → verified    
                 creates PA-1 +          
                 Asset (PA-2/PA-3)       
```

### Step 1 — `/import`

- Accepts: PDF (OCR via `OcrService`), CSV, Excel, or pasted text
- Flexible column header matching (Malay/English aliases, brand-model split heuristic)
- Preview editable table → confirm → inserts into `master_items` with `status=belum_diterima`
- Controller: `ImportController`
- Service: `MasterItemImportService`
- Permission: none (all authenticated users)

### Step 2 — `/verification`

Two tabs:
- **Pipeline tab**: Paginated `master_items`, filterable by status/category/date. Two actions:
  - **Receive** (no permission gate): `belum_diterima` → `diterima`. Stores receiver/DO metadata as JSON in `master_items.receive_meta`. Does NOT create PA-1.
  - **Verify** (`permission:verify` gate): `diterima` → `verified`. Reads `receive_meta`, creates PA-1 `Receiving` + `Asset`(s) atomically in one DB transaction.
- **Registry tab**: Merged `master_items` + `assets` in one searchable table.

Threshold: `config('cairo.asset_threshold')` (RM 2,000)
- `>= 2000` → `fixed_asset` (PA-3 / KEW.PA-2)
- `< 2000` → `inventory` (PA-2 / KEW.PA-3)

Per-S/N split: When `quantity > 1` and multiple serial numbers, creates one asset per S/N.

Scanner: Embedded ScanPanel at `?scan=1` — searches `master_items.serial_numbers` + `assets.serial_number` + `assets.asset_tag`.

Controller: `VerificationController`
Scanner API: `ScanController` (`POST /scanner/lookup`, `POST /scanner/scan`)

### Step 3a — `/receivings` (PA-1)

- Index of `Receiving` records with inline CRUD
- KEW.PA-1 PDF download per record
- Trash tab with restore (admin/superadmin)
- Delete gated: `permission:crud_assets`
- Controller methods on `AssetController`

### Step 3b — `/assets` (PA-2 / PA-3)

- Index of `Asset` records with filter tabs (Semua / Aset Tetap / Inventori / Tong)
- Each asset links to its KEW.PA-2 or KEW.PA-3 detail hub
- Detail hub bundles inline CRUD for: placements, inspections, maintenances, transfers, disposals, loss reports, damage reports, upgrades
- KEW.PA-2/PA-3 PDF download, barcode label download
- Trash tab with restore (admin/superadmin)
- Delete gated: `permission:crud_assets`
- Controller: `AssetController`

---

## 3. Sidebar Navigation

6 sections, 34 KEW.PA forms accessible:

### Utama
- ◈ Direktori KEW.PA (`/kewpa`)
- ▣ Dashboard or Admin Dashboard (role-dependent)
- ! Log Audit (`/audit-log`)

### Logistik & Penerimaan
- ⬆ Import PDF / CSV / Excel (`/import`)
- ◈ Verification Pipeline + Registry (`/verification`)
- ◫ PA-1 Senarai Penerimaan (`/receivings`)
- ◈ PA-2/3 Pendaftaran Aset (`/assets`)

### Pergerakan & Pemeriksaan
- ↗ PA-6 Daftar Pergerakan
- ! PA-9 Aduan Kerosakan
- ◈ PA-9A Pinjaman Aset
- ◎ PA-10 Pemeriksaan Aset
- ◈ PA-11 Pemeriksaan Inventori
- ▤ PA-12 Perakuan Tahunan

### Penyelenggaraan
- ⚙ PA-13 Senarai Penyelenggaraan
- ◈ PA-14 Daftar Penyelenggaraan

### Pelupusan & Jualan
- ◈ PA-16 Pelupusan Kenderaan
- ◈ PA-15 Jawatankuasa Pelupusan
- ◈ PA-17/18/19 Pelupusan _(collapsible)_
- ▤ PA-20 Laporan Pelupusan
- ◈ PA-21→27A Jualan Aset _(collapsible)_

### Kehilangan
- ! PA-28→32 Kehilangan _(collapsible: PA-28, PA-31, PA-32)_

### Laporan Tahunan _(admin/superadmin/manager/auditor)_
- ▤ PA-4 Harta Tetap
- ▤ PA-5 Inventori
- ▤ PA-7 Kedudukan Aset
- ▤ PA-8 Lap. Tahunan

### Sistem _(admin/superadmin)_
- 👤 Pengurusan Pengguna (superadmin only)
- ◈ Pembekal (Suppliers)

---

## 4. Trash Bin — 13 Modules

Every CRUD index page has a `Tong (N)` toggle button (admin/superadmin only) that reveals soft-deleted records with `Pulihkan` (restore) buttons. All 19 tables have `deleted_at` (SoftDeletes). All DELETE routes gated behind `permission:crud_assets`.

| # | Page | Restore Route | Columns in Trash |
|---|------|--------------|------------------|
| 1 | Assets | `assets.restore` | Tag, Nama, Kategori, Dipadam Pada |
| 2 | Receivings | `receivings.restore` | No. Terima, Pembekal, Dipadam Pada |
| 3 | Transfers (PA-6) | `assets.transfers.restore` | Aset, Dari, Ke |
| 4 | Damage Reports (PA-9) | `assets.damage-reports.restore` | Aset, Kerosakan, Tarikh |
| 5 | Placements (PA-9A) | `assets.placements.restore` | Aset, Peminjam, Lokasi |
| 6 | Inspections (PA-10) | `assets.inspections.restore` | Aset, Tarikh, Status |
| 7 | Maintenances (PA-13) | `assets.maintenances.restore` | Aset, Penerangan, Tarikh |
| 8 | Disposals (PA-17) | `assets.disposals.restore` | Aset, Kaedah, Tarikh |
| 9 | Loss Reports (PA-28) | `assets.loss-reports.restore` | Aset, Tarikh, Status |
| 10 | Vehicle Disposals (PA-16) | `assets.vehicle-disposal.restore` | Aset, No. Plat, Tarikh |
| 11 | Disposal Sales | `disposal-sales.restore` | Rujukan, Jenis, Tarikh |
| 12 | Suppliers | `suppliers.restore` | Nama, No. Pendaftaran |
| 13 | Committee Appointments | `committee-appointments.restore` | Nama, Jawatan, Tarikh |

---

## 5. KEW.PA Form Coverage — 34 Forms

| Form | Name | PDF |
|------|------|:--:|
| PA-1 | Borang Penerimaan Aset | ✅ |
| PA-2 | Daftar Harta Tetap | ✅ |
| PA-3 | Kad Daftar Inventori | ✅ |
| PA-4 | Senarai Harta Tetap | ✅ |
| PA-5 | Senarai Inventori | ✅ |
| PA-6 | Daftar Pergerakan Aset | ✅ |
| PA-7 | Laporan Kedudukan Aset | ✅ |
| PA-8 | Laporan Aset Tahunan | ✅ |
| PA-9 | Aduan Kerosakan Aset | ✅ |
| PA-9A | Borang Pinjaman Aset | ✅ |
| PA-10 | Laporan Pemeriksaan Aset | ✅ |
| PA-11 | Pemeriksaan Inventori | ✅ |
| PA-12 | Perakuan Pemeriksaan Tahunan | ✅ |
| PA-13 | Rekod Penyelenggaraan | ✅ |
| PA-14 | Daftar Penyelenggaraan | ✅ |
| PA-15 | Lantikan JK Pemeriksa Pelupusan | ✅ |
| PA-16 | Perakuan Pelupusan Kenderaan | ✅ |
| PA-17 | Permohonan Pelupusan | ✅ |
| PA-18 | Sijil Pemusnahan Aset | ✅ |
| PA-19 | Sijil Pelupusan Aset | ✅ |
| PA-20 | Laporan Pelupusan Tahunan | ✅ |
| PA-21 | Notis Tawaran (Tender) | ✅ |
| PA-22 | Borang Tawaran (Tender) | ✅ |
| PA-23 | Pembukaan Tender | ✅ |
| PA-24 | Notis Sebutharga | ✅ |
| PA-25 | Borang Sebutharga | ✅ |
| PA-26 | Pembukaan Sebutharga | ✅ |
| PA-27 | Notis Lelongan | ✅ |
| PA-27A | Senarai Aset Lelongan | ✅ |
| PA-28 | Laporan Awal Kehilangan | ✅ |
| PA-29 | Lantikan JK Penyiasat Kehilangan | ✅ |
| PA-30 | Laporan Akhir Kehilangan | ✅ |
| PA-31 | Sijil Hapuskira | ✅ |
| PA-32 | Laporan Tindakan Kehilangan | ✅ |

---

## 6. Changes Made — 31 July 2026

### Phase 1: Pipeline Stabilization

| # | Change | Files |
|---|--------|-------|
| 1a | Extracted RM 2,000 threshold to `config('cairo.asset_threshold')` — single source of truth | `config/cairo.php` (new), `VerificationController.php`, `AssetController.php` |
| 1b | Moved PA-1 creation from `receive()` to `verify()` — receiver metadata stored as JSON in `master_items.receive_meta`, PA-1 + Asset created atomically at verify time | `VerificationController.php`, `MasterItem.php`, `database/migrations/2026_07_31_000001` |
| 1c | Removed dead `AdminMiddleware` alias, merged two duplicate `auth+verified` route groups into one | `bootstrap/app.php`, `routes/web.php` |

### Phase 2: Cleanup

| # | Change | Files |
|---|--------|-------|
| 2a | Removed `/items` and `/scanner` permanent redirect routes (absorbed into `/verification`) | `routes/web.php` |
| 2b | Removed Scanner S/N sidebar link (already in Verification page), deduplicated committee-appointments sidebar entry | `AuthenticatedLayout.jsx` |

### RBAC Fix

| # | Change | Files |
|---|--------|-------|
| 3 | Added `verify` permission to `user` role — self-registered users can now participate in the pipeline | `config/roles.php` |

### Trash Bin (Soft-Delete Restore)

| # | Change | Files |
|---|--------|-------|
| 4a | Added `restore()` methods + `trashed` data + restore routes to 11 controllers | 11 controller files + `routes/web.php` |
| 4b | Added trash tab UI (Tong toggle + trashed table + Pulihkan button) to 11 JSX pages | 11 JSX files |

### Welcome Page

| # | Change | Files |
|---|--------|-------|
| 5 | Updated from 26 stale entries → 34 KEW.PA forms with correct Malay names | `Welcome.jsx` |

### Total: 27 files modified, 2 files created

---

## 7. Tech Stack

- **Backend**: Laravel 11, PHP 8.x, PostgreSQL
- **Frontend**: React 18, Inertia.js, Vite
- **PDF**: Spatie Laravel-PDF + Browsershot (Chromium)
- **OCR**: Python bridge (`OcrService`)
- **Activity Log**: spatie/laravel-activitylog
- **Barcode**: picqer/php-barcode-generator
- **Media**: spatie/laravel-medialibrary
- **Environment**: WSL, Laravel Herd (Windows), PostgreSQL on Windows

---

## 8. Known Issues

| # | Severity | Issue | Status |
|---|----------|-------|--------|
| 1 | 🟡 | `acceptReceiving()` on AssetController still uses hardcoded `>= 2000` threshold (line 61) — separate from the pipeline verify flow, handles manual PA-1 acceptance | OPEN — Phase 2 item 15 |
| 2 | 🟢 | Welcome page has no sidebar link — authenticated users must type `/` to see it | OPEN |
| 3 | 🟢 | Register page has no role selector — all self-registrations default to `user` | SUPERSEDED — registration disabled by default (`REGISTRATION_ENABLED=false`) |
| 4 | 🟢 | No invite-only registration gating (open to public) | RESOLVED — gated via config; invite/allowlist still a future option |
| 5 | 🟡 | OCR `total_amount` still None on scanned DO docs — Tesseract text contains no JUMLAH/TOTAL/RM markers (regex fix verified; capture quality is the limit) | OPEN — OCR follow-up |
| 6 | 🟢 | OCR supplier capture includes noise (`[^\n]*` grabs to EOL, e.g. `Dotcom Telecommunications Sdn BAG Â« …`) — tighten to `[A-Za-z0-9 .&()\-]+` | OPEN — OCR follow-up |
| 7 | 🟢 | JADUAL HARGA multi-page scan takes 10+ min to OCR | OPEN — slow-path; consider page-range option |
| 8 | 🟡 | 2 stale tests failing: `EndToEndPipelineTest` (asserts PA-1 at Receive — old pipeline) + `RbacTest > staff cannot verify` (pre-RBAC-fix) — suite currently 116 pass / 2 fail / 482 assertions | OPEN — Phase 3 item 17 |

---

## 9. Git Status

```
M  README.md                                  (login table + pipeline description fixed)
M  app/Console/Commands/ImportPdfData.php     (import result shape: imported/skipped)
M  app/Http/Controllers/AssetController.php   (rejectReceiving redesign, tag via service)
M  app/Http/Controllers/ImportController.php  (import result shape)
M  app/Http/Controllers/PdfImportController.php
M  app/Http/Controllers/ScanController.php    (off-by-one, generator, split guard, config threshold)
M  app/Http/Controllers/VerificationController.php  (PA-1 + tags via generator)
M  app/Http/Middleware/HandleInertiaRequests.php    (flash → Inertia props)
M  app/Models/Receiving.php                   (rejection_reason, rejected_at fillable)
M  app/Services/MasterItemImportService.php   (transaction + dedup + skipped counts)
M  bin/cairo-ocr                              (7 regex lines fixed, qty cross-check)
M  phpunit.xml                                (REGISTRATION_ENABLED=true for tests)
M  resources/js/Components/Toast.jsx          (no-emoji icons ✓ ✕ ! i)
M  resources/js/Layouts/AuthenticatedLayout.jsx  (FlashToaster bridge)
M  routes/auth.php                            (registration gated)
M  routes/web.php                             (/_dev/dedup-* routes removed)
M  tests/Feature/MasterItemLifecycleTest.php  (dedup tests → artisan command)
?? app/Console/Commands/DedupMasterItems.php  (NEW — cairo:dedup-master-items)
?? app/Console/Commands/DedupSuppliers.php    (NEW — cairo:dedup-suppliers)
?? app/Console/Commands/ResetPipeline.php
?? app/Models/Location.php
?? app/Services/AssetTagGenerator.php         (NEW — unified AST-/REC- generation)
?? config/cairo.php                           (asset_threshold, receive_no_prefix, registration_enabled)
?? database/migrations/2026_07_24_000002_create_locations_table.php
?? database/migrations/2026_07_31_000001_add_receive_meta_to_master_items.php
?? database/migrations/2026_07_31_000002_add_rejection_fields_to_receivings.php  (NEW — applied)
?? database/seeders/LocationSeeder.php
```

> NOTE: `ocr_total_debug_tmp.py` (OCR debug helper) is still at repo root — pending deletion approval. All changes uncommitted.

---

## 10. Phase 0 + Phase 1 — Executed (31-Jul afternoon)

From the compiled assessment (`C:\Hakimi\Stuff\assess\COMPILED_ASSESSMENT_2026-07-31.md` — ACTION_PLAN 15/15 findings verified). Suite baseline after both phases: **116 pass / 2 fail (482 assertions)** — the 2 fails are pre-existing stale tests (see Known Issue #8). Build green.

### Phase 0 — Security & crash fixes ✅

| # | Change | Files |
|---|--------|-------|
| 1 | `/_dev/dedup-master-items` + `/_dev/dedup-suppliers` ungated GET routes **removed** → converted to artisan commands with `--dry-run` / `--force` + confirm | `routes/web.php`, `app/Console/Commands/DedupMasterItems.php` (NEW), `DedupSuppliers.php` (NEW) |
| 2 | `kewpa9a()` null deref fixed (`!$placement \|\| !$placement->exists`) — matches sibling `downloadKewpa9a` | `AssetController.php` |
| 3 | Self-registration gated: `config('cairo.registration_enabled')` (default **false**) → routes unregistered, Register link auto-hides; tests set `REGISTRATION_ENABLED=true` in phpunit.xml | `config/cairo.php`, `routes/auth.php`, `phpunit.xml`, `.env.example` |
| 4 | Session flashes (`success`/`error`) now shared to Inertia + `FlashToaster` auto-renders them as toasts — permission denials finally visible; toast icons de-emoji'd (✓ ✕ ! i) | `HandleInertiaRequests.php`, `AuthenticatedLayout.jsx`, `Toast.jsx` |
| 5 | README login table corrected (superadmin=`user@gmail.com`, admin=`admin@cairo.utm`, staff=`ibrahim@cairo.utm`; `admin2@cairo.utm` never existed) + pipeline description (PA-1 at **Verify**, not Receive) | `README.md` |

### Phase 1 — Data integrity ✅

| # | Change | Files |
|---|--------|-------|
| 6 | OCR double-backslash regexes fixed — **32 bad escapes across 7 raw strings** (L617-618 supplier, L643-646 total, L1904 generic fallback); audit re-ran: 0 remaining (L184-185 `C:\Program Files` intentionally kept) | `bin/cairo-ocr` |
| 7 | `rejectReceiving()` no longer creates a fake zero-value `REJ-` `disposed` asset — records `rejection_reason` + `rejected_at` on the Receiving (new migration, applied; live DB had 0 REJ assets to clean) | `AssetController.php`, `Receiving.php`, migration `2026_07_31_000002` |
| 8 | Import service: **DB::transaction** (no partial imports) + **dedup guard** (item_no + normalized description) + returns `['imported','skipped']`; all 3 callers show "N duplicates skipped" | `MasterItemImportService.php`, `ImportController.php`, `PdfImportController.php`, `ImportPdfData.php` |
| 9 | Scan off-by-one fixed — `verifyFromScan`/`receiveFromScan` now return the **actual** created tag/number (captured inside the transaction), not the next sequence value | `ScanController.php` |
| 10 | **`AssetTagGenerator` service** — one parsing rule (`SUBSTR`), `Cache::lock`, unified `AST-YYYYMMDD-NNNNNN` / `REC-YYYYMMDD-NNNN`; applied to verify, scanner, `acceptReceiving`, `storeReceiving`; legacy `CAIRO/…` + `RC-` formats retired. Bonus: scanner split-guard unified with pipeline (`quantity > 1`), scanner threshold → config | `app/Services/AssetTagGenerator.php` (NEW), `VerificationController.php`, `ScanController.php`, `AssetController.php` |
| 11 | OCR qty corruption fixed — qty × unit_price cross-check against total_price (>5% divergence → recompute from totals, bounded 1-999) | `bin/cairo-ocr` |
| 12 | **Supplier dedup EXECUTED on live DB**: 5 → **2** suppliers (Dotcom id 1, SNS id 3); 156 master_items re-pointed (64→1, 92→3); verified | `php artisan cairo:dedup-suppliers --force` |

### OCR regression results (real PDFs)

| Doc | type | supplier | total | items |
|-----|------|----------|-------|-------|
| Acknowledment Goods DOTCOM.pdf | do | ✅ extracted (with OCR noise — Known Issue #6) | ❌ None — no JUMLAH/TOTAL/RM in Tesseract text | 22 |
| DO SNS.pdf | do | ❌ None (no DOTCOM / NAMA-DAN-ALAMAT header) | ❌ None — same capture limit | 45 |
| JADUAL HARGA DOTCOM.pdf | — | (OCR too slow — 10+ min, aborted) | | |

### Remaining phases

> **Full detail + execution order: `plans/2026-07-31_future-work-backlog.md`** (self-contained, written at session close).

- **Phase 2** (clerk UX): confidence enforcement at import; `acceptReceiving` validation; threshold → config; verify-button permission sync (`Verification/Index.jsx:891` gates on hardcoded roles while `user` role now has `verify`).
- **Phase 3**: 2 stale tests (Known Issue #8); README overhaul (counts/DB-state/new files); Jul-27 asset fate (14 assets from qty=1 item); dead-code cleanup (ItemsController, orphaned Scanner pages, Adminmiddleware.php, unused seeders).
- **Phase 4** (deferred): DB-level pagination; `orWhereYear` grouping.
- **OCR follow-ups**: total_amount capture limit (F1), supplier noise (F2), slow multi-page scans (F3).
- **Housekeeping**: delete `ocr_total_debug_tmp.py` (H1); commit the Phase 0/1 change set (H2); `REGISTRATION_ENABLED` for dev (H3); seed `locations` (H4).
