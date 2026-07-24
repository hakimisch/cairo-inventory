# CAIRO Inventory — Handover Document

> **Project handover** — for the next developer continuing CAIRO Inventory development after the original author's departure.
>
> **Author:** Hakimi (hakimisch) — Last updated: 2026-07-24
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
| Migrations ran | `php artisan migrate:status` | All 56 migrations show "Y" (ran) |
| Seed users exist | Check DB: `SELECT email, role FROM users;` | 7 users across 5 roles |
| Master items exist | Check DB: `SELECT COUNT(*) FROM master_items;` | 170 rows |
| Build is clean | `npm run build` | ✓ built in ~18s, 0 errors |
| Tests pass | `php artisan test` | 117 tests, 538 assertions |

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
│  Routes → Controllers (37) → Eloquent Models (20) → PostgreSQL   │
│                              │                                   │
│            ┌─────────────────┼─────────────────┐                │
│            ▼                 ▼                  ▼                │
│     PDF Engine          OCR Pipeline       Services              │
│    (35 Blade tpls)   (2,069-line Python)   (Import, OCR)         │
│     Browsershot        Tesseract                                 │
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
master_items (170 items, status: belum_diterima)
       │
       ▼  /verification page
Receive ──→ diterima ──→ PA-1 created (receivings table)
  │
  ▼  (admin/superadmin only)
Verify ──→ verified ──→ Assets created (assets table, per-S/N split)
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
| `master_items` | Equipment register — unique by `item_no`. The source of truth for all pipeline imports. | `id`, `item_no`, `name`, `category`, `brand`, `serial_numbers`, `unit_price`, `quantity`, `status` ('belum_diterima'/'diterima'/'verified'), `supplier_id`, `is_bundle_component`, `parent_item_id`, `location`, `deleted_at` |
| `receivings` | PA-1 records — created at Receive time. One per receive action (not one per item). | `id`, `master_item_id`, `status`, `received_date`, `receiver_name`, `receiver_position`, `technical_officer_name`, `technical_officer_position`, `damage_description`, `notes`, `invoice_no`, `purchase_order_no`, `delivery_order_no`, `quantity_received`, `signatures`, `deleted_at` |
| `assets` | Individual assets — created at Verify time. One per S/N after split. | `id`, `asset_tag` ('AST-XXXXXXXX'), `name`, `category`, `brand`, `serial_number`, `unit_price`, `purchase_price`, `quantity` (always 1 after split), `location`, `custodian_name`, `warranty_expiry`, `budget_vot`, `voucher_no`, `supplier_name`, `received_date`, `status`, `verified_by_scan`, `deleted_at` |
| `suppliers` | Supplier registry. Currently has duplicates (needs dedup). | `id`, `name`, `contact_person`, `email`, `phone`, `address`, `deleted_at` |
| `scans` | Scanner lookup log. Currently empty — never exercised. | `id`, `serial_number`, `master_item_id`, `asset_id`, `scanned_at`, `action` |
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
                                PA-1 created          Assets created
                                (receivings)          (one per S/N)
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
| `bin/cairo-ocr` (2,069 lines) | Python OCR engine — parses PO/DO/Quotation PDFs, extracts items with confidence scoring. Detects CamScanner watermarks, handles 4 document formats. |
| `app/Http/Controllers/ImportController.php` | Web UI import — handles CSV/Excel/pasted text uploads at `/import`. `parseCsv()` and `parseExcel()` could be unified. |
| `app/Http/Controllers/PdfImportController.php` | Web UI PDF import at `/pdf-import` — uploads PDF, runs OCR via `OcrService`, shows preview grid. |
| `app/Console/Commands/ImportPdfData.php` | CLI version — `php artisan cairo:import-pdf --file=... --type=... --supplier=...` |
| `app/Services/MasterItemImportService.php` | **Shared service** — `importAsMasterItems()` replaces 3 previously-duplicate implementations. All import paths (CLI, web PDF, web CSV) now call this. |
| `app/Services/OcrService.php` | Python bridge — calls `bin/cairo-ocr` via `proc_open()` without the self-re-exec overhead. |

### Verification Pipeline (Receive → Verify → Assets)

| File | What It Does |
|------|-------------|
| `app/Http/Controllers/VerificationController.php` | The main pipeline controller. `index()` shows master items + scanner panel. `receive()` creates PA-1 + updates status to `diterima`. `verify()` splits by S/N and creates Assets. |
| `resources/js/Pages/Verification/Index.jsx` | React page with Registry tab, Receive modal (with partial qty + PO/DO/invoice fields), Verify modal (with S/N split table + budget_vot/voucher_no), Scanner panel. |
| `app/Http/Controllers/ScanController.php` | Scanner — status-aware lookup. Returns `belum_diterima`/`diterima`/`verified` info for the frontend to decide next action. |

### RBAC System

| File | What It Does |
|------|-------------|
| `config/roles.php` | Role-permission matrix defining all 5 roles and their granted permissions. |
| `app/Middleware/PermissionMiddleware.php` | Route-level gate — checks `$request->user()->hasPermission($permission)`. Used as `->middleware('permission:verify')` etc. |
| `app/Middleware/RoleMiddleware.php` | Route-level role check — `->middleware('role:superadmin')`. |
| `app/Models/User.php` | `role` column (string), `hasPermission()`, `hasRole()` methods. `MustVerifyEmail` interface implemented. |
| `app/Http/Controllers/Admin/AdminUserController.php` | CRUD at `/admin/users` — manage users, assign roles (superadmin only). |
| `database/seeders/AdminUserSeeder.php` | Seeds 7 users across all 5 roles. |

### Soft Delete + Recycle Bin

| File | What It Does |
|------|-------------|
| `database/migrations/2026_07_24_000001_add_soft_deletes_to_all_tables.php` | Adds `deleted_at` to 19 tables in one migration. |
| All 19 models in `app/Models/` | `use SoftDeletes;` trait. |
| `app/Http/Controllers/AssetController.php` | `destroy()` soft-deletes assets/receivings. `restore()` / `restoreReceiving()` restores with activity logging. |
| `routes/web.php` | All DELETE routes gated with `permission:crud_assets`. Restore routes: `POST /assets/{id}/restore`, `POST /receivings/{id}/restore`. |
| `resources/js/Pages/Assets/Index.jsx` | "Tong" tab shows soft-deleted assets with "Pulihkan" button. Hapus button gated to admin/superadmin. |
| `resources/js/Pages/Assets/ReceivingIndex.jsx` | "Tong Sampah" section at page bottom with restore. |

### Pages & Components (Frontend)

| Directory | Contents |
|-----------|----------|
| `resources/js/Pages/` | 65 React page components — 1 per route |
| `resources/js/Pages/Verification/` | The pipeline hub: Receive/Verify/Scanner |
| `resources/js/Pages/Assets/` | KEW.PA-2/PA-3 registration, list, show |
| `resources/js/Layouts/AuthenticatedLayout.jsx` | Sidebar + header + role badges (all 5 roles) |
| `resources/js/Components/` | 15 reusable components |
| `resources/views/pdfs/` | 35 Blade PDF templates — all 34 KEW.PA forms |

### Routes

| File | Routes |
|------|--------|
| `routes/web.php` | ~175 route definitions — everything is here (auth, pipeline, lifecycle, reports). No separate API routes. |

---

## 6. Current State — Snapshot

**Date:** 2026-07-24

### What's Built vs What's Exercised

| Area | Built | Exercised With Real Data |
|------|-------|-------------------------|
| **Phase 1 — 16 Lifecycle Modules** | ✅ All CRUD, PDFs, sidebar, seed data | ✅ Demo seed data only |
| **OCR Engine** (bin/cairo-ocr) | ✅ 2,069 lines, 4 doc format detectors | ✅ Real PO + DO PDFs |
| **Import Pipeline** (PDF/CSV/Excel) | ✅ All interfaces work | ✅ 170 master items imported |
| **Receive → PA-1** | ✅ Full modal with signature block, partial qty | 
| **Verify → Assets** | ✅ Per-S/N split, atomic tags, budget_vot/voucher_no | 
| **Scanner** | ✅ Status-aware lookup panel | 
| **RBAC (5 roles)** | ✅ PermissionMiddleware, RoleMiddleware, email verification | ✅ Tested via test suite |
| **Soft Delete + Tong** | ✅ 19 models, restore endpoints, UI buttons | ✅ Tested via test suite |
| **Supplier Dedup** | ✅ `GET /_dev/dedup-suppliers` route | 

### Database Row Counts

| Table | Count | Status |
|-------|-------|--------|
| `master_items` | 170 | All `belum_diterima` |
| `assets` | 0 | Pipeline never run |
| `receivings` | 12 | Legacy seed only (fictional companies, no FK to pipeline) |
| `suppliers` | 5 | Has duplicates (Dotcom ×3: IDs 1,6,7; SNS ×2: IDs 3,8) |
| `scans` | 0 | Never used |
| `users` | 7 | 1 superadmin, 2 admin, 1 manager, 1 auditor, 2 staff |
| `activity_log` | ~970 | Active audit trail |
| `committee_appointments` | 12 | Placeholder (DummyKewpaSeeder) |

### Codebase Size

| Metric | Count |
|--------|-------|
| React Pages | 65 |
| Components | 15 |
| Eloquent Models | 20 |
| Controllers | 37 (28 business + 9 auth) |
| Migrations | 56 |
| Blade PDFs | 35 |
| Routes | ~175 |
| OCR Engine | 2,069 lines Python |
| Tests | 117 tests, 538 assertions |
| Build | ✅ 0 errors (Vite) |

### Master Items by Status

| Status | Count | With Asset |
|--------|-------|------------|
| `belum_diterima` | 170 | 0 |
| `diterima` | 0 | 0 |
| `verified` | 0 | 0 |

**The entire pipeline is ready but never exercised.** This is the single most important task for whoever picks this up.

---

## 7. Uncommitted Changes Basket

**31 modified files, 9 untracked files** — changes from the last working session that are NOT yet committed.

### Modified Files (31)

```
app/Console/Commands/ImportPdfData.php       ← Uses MasterItemImportService
app/Http/Controllers/AssetController.php     ← Soft-delete + restore methods
app/Http/Controllers/ImportController.php    ← Uses MasterItemImportService
app/Http/Controllers/PdfImportController.php ← Uses MasterItemImportService
app/Http/Controllers/VerificationController.php ← PO field, partial qty, budget_vot/voucher_no
app/Models/*.php (19 models)                 ← SoftDeletes trait
bin/cairo-ocr                                ← Brand extraction fix, S/N parsing, qty fix, roman numeral format
resources/js/Layouts/AuthenticatedLayout.jsx ← Role badges (all 5 roles)
resources/js/Pages/Assets/Index.jsx          ← Tong tab + gate delete
resources/js/Pages/Assets/ReceivingIndex.jsx ← Tong section + gate delete
resources/js/Pages/Suppliers/Index.jsx       ← Gate delete
resources/js/Pages/Verification/Index.jsx    ← Gate Verify + PO field + partial qty + budget_vot
routes/web.php                               ← crud_assets gates, restore routes, dedup route
```

### Untracked Files (9)

```
app/Services/MasterItemImportService.php     ← New shared import service
database/migrations/2026_07_24_000001_add_soft_deletes_to_all_tables.php  ← New migration
scripts/seed_rbac_users.php                  ← Utility
scripts/validate_seeder_prices.py            ← Utility
scripts/verify_analysis.py                   ← Utility
scripts/test-mail.php                        ← Utility
item_documents/DO SNS.pdf                    ← Reference PDF
```

### What's in the diff

```bash
# View the full uncommitted diff:
git diff HEAD --stat
# 31 files changed, 464 insertions(+), 187 deletions(-)
```

**Recommendation:** Commit these changes before the handover. They represent a coherent batch of work (soft delete + RBAC hardening + pipeline polish + OCR hardening). A single commit message like `feat: soft delete, RBAC hardening, pipeline completion` would be appropriate.

---

## 8. Known Issues & Technical Debt

### 🔴 Pipeline (needs action before production use)

| # | Issue | Impact | Fix |
|---|-------|--------|-----|
| 1 | **Suppliers have duplicates** — Dotcom (IDs 1,6,7) and SNS (IDs 3,8) | Importing new items links to wrong supplier ID | Hit `GET /_dev/dedup-suppliers` to merge. After that, confirm 14 master_items references updated. |
| 2 | **Pipeline never exercised** — 170 master items all `belum_diterima`. 0 assets. 0 receivings. | No production data exists | Run Receive then Verify on a batch of items. Start small (5-10 items) to validate the flow. |
| 3 | **12 legacy receivings** have no FK to pipeline (fictional companies: TechZone, Global Tech, etc.) | They're noise in the PA-1 listing | Either delete them or hide behind a filter. |

### 🟡 Functional

| # | Issue | Notes |
|---|-------|-------|
| 4 | **Dashboard** uses free-text `custodian_name` matching instead of user FK | Low priority — only matters once assets exist with real custodian data |
| 5 | **Duplicate `/dashboard` route** defined twice in `routes/web.php` | No runtime impact — Laravel uses the last definition |
| 6 | **Many lifecycle tables empty** (12/22 tables have 0 records) | By design — scaffolded for future use. Not a bug. |
| 7 | **DummyKewpaSeeder seeds 12 placeholder committee_appointments** | Low priority — delete when real data is ready |

### 🔵 Technical Debt

| # | Issue | Location | Suggested Fix |
|---|-------|----------|---------------|
| 8 | `bin/cairo-ocr` self-re-exec logic doubles execution time when called directly | `bin/cairo-ocr:__main__` | PdfImportController uses OcrService (no re-exec). The re-exec is only needed for direct CLI use. |
| 9 | `ImportController::parseCsv` and `parseExcel` share no common base | `ImportController.php` | Extract a shared CSV/Excel parser service |
| 10 | SNS Network DO PDFs have ~61% extraction accuracy | `bin/cairo-ocr` | Harder format — the SNS PDF has less tabular structure |

### ✅ Recently Fixed (committed in the uncommitted diff)

| Fix | What Changed |
|-----|-------------|
| Brand extraction | KNOWN_BRANDS list checked first (was falling through to `/` split) |
| Embedded S/N extraction | Regex patterns now extract S/N from description text |
| Qty override bug | Prefers larger qty values (was taking the first number found) |
| Roman numeral format | `(i)/(ii)/(iii)` in item codes converted to `-A/-B/-C` |
| PO reference field | Added to Receive modal (was hardcoded to `'N/A'`) |
| Partial receive qty | Quantity Received field with partial warning |
| budget_vot + voucher_no | Added to Verify modal (was 0% fill rate) |
| MasterItemImportService | Replaced 3 duplicate implementations |

---

## 9. Roadmap — Prioritised Next Steps

### P0 — Exercise the Pipeline (est. 1 day)

| # | Task | Est. | Details |
|---|------|------|---------|
| 1 | Commit uncommitted changes | 5 min | `git add -A && git commit -m "..."` |
| 2 | Run `GET /_dev/dedup-suppliers` | 1 min | Merge Dotcom (IDs 1,6,7→1) and SNS (IDs 3,8→3) |
| 3 | Exercise Receive flow | 30 min | Select 5-10 master items, Receive them → verify PA-1 created |
| 4 | Exercise Verify flow | 30 min | Verify received items → verify Assets created with correct S/N split |
| 5 | Write a smoke test | 15 min | Quick assertion that pipeline produces 1 asset per S/N |

### P1 — Production Readiness (est. 2-3 days)

| # | Task | Est. | Details |
|---|------|------|---------|
| 6 | Barcode label printing from verification page | 30 min | Currently only from Asset show page. Add print button to Verify modal. |
| 7 | Dashboard custodian_name → user FK | 30 min | Replace string matching with proper user relationship |
| 8 | Import remaining SNS DO data | 1 hr | If DO SNS.pdf hasn't been imported yet |
| 9 | Remove DummyKewpaSeeder placeholder data | 10 min | 12 pre-seeded rows |

### P2 — Polish & Tech Debt (est. 1-2 days)

| # | Task | Est. | Details |
|---|------|------|---------|
| 10 | Unify CSV/Excel parsers in ImportController | 15 min | Extract shared base class |
| 11 | Add PA-4/PA-5/PA-8 Blade PDF templates | 1 hr | Currently React-only output; add Blade for consistent PDF style |
| 12 | Add PA-15/PA-29 letter-format PDF | 30 min | Committee appointment letters |

### Stretch — Nice To Have

| # | Task | Notes |
|---|------|-------|
| 13 | Docker Compose for reproducible dev environment | Laravel + PostgreSQL + OCR in containers |
| 14 | Bilingual support (BM/EN toggle) | Current UI is mixed — some labels in BM, some in EN |
| 15 | SAGA integration | Sync asset data with Malaysia's national SAGA system |
| 16 | E2E test suite | Automated browser tests for all CRUD workflows |

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

**117 tests, 538 assertions** across 13 test files:

| Test File | Tests | Assertions | Coverage |
|-----------|-------|------------|----------|
| `ImportPipelineTest.php` | 13 | 57 | PDF import, CSV import, column matching, dedup |
| `RbacTest.php` | 20 | 31 | Route permissions, role gating, middleware |
| `RolesTest.php` | 15 | 29 | Role assignment, user management |
| `VerificationPipelineTest.php` | 12 | 45 | Receive flow, Verify flow, item status transitions |
| `ScannerTest.php` | 10 | 42 | Scanner lookup, status-aware actions |
| `MasterItemLifecycleTest.php` | 8 | 22 | CRUD, filtering, master item status |
| `PaFormViewTest.php` | 6 | 9 | PDF form rendering, field presence |
| `ProfileTest.php` | 5 | 16 | User profile, password change |
| `VerifyEmailFullFlowTest.php` | 4 | 8 | Email verification lifecycle |
| `EmailVerificationTest.php` | 3 | 5 | Verification mail, resend |
| `EndToEndPipelineTest.php` | 1 | 51 | Full pipeline: import → receive → verify → asset check |
| `ExampleTest.php` (Unit) | 1 | 1 | Default |
| `EmailLogTest.php` | 1 | 3 | Mail log assertion |

```bash
# Run all tests
php artisan test

# Run specific suite
php artisan test --filter=VerificationPipelineTest
php artisan test --filter=RbacTest

# Run with coverage (requires Xdebug)
php artisan test --coverage
```

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
SELECT name, COUNT(*) as cnt FROM suppliers GROUP BY name HAVING COUNT(*) > 1;
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
| **Architecture & Features** | `README.md` | Updated 2026-07-24 — describes all modules, pipeline, RBAC, soft delete |
| **Current State + Roadmap** | `plans/current-state-and-roadmap.md` | Detailed system state, data flow, sidebar nav, edge cases, next steps |
| **Last Progress Report** | `plans/progress-2026-07-24.md` | What was completed in the last working session |
| **RBAC Implementation** | `plans/user-and-roles-module.md` | 5-role design, PermissionMiddleware, migration plan (ALL PHASES COMPLETE) |
| **Director Feedback** | `plans/user-roles-and-sidebar-flow.md` | Original requirements for role separation and sidebar restructure |
| **Test Methodology** | `plans/test-methodology-and-coverage.md` | Test file breakdown, what each test covers |
| **System Audit (stale)** | `plans/system-assessment.md` | From June 2026 — PO/DO tables described no longer exist. Use current-state-and-roadmap.md instead. |
| **Ground Truth Forms** | `KOLEKSI BORANG KEW.PA.pdf` | Official PDF of all 34 KEW.PA forms — the specification document |
| **OCR Engine** | `bin/cairo-ocr` | 2,069-line Python — the code is the documentation |
| **This Handover** | `HANDOVER.md` | You are here |


---

> *"A comprehensive, digital, and user-friendly asset management system for Universiti Teknologi Malaysia."*


---
