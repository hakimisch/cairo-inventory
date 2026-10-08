# CAIRO Inventory — Handover Document

> **Project handover** — for the next developer continuing CAIRO Inventory development after the original author's departure.
>
> **Author:** Hakimi (hakimisch) — Last updated: 2026-08-08
> **Stack:** Laravel 12 + React 18 (Inertia.js SPA) + PostgreSQL 18
> **Repo:** https://github.com/hakimisch/cairo-inventory.git
> **Deployment:** AWS Elastic Beanstalk + RDS PostgreSQL + S3

---

## Table of Contents

1. [Quick Start — Clone & Run](#1-quick-start--clone--run)
2. [Dev Environment Quirks](#2-dev-environment-quirks)
3. [System Architecture Overview](#3-system-architecture-overview)
4. [Database Schema Cheat Sheet](#4-database-schema-cheat-sheet)
5. [Key Code Paths — Where To Look](#5-key-code-paths--where-to-look)
6. [Current State — Snapshot](#6-current-state--snapshot)
7. [Uncommitted Changes Basket](#7-uncommitted-changes-basket)
8. [Known Issues & Technical Debt](#8-known-issues--technical-debt)
9. [Roadmap — Prioritised Next Steps](#9-roadmap--prioritised-next-steps)
10. [Deployment](#10-deployment)
11. [Test Suite](#11-test-suite)
12. [Reference — Key CLI Commands](#12-reference--key-cli-commands)
13. [Sources of Truth](#13-sources-of-truth)

---

## 1. Quick Start — Clone & Run

### Prerequisites

| Requirement | Version | Notes |
|-------------|---------|-------|
| PHP | ≥ 8.2 | Extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, PostgreSQL, Tokenizer, XML |
| Composer | ≥ 2.x | |
| Node.js | ≥ 18 | For Vite + React frontend |
| PostgreSQL | ≥ 15 | |
| Python | ≥ 3.10 | For OCR pipeline only |
| Tesseract OCR | ≥ 5.0 | For OCR pipeline only |
| Chromium | latest | For PDF generation via Browsershot |

### Step-by-Step

```bash
# 1. Clone the repo
git clone https://github.com/hakimisch/cairo-inventory.git
cd cairo-inventory

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment configuration
cp .env.example .env
# Edit .env — set DB_CONNECTION=pgsql, your DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD
# On AWS RDS: host = cairo-inventory-db.c3k8m8ow8w3g.ap-southeast-1.rds.amazonaws.com
# On local: host = 127.0.0.1

# 5. Generate application key (if .env.example's key was lost)
php artisan key:generate

# 6. Create the database and run migrations + seeders
php artisan migrate --seed

# 7. Install Python OCR dependencies (optional)
pip install -r requirements.txt

# 8. Build frontend assets
npm run build

# 9. Start dev server
php artisan serve
# Or use Laravel Herd / Valet for production-like setup
```

### What to verify after setup

| Check | Command | Expected |
|-------|---------|----------|
| Migrations ran | `php artisan migrate:status` | All 59 migrations show "Y" (ran) |
| Seed users exist | Check DB: `SELECT email, role FROM users;` | 7 users across 5 roles |
| Master items exist | Check DB: `SELECT COUNT(*) FROM master_items;` | 156 rows |
| Build is clean | `npm run build` | ✓ built in ~18s, 0 errors |
| Tests pass | `php artisan test` | 127 passed / 0 failed / 593 assertions (2026-10-08) |

### Potential Gotchas

1. **PHP on Windows + WSL**: The project was developed on WSL2 with Laravel Herd on Windows. PHP is installed via Herd and lives at `C:\Users\{user}\AppData\Local\Programs\Herd\bin\php.exe`. Commands like `php artisan` need to be run either in WSL's terminal (if PHP is installed in WSL) or via PowerShell pointing to the Herd binary.

2. **PostgreSQL connection**: The `.env.example` has `DB_HOST=127.0.0.1 / cairo-inventory-db.c3k8m8ow8w3g.ap-southeast-1.rds.amazonaws.com` — pick one. On WSL, PostgreSQL running on Windows can be reached at `127.0.0.1` from WSL (Windows port forwards to WSL).

3. **APP_KEY**: If lost, `php artisan key:generate` regenerates it — this invalidates encrypted sessions/cookies but database data is unaffected.

4. **Storage symlink**: Run `php artisan storage:link` after fresh clone to create `public/storage` → `storage/app/public`.

5. **PDF generation**: Chromium path must be set in `.env`:
   ```
   LARAVEL_PDF_CHROME_PATH=/path/to/chrome
   LARAVEL_PDF_NO_SANDBOX=true
   ```
   On Herd/WSL, find Chrome with `which chromium-browser` or `which google-chrome`.

6. **npm ERR! on build**: If Vite throws dependency errors, delete `node_modules/` and `package-lock.json`, then `npm install && npm run build`.

---

## 2. Dev Environment Quirks

The original dev environment had some unique setup details that took time to figure out:

### WSL (Ubuntu) + Laravel Herd (Windows)

```
Windows 11
├── Laravel Herd ── nginx (port 80/443)
├── PostgreSQL 18 (localhost:5432)
│     Databases: cairo_inventory, postgres
└── WSL2 (Ubuntu 24.04)
    ├── Code editor + git
    ├── Python venv + Tesseract
    ├── Node.js + npm (for build only)
    └── Access to Windows filesystem at /mnt/c/
```

- **PHP is not installed in WSL** — `php artisan` commands are run from Windows PowerShell using Herd's PHP binary
- **PostgreSQL is on Windows** — WSL connects to `127.0.0.1:5432` (Windows forwards localhost to WSL)
- **OCR pipeline** (`bin/cairo-ocr`) runs from WSL Python — it reads PDFs from Windows filesystem at `/mnt/c/...`
- **Vite dev server** runs in WSL and is accessible from Windows browser at `http://localhost:5173`
- **npm run build** runs in WSL — output goes to `public/build/`

### Running Artisan Commands From WSL

If PHP isn't in WSL, use PowerShell:

```powershell
# PowerShell from WSL:
powershell.exe -Command "& 'C:\Users\miisch\AppData\Local\Programs\Herd\bin\php.exe' artisan migrate --seed"

# Or add an alias in ~/.bashrc:
alias php-heard='powershell.exe -Command "& \"C:\Users\miisch\AppData\Local\Programs\Herd\bin\php.exe\""'
```

### Python + Tesseract for OCR

```bash
# Python setup (WSL)
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
# Tesseract is already installed in WSL: which tesseract → /usr/bin/tesseract
```

---

## 3. System Architecture Overview

```
                     Browser — React SPA (65 page components)
                              │
                     Inertia.js (no API layer)
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       Laravel Backend                            │
│                                                                  │
│  Routes → Controllers (37) → Eloquent Models (21) → PostgreSQL   │
│                              │                                   │
│            ┌─────────────────┼─────────────────┐                │
│            ▼                 ▼                  ▼                │
│     PDF Engine          OCR Pipeline       Services              │
│    (35 Blade tpls)   (2,080-line Python)   (Import, OCR,         │
│     Browsershot        Tesseract           AssetTagGenerator)    │
│                                                                  │
│  Middleware: auth, verified, PermissionMiddleware, RoleMiddleware │
└─────────────────────────────────────────────────────────────────┘
```

### Data Flow — Procurement Pipeline (the critical path)

```
Supplier PDF/CSV
       │
       ▼  OCR (bin/cairo-ocr) or CSV/Excel parser
Editable Preview Grid (/import or /pdf-import)
       │
       ▼  Confirm Import
master_items (156 items, status: belum_diterima)
       │
       ▼  /verification page
Receive ──→ diterima ──→ receiver metadata stored as JSON (receive_meta)
  │
  ▼  (permission:verify — superadmin/admin/user)
Verify ──→ verified ──→ PA-1 (receivings) + Assets (assets table, per-S/N split) created atomically
  │
  ▼
KEW.PA-2 / PA-3 forms with individual S/Ns
```

### Data Flow — Phase 1 Lifecycle (the scaffolded modules)

```
Assets ──→ Movements (PA-6) ──→ Loans (PA-9A)
         → Inspections (PA-10/11)
         → Damage Reports (PA-9)
         → Maintenance (PA-13/14)
         → Upgrades
         → Disposals (PA-17/18/19)
         → Loss Reports (PA-28→32)
         → Disposal Sales / Tender / Auction (PA-21→27A)
```

---

## 4. Database Schema Cheat Sheet

### Canonical Tables (the ones that matter for the pipeline)

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `master_items` | Equipment register — unique by `item_no`. The source of truth for all pipeline imports. | `id`, `item_no`, `name`, `category`, `brand`, `serial_numbers`, `unit_price`, `quantity`, `status` ('belum_diterima'/'diterima'/'verified'), `supplier_id`, `is_bundle_component`, `parent_item_id`, `location`, `receive_meta` (JSON), `deleted_at` |
| `receivings` | PA-1 records — created at **Verify** time (pipeline) or immediately at scanner quick-receive. One per receive action (not one per item). | `id`, `master_item_id`, `status`, `received_date`, `receiver_name`, `receiver_position`, `technical_officer_name`, `technical_officer_position`, `damage_description`, `notes`, `invoice_no`, `purchase_order_no`, `delivery_order_no`, `quantity_received`, `rejection_reason`, `rejected_at`, `signatures`, `deleted_at` |
| `assets` | Individual assets — created at Verify time. One per S/N after split. | `id`, `asset_tag` ('AST-XXXXXXXX'), `name`, `category`, `brand`, `serial_number`, `unit_price`, `purchase_price`, `quantity` (always 1 after split), `location`, `custodian_name`, `warranty_expiry`, `budget_vot`, `voucher_no`, `supplier_name`, `received_date`, `status`, `verified_by_scan`, `deleted_at` |
| `suppliers` | Supplier registry. **Dedup executed 2026-07-31 — 2 canonical rows remain.** | `id`, `name`, `contact_person`, `email`, `phone`, `address`, `deleted_at` |
| `scans` | Scanner lookup log. Currently empty — never exercised. | `id`, `serial_number`, `master_item_id`, `asset_id`, `scanned_at`, `action` |
| `locations` | Location lookup table (Phase 1 cleanup). Currently empty — seed via `LocationSeeder`. | `id`, `name`, `code`, `deleted_at` |
| `users` | 7 users across 5 roles. | `id`, `name`, `email`, `password`, `role` ('superadmin'/'admin'/'manager'/'auditor'/'user'), `email_verified_at` |

### Lifecycle Tables (scaffolded, most are empty)

| Table | Records | FK to Assets? | Notes |
|-------|---------|---------------|-------|
| `asset_placements` | 11 | `asset_id` | Demo seed only |
| `asset_disposals` | 10 | `asset_id` | Demo seed only |
| `asset_transfers` | 10 | `asset_id` | Demo seed only |
| `asset_inspections` | 10 | `asset_id` | Demo seed only |
| `asset_maintenances` | 10 | `asset_id` | Demo seed only |
| `asset_loss_reports` | 10 | `asset_id` | Demo seed only |
| `damage_reports` | 11 | `asset_id` | Demo seed only |
| `vehicle_disposal_assessments` | 10 | `asset_id` | Demo seed only |
| `disposal_sales` | 10 | — | Has its own items table |
| `disposal_sale_items` | 21 | — | Sale lots with reserve prices |
| `sale_bids` | 30 | — | Demo bids |
| `asset_upgrades` | 10 | `asset_id` | Demo seed only |
| `committee_appointments` | 12 | — | Pre-seeded placeholder |
| `final_loss_reports` | 10 | — | PA-30 8-section schema |

### Status State Machine

```
master_items.status:
  belum_diterima ──[Receive]──→ diterima ──[Verify]──→ verified
                                    │                      │
                           receive_meta JSON      PA-1 (receivings) +
                           stored (no PA-1)       Assets created atomically
                                                  (one per S/N)
```

### Dropped Tables (no longer exist — cleaned up during refactor)

| Dropped Table | Replaced By |
|---------------|-------------|
| `purchase_orders` | `master_items` (items go direct, no PO abstraction) |
| `purchase_order_items` | `master_items` |
| `delivery_orders` | `master_items` |
| `do_line_items` | `master_items` |

---

## 5. Key Code Paths — Where To Look

### Import Pipeline (OCR → Master Items)

| File | What It Does |
|------|-------------|
| `bin/cairo-ocr` (2,080 lines) | Python OCR engine — parses PO/DO/Quotation PDFs, extracts items with confidence scoring. Detects CamScanner watermarks, handles 4 document formats. |
| `app/Http/Controllers/ImportController.php` | Web UI import — handles CSV/Excel/pasted text uploads at `/import`. `parseCsv()` and `parseExcel()` could be unified. |
| `app/Http/Controllers/PdfImportController.php` | Web UI PDF import at `/pdf-import` — uploads PDF, runs OCR via `OcrService`, shows preview grid. |
| `app/Console/Commands/ImportPdfData.php` | CLI version — `php artisan cairo:import-pdf --file=... --type=... --supplier=...` |
| `app/Services/MasterItemImportService.php` | **Shared service** — `importAsMasterItems()` replaces 3 previously-duplicate implementations. All import paths (CLI, web PDF, web CSV) now call this. |
| `app/Services/OcrService.php` | Python bridge — calls `bin/cairo-ocr` via `proc_open()` without the self-re-exec overhead. |

### Verification Pipeline (Receive → Verify → Assets)

| File | What It Does |
|------|-------------|
| `app/Http/Controllers/VerificationController.php` | The main pipeline controller. `index()` shows master items + scanner panel. `receive()` stores receiver/DO metadata as JSON in `master_items.receive_meta` and updates status to `diterima` (no PA-1 yet). `verify()` reads the metadata and creates **PA-1 (Receiving) + Asset(s) atomically** in one transaction — per-S/N split, location/custodian/warranty/budget_vot/voucher_no capture. |
| `app/Services/AssetTagGenerator.php` | **Unified tag generation** — one parsing rule (`SUBSTR`), `Cache::lock` against races, `AST-YYYYMMDD-NNNNNN` (assets) / `REC-YYYYMMDD-NNNN` (receivings). Used by verify, scanner, `acceptReceiving`, `storeReceiving`. Legacy `CAIRO/…` + `RC-` formats retired. |
| `resources/js/Pages/Verification/Index.jsx` | React page with Registry tab, Receive modal (partial qty + PO/DO/invoice fields), Verify modal (S/N split table + budget_vot/voucher_no), Scanner panel, Tong (soft-deleted items) panel. Verify/Delete buttons are driven by `can_verify` / `can_delete` props derived from `config/roles.php` (fixed 2026-10-08 — Verify was hardcoded to `['admin','superadmin']` while the backend granted `user` role `verify`). |
| `app/Http/Controllers/ScanController.php` | Scanner — status-aware lookup. Returns `belum_diterima`/`diterima`/`verified` info. Exposes only `lookup`, `scan`, `history` — the unrouted `verifyFromScan`/`receiveFromScan` quick-scan actions were removed 2026-10-08 (they contradicted the pipeline's PA-1-at-verify rule). |

### RBAC System

| File | What It Does |
|------|-------------|
| `config/roles.php` | Role-permission matrix defining all 5 roles. **`user` role has `verify`** (added 2026-07-31) — staff can verify, cannot delete. |
| `app/Middleware/PermissionMiddleware.php` | Route-level gate — checks `$request->user()->hasPermission($permission)`. Used as `->middleware('permission:verify')` etc. |
| `app/Middleware/RoleMiddleware.php` | Route-level role check — `->middleware('role:superadmin')`. |
| `app/Models/User.php` | `role` column (string), `hasPermission()`, `hasRole()` methods. `MustVerifyEmail` interface implemented. |
| `app/Http/Controllers/Admin/AdminUserController.php` | CRUD at `/admin/users` — manage users, assign roles (superadmin only). |
| `database/seeders/DatabaseSeeder.php` | Creates the 5 demo accounts (user@gmail.com / admin@cairo.utm / manager@cairo.utm / auditor@cairo.utm / ibrahim@cairo.utm) + master items via `MasterItemSeeder` (156). |
| `database/seeders/AdminUserSeeder.php` | Creates only the deployment admin `admin@cairo.test` (NOT the 5 demo accounts — those come from `DatabaseSeeder`). |

### Soft Delete + Recycle Bin

| File | What It Does |
|------|-------------|
| `database/migrations/2026_07_24_000001_add_soft_deletes_to_all_tables.php` | Adds `deleted_at` to 19 tables in one migration. |
| All 19 models in `app/Models/` | `use SoftDeletes;` trait. |
| `app/Http/Controllers/*.php` (11 controllers) | `destroy()` soft-deletes. `restore()` methods + trashed data for 13 modules (Assets, Receivings, Transfers, Damage Reports, Placements, Inspections, Maintenances, Disposals, Loss Reports, Vehicle Disposals, Disposal Sales, Suppliers, Committees) — with activity logging. |
| `routes/web.php` | All DELETE routes gated with `permission:crud_assets`. Restore routes: `POST /{resource}/{id}/restore` per module. Permanent-delete routes use the stricter `permission:force_delete` (superadmin only). |
| `resources/js/Pages/*/Index.jsx` (11 pages) | "Tong (N)" toggle (admin/superadmin only) revealing trashed table with "Pulihkan" button. Hapus button gated to admin/superadmin. |

### Pages & Components (Frontend)

| Directory | Contents |
|-----------|----------|
| `resources/js/Pages/` | 65 React page components — 1 per route |
| `resources/js/Pages/Verification/` | The pipeline hub: Receive/Verify/Scanner |
| `resources/js/Pages/Assets/` | KEW.PA-2/PA-3 registration, list, show |
| `resources/js/Layouts/AuthenticatedLayout.jsx` | Sidebar + header + role badges (all 5 roles) |
| `resources/js/Components/` | 16 reusable components |
| `resources/views/pdfs/` | 35 Blade PDF templates — all 34 KEW.PA forms |

### Routes

| File | Routes |
|------|--------|
| `routes/web.php` | ~200 route definitions — everything is here (auth, pipeline, lifecycle, reports). No separate API routes. |

---

## 6. Current State — Snapshot

**Date:** 2026-07-31 (verified 2026-08-08)

### What's Built vs What's Exercised

| Area | Built | Exercised With Real Data |
|------|-------|-------------------------|
| **Phase 1 — 16 Lifecycle Modules** | ✅ All CRUD, PDFs, sidebar, seed data | ✅ Demo seed data (DB reset 2026-07-31 — tables empty, re-seed via `DatabaseSeeder`) |
| **OCR Engine** (bin/cairo-ocr) | ✅ 2,080 lines, 4 doc format detectors | ✅ Real PO + DO PDFs |
| **Import Pipeline** (PDF/CSV/Excel) | ✅ All interfaces work | ✅ 156 master items imported |
| **Receive** | ✅ Modal with partial qty, metadata stored as JSON (`receive_meta`) | ✅ Pipeline exercised |
| **Verify → PA-1 + Assets** | ✅ Per-S/N split, atomic creation, AssetTagGenerator | ✅ **15 assets, 2 PA-1 records** (2026-07-27) |
| **Scanner** | ✅ Status-aware lookup panel | ✅ Used during pipeline exercise |
| **RBAC (5 roles)** | ✅ PermissionMiddleware, RoleMiddleware, email verification | ✅ Tested via test suite |
| **Soft Delete + Tong (13 modules)** | ✅ 19 models, restore endpoints, UI buttons | ✅ Tested via test suite |
| **Supplier Dedup** | ✅ `php artisan cairo:dedup-suppliers --force` | ✅ **Executed — 5 → 2 suppliers** |
| **Registration Gating** | ✅ `REGISTRATION_ENABLED` (default off) | ✅ Route unregistered when disabled |

### Database Row Counts (verified 2026-08-08)

| Table | Count | Status |
|-------|-------|--------|
| `master_items` | 156 | 154 `belum_diterima`, 2 `verified` |
| `assets` | 15 | From pipeline exercise (14 from Jul-27 batch + 1) |
| `receivings` | 2 | PA-1 records created at Verify |
| `suppliers` | 2 | Canonical — dedup executed (Dotcom id 1, SNS id 3) |
| `scans` | 0 | Never used |
| `users` | 7 | 1 superadmin, 2 admin, 1 manager, 1 auditor, 2 staff |
| `activity_log` | ~988 | Active audit trail |
| `locations` | 0 | `LocationSeeder` available |
| Demo tables (14) | 0 | DB was reset 2026-07-31 |

### Codebase Size

| Metric | Count |
|--------|-------|
| React Pages | 65 |
| Components | 16 |
| Eloquent Models | 21 |
| Controllers | 37 (28 business + 9 auth) |
| Migrations | 59 |
| Blade PDFs | 35 |
| Routes | ~200 |
| OCR Engine | 2,080 lines Python |
| Tests | 127 tests, 593 assertions — all passing (2026-10-08) |
| Build | ✅ 0 errors (Vite) |

### Master Items by Status

| Status | Count | With Asset |
|--------|-------|------------|
| `belum_diterima` | 154 | 0 |
| `diterima` | 0 | 0 |
| `verified` | 2 | 15 |

**The pipeline is now exercised** (15 assets, 2 PA-1 records). **154 master items remain `belum_diterima`** — the next step is to Receive→Verify them in batches to build the production asset register.

---

## 7. Uncommitted Changes Basket

**46 modified files, 11 untracked files** — changes from the last two working sessions (2026-07-24 + 2026-07-31) that are NOT yet committed.

### Modified Files (46 — highlights)

```
README.md                                        ← login table + pipeline description fixed
app/Console/Commands/ImportPdfData.php           ← Uses MasterItemImportService, imported/skipped shape
app/Http/Controllers/AssetController.php         ← Soft-delete + restore, rejectReceiving redesign, tag via service
app/Http/Controllers/VerificationController.php  ← receive_meta JSON, PA-1 at verify, tags via generator
app/Http/Controllers/ScanController.php          ← off-by-one fix, split guard, threshold config
app/Http/Controllers/ImportController.php        ← Uses MasterItemImportService
app/Http/Controllers/PdfImportController.php     ← Uses MasterItemImportService
app/Http/Controllers/{AssetDisposal,AssetInspection,AssetLossReport,AssetMaintenance,AssetTransfer,AssetUpgrade,CommitteeAppointment,DamageReport,DisposalSale,Supplier,VehicleDisposalAssessment}Controller.php  ← restore() methods
app/Http/Middleware/HandleInertiaRequests.php    ← flash → Inertia props (FlashToaster)
app/Models/MasterItem.php, app/Models/Receiving.php  ← receive_meta, rejection fields
app/Services/MasterItemImportService.php         ← transaction + dedup + skipped counts
bin/cairo-ocr                                    ← 32 regex escapes fixed, qty cross-check
bootstrap/app.php                                ← AdminMiddleware alias removed, route groups merged
config/roles.php                                 ← user role gains verify
database/seeders/DatabaseSeeder.php              ← calls Location/Supplier/MasterItem seeders
phpunit.xml                                      ← REGISTRATION_ENABLED=true for tests
resources/js/Components/Toast.jsx                ← no-emoji icons ✓ ✕ ! i
resources/js/Layouts/AuthenticatedLayout.jsx     ← Role badges + FlashToaster bridge
resources/js/Pages/* (13 JSX)                    ← Trash tab UI + permission gating + Welcome 34 forms
routes/auth.php                                  ← registration gated
routes/web.php                                   ← /_dev/dedup-* removed, restore routes, crud_assets gates
tests/Feature/MasterItemLifecycleTest.php        ← dedup tests → artisan command
```

### Untracked Files (11)

```
app/Console/Commands/DedupMasterItems.php        ← NEW — cairo:dedup-master-items (--dry-run/--force)
app/Console/Commands/DedupSuppliers.php          ← NEW — cairo:dedup-suppliers (executed on live DB)
app/Console/Commands/ResetPipeline.php           ← NEW — cairo:reset-pipeline
app/Models/Location.php                          ← NEW
app/Services/AssetTagGenerator.php               ← NEW — unified AST-/REC- numbering
config/cairo.php                                 ← NEW — asset_threshold, receive_no_prefix, registration_enabled
database/migrations/2026_07_24_000002_create_locations_table.php  ← NEW
database/migrations/2026_07_31_000001_add_receive_meta_to_master_items.php  ← NEW
database/migrations/2026_07_31_000002_add_rejection_fields_to_receivings.php  ← NEW (applied)
database/seeders/LocationSeeder.php              ← NEW
ocr_total_debug_tmp.py                           ← OCR debug helper — pending deletion approval (H1)
```

### What's in the diff

```bash
# View the full uncommitted diff:
git diff HEAD --stat
```

**Recommendation:** Commit these in logical groups before handover (see Housekeeping H2): Phase 0 (security), Phase 1 (data integrity), or per-feature. Do **not** commit `ocr_total_debug_tmp.py` — delete it first.

---

## 8. Known Issues & Technical Debt

### 🔴 Pipeline (needs action before production use)

| # | Issue | Impact | Fix |
|---|-------|--------|-----|
| 1 | ~~**2 stale tests failing**~~ | RESOLVED 2026-10-08 — `EndToEndPipelineTest` now asserts PA-1 is *not* created at receive and is created at verify; `RbacTest::test_staff_can_verify_items` asserts the current contract (staff DO verify). Full suite verified green locally: **127 passed / 593 assertions**. | Done |
| 2 | ~~**Verify button UI/backend mismatch**~~ — `Verification/Index.jsx` hid the button for non-`['admin','superadmin']` while the route only needs `permission:verify` | RESOLVED 2026-10-08 — `can_verify` prop from `VerificationController::index()` (via `User::hasPermission()`, same `config/roles.php` source as `PermissionMiddleware`). Delete button likewise uses `can_delete`. | Done |
| 3 | **`acceptReceiving()` validates only `photo`** (`AssetController.php:51-105`) | Garbage category/campus/location/warranty values persist silently | Mirror `update()` rules + `$request->safe()->only([...])`. See backlog item 14. |
| 4 | **Jul-27 asset fate** — 14 assets (ids 1-14, `AST-20260727-000001…014`) created from a **qty=1** master item (mi=3, "Single Core", 14 S/Ns) under old split logic; source list has duplicate S/N `BZMKCH4` | Current code would create ONE asset with concatenated S/Ns for qty=1 — inconsistent with the 14 existing assets | Decide: collapse 14 → 1 asset or keep (verify against source DO). See backlog item 19. |

### 🟡 Functional

| # | Issue | Notes |
|---|-------|-------|
| 5 | **Dashboard** uses free-text `custodian_name` matching instead of user FK | Low priority — only matters once assets exist with real custodian data |
| 6 | **OCR `total_amount` still None** on scanned DO docs — Tesseract text contains no JUMLAH/TOTAL/RM markers | Capture quality is the limit; multi-line pattern + preprocessing options in backlog F1 |
| 7 | **OCR supplier capture includes noise** — `[^\n]*` grabs to EOL (e.g. "Dotcom Telecommunications Sdn BAG Â« …") | Tighten capture class to `[A-Za-z0-9 .&()\-]+` (backlog F2) |
| 8 | **JADUAL HARGA multi-page scan takes 10+ min to OCR** | Blocks PHP worker when invoked via web — add page-range option / async (backlog F3) |
| 9 | **Welcome page has no sidebar link** | Authenticated users must type `/` to see it |
| 10 | **`acceptReceiving()` hardcodes `>= 2000` threshold** (line 61) instead of `config('cairo.asset_threshold')` | One-line fix (backlog item 15) |

### 🔵 Technical Debt

| # | Issue | Location | Suggested Fix |
|---|-------|----------|---------------|
| 11 | `bin/cairo-ocr` self-re-exec logic doubles execution time when called directly | `bin/cairo-ocr:__main__` | PdfImportController uses OcrService (no re-exec). The re-exec is only needed for direct CLI use. |
| 12 | `ImportController::parseCsv` and `parseExcel` share no common base | `ImportController.php` | Extract a shared CSV/Excel parser service |
| 13 | SNS Network DO PDFs have ~61% extraction accuracy | `bin/cairo-ocr` | Harder format — the SNS PDF has less tabular structure |
| 14 | **Dead code**: ~~`ItemsController` + `Items/Index.jsx` (zero routes)~~ (removed 2026-10-08), orphaned `Scanner/Index.jsx` + `History.jsx`, `Adminmiddleware.php` (no alias registered), `DummyKewpaSeeder` + `DashboardTestSeeder` unreferenced | Various | Remaining: `git grep` each name → zero references (backlog item 20) |
| 15 | `orWhereYear` ungrouped in kewpa12/kewpa20 | `ReportController.php:196-199` | Wrap in `where(fn ($q) => ...)` (backlog item 22) |
| 16 | **No permanent delete outside Master Items** — only `DELETE /verification/{id}/force` exists. The other 13 trash bins (Assets, Receivings, Transfers, Damage Reports, Placements, Inspections, Maintenances, Disposals, Loss Reports, Vehicle Disposals, Disposal Sales, Suppliers, Committee Appointments) can restore but never purge, so soft-deleted rows accumulate forever | `routes/web.php` + each controller's `restore()` area | Mirror the Master Items pattern: `DELETE /{resource}/{id}/force` + `forceDelete($id)` + a `Padam Kekal` button in each trash table, all gated `permission:force_delete` |
| 17 | `MasterItem` has no `LogsActivity` trait (unlike the other 17 models), so master-item edits/imports are only logged where `VerificationController` logs explicitly | `app/Models/MasterItem.php` | Deliberate: adding it would write one `created` entry per imported row (156 per import). Revisit if import provenance needs auditing |
| 18 | **User account deletions are not audited** — the `User` model has no `LogsActivity` and neither `ProfileController::destroy()` (self-delete) nor `Admin\UserController::destroy()` (superadmin delete) writes an activity entry, so a deleted account leaves no trace | `app/Models/User.php`, `ProfileController.php`, `Admin/UserController.php` | Add `LogsActivity` to `User` (low volume — a handful of accounts) or log explicitly in both destroy methods |

### ✅ Recently Fixed (2026-07-31 — uncommitted)

| Fix | What Changed |
|-----|-------------|
| Supplier dedup EXECUTED | 5 → 2 suppliers (Dotcom id 1, SNS id 3); 156 master_items re-pointed via `cairo:dedup-suppliers --force` |
| Pipeline exercised | 15 assets + 2 PA-1 records created (2026-07-27) |
| Legacy receivings gone | 12 fictional-company receivings removed with DB reset (2026-07-31) |
| PA-1 moved to Verify | `receive()` stores `receive_meta` JSON; `verify()` creates PA-1 + Assets atomically |
| Ungated `/dev/dedup-*` routes removed | Converted to `cairo:dedup-master-items` / `cairo:dedup-suppliers` artisan commands (--dry-run/--force) |
| Registration gated | `REGISTRATION_ENABLED` (default off); routes unregistered, Register link auto-hides |
| 32 OCR regex escapes fixed | Double-backslash corruption across 7 raw strings (supplier, total, generic fallback) |
| rejectReceiving redesign | No more fake zero-value `REJ-` disposed asset — records `rejection_reason` + `rejected_at` |
| Import atomicity | `DB::transaction` + dedup guard + `['imported','skipped']` return shape |
| Scanner off-by-one fixed | Returns actual created tag/number, not next sequence value |
| AssetTagGenerator service | Unified `AST-`/`REC-` numbering with `Cache::lock`; legacy `CAIRO/…` + `RC-` retired |
| Session flashes → toasts | FlashToaster auto-renders `success`/`error` — permission denials finally visible |
| `user` role gains `verify` | RBAC fix — staff can verify, cannot delete |

### ✅ Recently Fixed (2026-10-08)

| Fix | What Changed |
|-----|-------------|
| Master items get a Tong | `DELETE /verification/{masterItem}` + `POST /verification/{id}/restore` (`permission:crud_assets`), `VerificationController::destroy()/restore()`, soft-delete guard against items that already have a PA-1/asset, Spatie activity log, `trashed` prop + **Tong (N)** panel and `Hapus`/`Pulihkan` buttons in the Pipeline tab |
| Pipeline errors finally visible | `Verification/Index.jsx` rendered `filters.item_error`, a key `index()` never sends — every receive/verify rejection (already received / already verified / must receive first) was a silent no-op. Now rendered from the Inertia `errors` prop, both as a page banner and inside the Verify modal |
| Verify button permission sync | `can_verify` / `can_delete` props from `VerificationController::index()`, backed by a new `User::hasPermission()` that reads the same `config/roles.php` as `PermissionMiddleware` — no more hardcoded `['admin','superadmin']` for Verify |
| Clear button on the pipeline date filter | The Verification filter row had none while Assets/Receiving did; the new one also re-fetches (the older two only reset local state) |
| Dead code removed | `ItemsController.php` + `resources/js/Pages/Items/Index.jsx` (zero routes, unreachable `/items` page) and `ScanController::verifyFromScan()` / `receiveFromScan()` (unrouted, and they disagreed with the pipeline on when PA-1 is created — scanner-receive + pipeline-verify would have produced two PA-1s) |
| 2 stale tests fixed | `EndToEndPipelineTest` asserts PA-1 is created at **verify**, not receive; `RbacTest::test_staff_cannot_verify_items` → `test_staff_can_verify_items`. 6 new tests in `VerificationPipelineTest` (T13–T18) — Tong delete/restore/guard, permission props, and the rejection-reaches-the-page regression guard |
| Stale pipeline copy corrected | The Verification page still told the user PA-1 is created at **Receive** in three places: the header sentence, the Receive modal's submit button ("Confirm Receive & Create PA-1"), and the legend ("Verify button = diterima (PA-1 exists)"). The same legend also had the PA-2/PA-3 threshold backwards. All now read: orange **Receive** → diterima (no PA-1 yet); blue **Verify** → creates PA-1 + the PA-2/PA-3 asset; below RM 2,000 = PA-3 Inventori, RM 2,000+ = PA-2 Harta Tetap |
| Permanent delete from the Tong | New `force_delete` permission (**superadmin only**) + `DELETE /verification/{id}/force` → `VerificationController::forceDelete()`, with a `Padam Kekal` button in the Tong (the UI asks you to type `PADAM` to confirm) gated by a `can_force_delete` prop. ⚠️ **Master Items only so far — the other 13 trash bins are still restore-only.** See Technical Debt #16. |
| Audit trail completed for the pipeline | `VerificationController` now writes explicit, named events: `received`, `verified`, `deleted`, `restored`, `force_deleted`. Previously `deleted`/`restored` were written with a **null `event`**, so the audit page showed an unnamed badge and the filter couldn't find them. `AuditLogController` now derives the event filter from the data instead of a hardcoded 4-item list, and the audit page labels/badges every event type — new event names become filterable with no code change |

> Verification (2026-10-08): `php -l` clean on every edited PHP file; real `vite build` succeeded (all page chunks). Full suite: **127 passed / 593 assertions / 0 failed** (`php artisan test`, 202s). Note for a fresh clone: `public/build/manifest.json` (gitignored) must exist — `resources/views/app.blade.php` uses `@vite(...)` with a per-page entry, so any test rendering the Inertia root view fails with "Vite manifest not found" until `npm run build` has been run once. Tests need no database: `phpunit.xml` pins sqlite `:memory:`.

---

## 9. Roadmap — Prioritised Next Steps

> Full detail + execution order: `plans/2026-07-31_future-work-backlog.md` (self-contained, written at session close).

### P0 — Commit the working tree (est. 30 min)

| # | Task | Est. | Details |
|---|------|------|---------|
| 1 | Delete `ocr_total_debug_tmp.py` | 1 min | OCR debug helper — pending deletion approval (Housekeeping H1) |
| 2 | Commit Phase 0 + Phase 1 changes in logical groups | 30 min | Security (Phase 0), data integrity (Phase 1), or per-feature. Do NOT commit the debug tmp file. |

### P1 — Green suite + clerk UX (est. half day)

| # | Task | Est. | Details |
|---|------|------|---------|
| 3 | ~~Fix 2 stale tests (backlog #17)~~ ✅ | done | EndToEndPipelineTest (PA-1 at Verify now) + RbacTest staff-can-verify → suite **123 pass / 0 fail** |
| 4 | `acceptReceiving()` validation (backlog #14) | 30 min | Mirror `update()` rules, `$request->safe()->only([...])` |
| 5 | Threshold → config (backlog #15) | 5 min | `AssetController.php:61` hardcoded `>= 2000` → `config('cairo.asset_threshold')` |
| 6 | Verify-button permission sync (backlog #16) | 30 min | Pass `can_verify` prop; gate button on it instead of hardcoded roles |
| 7 | OCR confidence enforcement (backlog #13) | 2-3 hrs | Show score in preview, warn/block below ~50, log low-confidence imports |

### P2 — Tests & docs (est. half day)

| # | Task | Est. | Details |
|---|------|------|---------|
| 8 | README overhaul (backlog #18) | 1 hr | Counts verified 2026-08-08: 156 master items, 59 migrations, 21 models, 16 components, 15 assets, 2 receivings, 2 suppliers → tests now **127 tests / 593 assertions (all passing)** as of 2026-10-08 |
| 9 | Jul-27 asset fate (backlog #19) | 30 min | 14 assets from qty=1 item — collapse or keep after checking source DO |
| 10 | Dead-code cleanup (backlog #20) ⚠️ Partial | 30 min | ✅ ItemsController + Items/Index.jsx + ScanController::verifyFromScan/receiveFromScan (2026-10-08). Remaining: orphaned Scanner pages, Adminmiddleware.php, unused seeders |

### P3 — Polish & Tech Debt (est. 1-2 days)

| # | Task | Est. | Details |
|---|------|------|---------|
| 11 | Barcode label printing from verification page | 30 min | Currently only from Asset show page. Add print button to Verify modal. |
| 12 | Dashboard custodian_name → user FK | 30 min | Replace string matching with proper user relationship |
| 13 | Unify CSV/Excel parsers in ImportController | 15 min | Extract shared base class |
| 14 | OCR follow-ups F1/F2/F3 | half day | total_amount capture, supplier noise, slow multi-page scans |
| 15 | Exercise remaining 154 master items | 1-2 hrs | Receive→Verify in batches to build the production asset register |

### Stretch — Nice To Have

| # | Task | Notes |
|---|------|-------|
| 16 | Docker Compose for reproducible dev environment | Laravel + PostgreSQL + OCR in containers |
| 17 | Bilingual support (BM/EN toggle) | Current UI is mixed — some labels in BM, some in EN |
| 18 | SAGA integration | Sync asset data with Malaysia's national SAGA system |
| 19 | E2E test suite | Automated browser tests for all CRUD workflows |
| 20 | DB-level pagination (backlog #21) + `orWhereYear` grouping (backlog #22) | Performance — defer until data grows past a few thousand |

---

## 10. Deployment

### Production Environment

| Service | Detail |
|---------|--------|
| **Compute** | AWS Elastic Beanstalk (PHP 8.2, nginx) |
| **Database** | AWS RDS PostgreSQL 18 (`cairo-inventory-db.c3k8m8ow8w3g.ap-southeast-1.rds.amazonaws.com`) |
| **Storage** | AWS S3 (for media files) |
| **PDF Engine** | Chromium installed via `.platform/hooks/postdeploy/01_puppeteer_setup.sh` |
| **VPN** | Tailscale — developer SSH access to dev environment |
| **Domain** | Elastic Beanstalk URL (custom domain TBD) |

### EB Configuration

| File | Purpose |
|------|---------|
| `.ebextensions/01_laravel.config` | Installs Chrome dependencies via yum, sets up Node.js 20, runs `php artisan migrate --force` on deploy |
| `.platform/hooks/postdeploy/01_puppeteer_setup.sh` | Installs Puppeteer + Chromium for PDF generation |
| `.platform/nginx/conf.d/elasticbeanstalk/laravel.conf` | Nginx config — rewrites to `index.php` |

### Env Vars That Matter

| Variable | Local | Production |
|----------|-------|------------|
| `DB_CONNECTION` | `pgsql` | `pgsql` |
| `DB_HOST` | `127.0.0.1` | RDS endpoint |
| `DB_DATABASE` | `cairo_inventory` | `cairo_inventory` |
| `APP_KEY` | From `.env.example` | Generate unique |
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `LARAVEL_PDF_NO_SANDBOX` | `true` | `true` |
| `LARAVEL_PDF_CHROME_PATH` | Chromium path on dev machine | `/var/app/current/node_modules/puppeteer/.local-chromium/...` |

### Deploy Steps

```bash
# AWS EB deploy (from the repo root)
eb deploy cairo-inventory-env

# SSH into EB instance
eb ssh cairo-inventory-env

# View logs
eb logs cairo-inventory-env
```

---

## 11. Test Suite

**127 tests, 593 assertions — all passing** (verified 2026-10-08; the 2 stale failures from Known Issues #1 are fixed) across 13 feature files + Auth suite. Per-file assertion counts below are the pre-2026-10-08 tally — the suite total above is authoritative:

| Test File | Tests | Assertions | Coverage |
|-----------|-------|------------|----------|
| `ImportPipelineTest.php` | 13 | 57 | PDF import, CSV import, column matching, dedup |
| `RbacTest.php` | 20 | 31 | Route permissions, role gating, middleware (stale staff-cannot-verify test rewritten to staff-can-verify, 2026-10-08) |
| `RolesTest.php` | 15 | 29 | Role assignment, user management |
| `VerificationPipelineTest.php` | 21 | 45 | Receive flow, Verify flow, item status transitions, Tong delete/restore + downstream guard + permission props + rejection-reaches-page + superadmin purge + audit trail (T13–T21, 2026-10-08) |
| `ScannerTest.php` | 10 | 42 | Scanner lookup, status-aware actions |
| `MasterItemLifecycleTest.php` | 8 | 22 | CRUD, filtering, master item status, dedup commands |
| `PaFormViewTest.php` | 6 | 9 | PDF form rendering, field presence |
| `ProfileTest.php` | 5 | 16 | User profile, password change |
| `VerifyEmailFullFlowTest.php` | 4 | 8 | Email verification lifecycle |
| `EmailVerificationTest.php` | 3 | 5 | Verification mail, resend |
| `EndToEndPipelineTest.php` | 1 | 51 | Full pipeline: import → receive → verify → asset check (asserts PA-1 at Verify, and asserts receive creates none — 2026-10-08) |
| `ExampleTest.php` (Unit) | 1 | 1 | Default |
| `EmailLogTest.php` | 1 | 3 | Mail log assertion |
| `tests/Feature/Auth/*` | — | — | Breeze defaults (Authentication, Password Reset, Registration, etc.) |

```bash
# Run all tests
php artisan test

# Run specific suite
php artisan test --filter=VerificationPipelineTest
php artisan test --filter=RbacTest

# Run with coverage (requires Xdebug)
php artisan test --coverage
```

> **Note:** `phpunit.xml` sets `REGISTRATION_ENABLED=true` so registration tests pass — the app default is **off**.

---

## 12. Reference — Key CLI Commands

### Artisan Commands

```bash
# Import PDF via CLI
php artisan cairo:import-pdf --file=path/to/document.pdf --type=po --supplier=1

# Migrate + seed fresh
php artisan migrate:fresh --seed

# Run migrations only
php artisan migrate

# Run seeders
php artisan db:seed
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=LocationSeeder

# Dedup master items / suppliers (replaces the removed /_dev/dedup-* routes)
php artisan cairo:dedup-master-items --dry-run
php artisan cairo:dedup-master-items --force
php artisan cairo:dedup-suppliers --dry-run
php artisan cairo:dedup-suppliers --force

# Reset the pipeline (master items back to belum_diterima)
php artisan cairo:reset-pipeline

# Clear cache
php artisan optimize:clear

# Generate application key
php artisan key:generate

# List all routes
php artisan route:list

# Create storage symlink
php artisan storage:link
```

### Frontend Commands

```bash
# Build for production
npm run build

# Dev server with hot reload
npm run dev

# Build with stats
npx vite build --debug
```

### Database Commands

```bash
# Access PostgreSQL via WSL
sudo -u postgres psql -d cairo_inventory

# Or via PowerShell (to Windows PostgreSQL)
powershell.exe -Command "psql -U postgres -d cairo_inventory -c 'SELECT COUNT(*) FROM master_items;'"

# Quick sanity checks
SELECT COUNT(*), status FROM master_items GROUP BY status;
SELECT COUNT(*) FROM assets;
SELECT COUNT(*) FROM receivings WHERE master_item_id IS NOT NULL;
SELECT name, COUNT(*) as cnt FROM suppliers GROUP BY name HAVING COUNT(*) > 1;  -- expect 0 rows (dedup done)
```

### OCR Tests

```bash
# Run OCR on a PDF directly (Python)
python3 bin/cairo-ocr /path/to/document.pdf

# With verbose debug output
python3 bin/cairo-ocr /path/to/document.pdf --debug
```

---

## 13. Sources of Truth

| What | Where | Notes |
|------|-------|-------|
| **Architecture & Features** | `README.md` | Updated 2026-08-08 — all modules, pipeline, RBAC, soft delete, verified counts |
| **System State + Changes** | `plans/2026-07-31_current-system-state.md` | Detailed current state, pipeline, RBAC, trash bin, Phase 0+1 execution record |
| **Future Work Backlog** | `plans/2026-07-31_future-work-backlog.md` | Self-contained phased plan (Phase 2-4, OCR follow-ups, housekeeping) — start here |
| **Current State + Roadmap** | `plans/current-state-and-roadmap.md` | Earlier state doc (2026-07-24) — superseded by the 2026-07-31 docs |
| **Test Methodology** | `plans/test-methodology-and-coverage.md` | Test file breakdown, what each test covers |
| **System Audit (stale)** | `plans/system-assessment.md` | From June 2026 — PO/DO tables described no longer exist. Use current-system-state.md instead. |
| **Ground Truth Forms** | `KOLEKSI BORANG KEW.PA.pdf` | Official PDF of all 34 KEW.PA forms — the specification document |
| **OCR Engine** | `bin/cairo-ocr` | 2,080-line Python — the code is the documentation |
| **Compiled Assessment** | `C:\Hakimi\Stuff\assess\COMPILED_ASSESSMENT_2026-07-31.md` | 15/15 findings verified against code (ACTION_PLAN.md) |
| **This Handover** | `HANDOVER.md` | You are here |


---

> *"A comprehensive, digital, and user-friendly asset management system for Universiti Teknologi Malaysia."*


---
