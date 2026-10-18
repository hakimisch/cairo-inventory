# CAIRO Inventory — UTM Asset Management System

> **A Comprehensive Web-Based Asset Management Platform** for managing the full lifecycle of movable and immovable assets at Universiti Teknologi Malaysia (UTM), covering all 34 KEW.PA forms mandated by the Malaysian Ministry of Finance.

---

## Executive Summary

| Attribute | Detail |
|-----------|--------|
| **Project** | CAIRO Inventory — Digital Asset Lifecycle Management |
| **Target** | UTM Asset Management Centre (Pusat Pengurusan Aset) |
| **Scope** | 34 KEW.PA forms × 16 functional modules + procurement pipeline |
| **Campus Coverage** | UTM Johor Bahru & UTM Kuala Lumpur |
| **Status** | **Phase 1** (16 KEW.PA lifecycle modules) — all built, seeded with demo data, ground-truth verified. **Procurement pipeline** — 156 master items imported from real supplier documents, 2-step Receive→Verify pipeline **exercised** (2026-07-27: 15 assets, 2 PA-1 records). **RBAC** — 5 roles with granular permissions, email verification, soft-delete recycle bin. |
| **Purpose** | Thesis demonstration & pilot deployment |
| **Deployment** | AWS Elastic Beanstalk + RDS + S3 (Singapore region) |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | React 18 + Inertia.js + Vite + Chart.js |
| **Backend** | Laravel 12 (PHP 8.4) |
| **Database** | PostgreSQL 18 |
| **PDF Engine** | Spatie Laravel-PDF + Browsershot (Chromium) — 35 Blade templates covering all 34 KEW.PA forms |
| **OCR Pipeline** | Python (PyMuPDF + Tesseract) — 2,080-line CLI engine `bin/cairo-ocr` |
| **UI Components** | Headless UI + Tailwind CSS |
| **Brand Identity** | UTM maroon (`#5C001F`) & gold (`#F8A617`) |
| **Auth** | Laravel Breeze (session-based, 5 roles + email verification) |
| **Assets** | Vite build pipeline |

### Why Inertia.js?

Inertia.js bridges Laravel and React without a separate API layer. Data flows directly from Laravel Controllers to React components via `Inertia::render()`, eliminating the need for REST API endpoints while preserving a true single-page application experience.

---

## System Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                    Browser — React SPA (65 page components)        │
│    Inertia.js ─── React Components ─── User Interactions          │
└──────────────────────────┬───────────────────────────────────────┘
                           │ Data + Routing (Inertia)
                           ▼
┌──────────────────────────────────────────────────────────────────┐
│                         Laravel Backend                           │
│  ┌──────────┐  ┌─────────────────┐  ┌────────────────────────┐  │
│  │ Routes   │  │ Controllers     │  │ Eloquent Models        │  │
│  │ web.php  │──│ (28 business +  │──│ (21 models)           │  │
│  │ ~200 rts │  │  9 auth = 37)   │  │                        │  │
│  └──────────┘  └─────────────────┘  └───────────┬────────────┘  │
│                                                  │                │
│  ┌───────────────────────────────────────────────┘                │
│  │  PostgreSQL (59 migrations — 17 business + 9 system tables)   │
│  └──────────────────────────────────────────────────────────────┘ │
│                              │
│  ┌───────────────────────────┘
│  │  Spatie Laravel-PDF + Browsershot (Chromium)
│  │  → 35 KEW.PA form templates (Blade) — all 34 forms
│  └──────────────────────────────────────────────────────────────┘
│                              │
│  ┌───────────────────────────┘
│  │  OCR & Import Pipeline (Python + Tesseract)
│  │  → 2,080-line engine parsing PO/DO/Quotation PDFs
│  │  → CamScanner watermark + confidence scoring
│  │  → CLI (`cairo:import-pdf`) + Web UI (`/import`)
│  │  → Brand extraction (KNOWN_BRANDS priority), embedded S/N parsing
│  └──────────────────────────────────────────────────────────────┘
│                              │
│  ┌───────────────────────────┘
│  │  Services Layer
│  │  → MasterItemImportService (shared import logic)
│  │  → OcrService (Python bridge without re-exec)
│  │  → AssetTagGenerator (unified AST-/REC- numbering, Cache::lock)
│  └──────────────────────────────────────────────────────────────┘
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│              Deployment: AWS Elastic Beanstalk                     │
│    Elastic Beanstalk (Laravel) ── RDS (PostgreSQL) ── S3          │
│    Tailscale VPN → Developer SSH access (WSL dev env)             │
└──────────────────────────────────────────────────────────────────┘
```

### Data Flow (Procurement Pipeline)

```
Upload at /import or /pdf-import
    → OCR (bin/cairo-ocr — Python + Tesseract, 2,080 lines)
    → Preview & edit items in editable grid
    → Confirm → master_items table (canonical register, unique by item_no)
    → /verification page
        → Receive (all roles) → stores receiver metadata, status → diterima
        → Verify (permission:verify) → PA-1 + Assets created atomically
            → per-S/N split; warranty + budget_vot + voucher_no captured
    → /scanner panel on verification page
        → Scan S/N → highlights the matching row; Receive / Verify run from the row actions
    → Print KEW.PA-2 / PA-3 forms with individual S/Ns
```

### Data Flow (Legacy Lifecycle — Phase 1)

1. **Request** → User navigates a URL → Laravel route resolves
2. **Data** → Controller fetches/validates data → passes via `Inertia::render()`
3. **Render** → React renders full page (sidebar, header, content) from received props
4. **CRUD** → Inline forms submit PUT/POST/DELETE through Inertia — no page reload
5. **PDF** → Controller renders Blade template → Spatie/Browsershot generates PDF → download response
6. **OCR Import** → Upload PDF → Python engine extracts items → preview grid (confidence %, raw text fallback) → confirm import → DB write

---

## 16 Modules — Full CRUD Coverage (Phase 1)

| # | Module | KEW.PA Forms | Description | Status |
|---|--------|--------------|-------------|--------|
| 1 | **Asset Receiving** | PA-1 / PA-1A | Receive new assets, register suppliers, PO/DO/Invoice docs | ✅ Complete |
| 2 | **Asset Registration** | PA-2 / PA-3 | Register assets (34 fields), photos, specifications | ✅ Complete |
| 3 | **Asset Movement** | PA-6 | Transfer assets between locations and custodians | ✅ Complete |
| 4 | **Asset Placement/Loan** | PA-9A | Loan assets to staff or students | ✅ Complete |
| 5 | **Asset Inspection** | PA-10 / PA-11 | Fixed asset and inventory periodic inspection records | ✅ Complete |
| 6 | **Damage Reports** | PA-9 | Fault reporting and repair actions for fixed assets & inventory | ✅ Complete |
| 7 | **Maintenance** | PA-13 / PA-14 | Maintenance list and register for fixed assets | ✅ Complete |
| 8 | **Asset Upgrades** | PA-2 (Section B) | Add RAM/SSD/components to existing assets | ✅ Complete |
| 9 | **Asset Disposal** | PA-17 / PA-18 / PA-19 | Disposal application, destruction cert, and disposal cert workflow | ✅ Complete |
| 10 | **Vehicle Disposal** | PA-16 | Vehicle valuation for disposal (PEP) | ✅ Complete |
| 11 | **Asset Sale (Tender/Quotation/Auction)** | PA-21 → PA-27A | 3-channel sale pipeline: Tender → Quotation → Auction (8 forms) | ✅ Complete |
| 12 | **Asset Loss** | PA-28 → PA-32 | Loss chain: initial report → investigation → final report → write-off → action | ✅ Complete |
| 13 | **Committees** | PA-15 / PA-29 | Appointment of disposal inspection & loss investigation committees | ✅ Complete |
| 14 | **Admin Dashboard** | — | Statistics, charts, system alerts | ✅ Complete |
| 15 | **Annual Reports** | PA-4/5/7/8/12/20 | 6 annual reports (admin only) | ✅ Complete |
| 16 | **KEW.PA Directory** | /kewpa | Searchable grid of all 34 KEW.PA forms | ✅ Complete |

> **Phase 1 complete:** 16 modules with full CRUD, seeded demo data (10+ records each), verified against official KEW.PA PDF.

---

## Procurement Pipeline (Phase 2)

The procurement pipeline bridges the gap between supplier PDF documents and the asset database — replacing manual data entry with automated OCR extraction, barcode reconciliation, and a 2-step Receive→Verify workflow.

### Pipeline Architecture

```
                                ┌─────────────────────┐
                                │   Supplier PDF/CSV   │
                                └─────────┬───────────┘
                                          │ OCR (Python)
                                          ▼
                                ┌─────────────────────┐
                                │   Editable Preview   │
                                │   Grid (confidence)  │
                                └─────────┬───────────┘
                                          │ Confirm
                                          ▼
                                ┌─────────────────────┐
                                │   master_items       │
                                │   (156 items)        │
                                └─────────┬───────────┘
                                          │
                    ┌─────────────────────┼─────────────────────┐
                    │                     │                     │
                    ▼                     ▼                     ▼
            ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
            │ Scanner S/N  │     │  Receive      │     │  Verify      │
            │ (status-     │     │  (all roles)  │     │  (permission:│
            │  aware)      │     │  → metadata   │     │  verify)     │
            │              │     │  stored       │     │  → PA-1 +    │
            └──────────────┘     └──────────────┘     │  Assets      │
                                                      └──────────────┘
                                                              │
                                                              ▼
                                                    ┌─────────────────────┐
                                                    │  KEW.PA-2/PA-3      │
                                                    │  (per-S/N split)     │
                                                    └─────────────────────┘
```

### 2-Step Pipeline: Receive → Verify

```
belum_diterima  ──[Receive]──→  diterima  ──[Verify]──→  verified
                                     │                        │
                              receiver metadata        PA-1 + assets created
                              stored as JSON           atomically (warranty,
                              (receive_meta)           budget_vot, voucher_no)
```

- **Receive**: Captures DO#, PO#, receiver name + position, tech officer name + position, damage description, notes, invoice no, **quantity_received** (supports partial). Stores the metadata on the master item and moves it to `diterima` — no PA-1 is created yet.
- **Verify**: Reads the stored metadata and creates the **PA-1 (Receiving) + Asset(s) atomically in one transaction** — per-S/N asset splitting, location/custodian/warranty/budget_vot/voucher_no capture, atomic asset tag generation. **Gated by `permission:verify`** (superadmin / admin / user roles).

### Real Data Imported

| Source | Supplier | Type |
|--------|----------|------|
| `PT DOT COM.pdf` | Dotcom Telecom | Purchase Order |
| `DOTCOM DO.pdf` | Dotcom Telecom | Delivery Order |
| `DO SNS - First.pdf` | SNS Network | Delivery Order |
| `JADUAL HARGA DOTCOM.pdf` | Dotcom Telecom | Tender pricing |

### Key Numbers

| Metric | Value |
|--------|-------|
| OCR engine size | **2,080 lines** (Python, PyMuPDF + Tesseract) |
| Document type detectors | 4 (PO, DO, Quotation, Unknown) |
| CamScanner detection | >60% threshold → forces full OCR pass |
| Brand extraction | KNOWN_BRANDS priority list with fallback `/` split |
| Embedded S/N extraction | Extracts S/N patterns from description text |
| Import interfaces | CLI (`cairo:import-pdf`) + Web UI (`/import`, `/pdf-import`) |
| Preview confidence | Per-item %, raw text fallback for low-confidence |
| Suppliers managed | **2 canonical** (Dotcom Telecom, SNS Network) |

### Import Web UI

- **Unified Import Page** (`/import`) — PDF, CSV, Excel, or pasted text
- **Editable Preview Grid** — review extracted items before commit (add row, delete, edit fields)
- **Confidence Indicators** — colour-coded badges (green/yellow/red) per extracted field
- **Raw Text Fallback** — full OCR output visible alongside structured preview
- **Error Handling** — inline error banner on import failure

### Scanner Module

Scanner panel on the Verification page (`/verification?scan=1`):

| Scan Result | Item Status | Action |
|-------------|-------------|--------|
| MATCH | `belum_diterima` | **Receive** (orange) — inline form; PA-1 is NOT created here |
| MATCH | `diterima` | **Verify** (blue) — inline form; creates PA-1 + the PA-2/PA-3 asset |
| DUPLICATE | `verified` / has asset | Info only — shows asset tag |
| MISMATCH | Not found | Info only |

---

## RBAC System

### 5 Roles

| Role | Permissions |
|------|-------------|
| **superadmin** | `manage_users`, `verify`, `crud_assets`, `force_delete`, `view_reports`, `view_admin` |
| **admin** | `verify`, `crud_assets`, `view_reports`, `view_admin` |
| **manager** | `view_reports` |
| **auditor** | `view_reports` |
| **user** (staff) | Can import, receive, scan, fill forms, and **verify** (`verify` permission). Cannot delete. |

### Features

- **PermissionMiddleware + RoleMiddleware** — route-level gating via `->middleware('permission:*')`
- **Email verification** — `MustVerifyEmail` enforced on all routes
- **Role badges** — Distinct coloured badges for all 5 roles in sidebar footer
- **Verify gating** — backend gated by `permission:verify` (superadmin/admin/user). Frontend button gated by the same rule via the `can_verify` prop (fixed 2026-10-08 — previously hardcoded to admin/superadmin)
- **Delete gating** — Hapus button hidden from non-admin/superadmin
- **User management** — `/admin/users` with role assignment (superadmin only)

---

## Soft Delete + Recycle Bin

All 19 deletable models use `Illuminate\Database\Eloquent\SoftDeletes`. The UI:

- **14 modules have trash tabs** — every CRUD index page has a "Tong (N)" toggle (admin/superadmin only) showing soft-deleted records with "Pulihkan" (Restore) buttons: Assets, Receivings, Transfers, Damage Reports, Placements, Inspections, Maintenances, Disposals, Loss Reports, Vehicle Disposals, Disposal Sales, Suppliers, Committee Appointments, and **Master Items** (Verification Pipeline, added 2026-10-08)
- **Delete buttons** gated by `permission:crud_assets` (admin/superadmin); the Verification pipeline's `Hapus` uses the `can_delete` prop derived from the same config
- **Permanent delete from the trash** — `force_delete` permission (**superadmin only**), `DELETE /{resource}/{id}/force` per module. Implemented for **Master Items, Assets, Receivings, Suppliers, Committee Appointments, Disposal Sales** (2026-10-18); the remaining trash bins are restore-only. The UI's `Padam Kekal` button requires typing `PADAM` to confirm, the purge only accepts rows already in the Tong, and it is written to the audit log as a `force_deleted` event with a snapshot
- **Purge guards** — an asset with a disposal / loss report / damage report / vehicle-disposal record cannot be purged (routine history such as placements, inspections, maintenances, transfers and upgrades is removed with it); a supplier still referenced by master items, a disposal sale with items, and a PA-1 that owns an asset are refused with a message naming the dependency
- **PA-1 delete** — a KEW.PA-1 can be moved to the Tong in **any** status as long as it has produced no asset (`assets.receiving_id`, added 2026-10-18, is stamped by `verify()` and `acceptReceiving()`); before that change only `pending` rows were deletable, so every PA-1 created by Verify was permanent
- **Master item guard** — an item that already has a PA-1 receiving or PA-2/PA-3 asset record cannot be deleted; the API rejects it with an inline error so the register is never orphaned
- **Restore endpoints**: `POST /{resource}/{id}/restore` per module (e.g. `/assets/{id}/restore`, `/receivings/{id}/restore`)
- **Activity logging**: All soft-delete and restore actions logged via Spatie Activitylog

---

## 34 KEW.PA Form Map

*Official form names extracted from KOLEKSI BORANG KEW.PA.pdf (ground truth reference)*

### Logistics & Receiving
| Form | Official Name (BM) | English | Status |
|------|--------------------|---------|--------|
| KEW.PA-1 / PA-1A | Borang Laporan Penerimaan Aset Alih Universiti | Asset Receiving Report | ✅ Ready |

### Asset Registration
| Form | Official Name (BM) | English | Status |
|------|--------------------|---------|--------|
| KEW.PA-2 | Daftar Harta Tetap | Fixed Asset Register | ✅ Ready |
| KEW.PA-3 | Daftar Inventori | Inventory Register | ✅ Ready |

### Movement & Inspection
| Form | Official Name (BM) | English | Status |
|------|--------------------|---------|--------|
| KEW.PA-6 | Daftar Pergerakan Harta Tetap dan Inventori | Asset Movement Register | ✅ Ready |
| KEW.PA-9 | Borang Aduan Kerosakan Harta Tetap dan Inventori | Asset Damage Report | ✅ Ready |
| KEW.PA-9A | Borang Penyerahan/Pinjaman Peralatan Milik UTM | Equipment Loan/Handover Form | ✅ Ready |
| KEW.PA-10 | Laporan Pemeriksaan Harta Tetap | Fixed Asset Inspection Report | ✅ Ready |
| KEW.PA-11 | Laporan Pemeriksaan Inventori | Inventory Inspection Report | ✅ Ready |
| KEW.PA-12 | Sijil Tahunan Pemeriksaan Harta Tetap dan Inventori | Annual Inspection Certificate | ✅ Ready |

### Maintenance
| Form | Official Name (BM) | English | Status |
|------|--------------------|---------|--------|
| KEW.PA-13 | Senarai Harta Tetap Yang Memerlukan Penyelenggaraan | Asset Maintenance List | ✅ Ready |
| KEW.PA-14 | Daftar Penyelenggaraan Harta Tetap | Fixed Asset Maintenance Register | ✅ Ready |

### Disposal & Sale
| Form | Official Name (BM) | English | Status |
|------|--------------------|---------|--------|
| KEW.PA-15 | Lantikan Jawatankuasa Pemeriksa Pelupusan | Disposal Committee Appointment | ✅ Ready |
| KEW.PA-16 | Perakuan Pelupusan (PEP) Kenderaan Universiti | Vehicle Disposal Certificate | ✅ Ready |
| KEW.PA-17 | Permohonan/Perakuan Pelupusan Harta UTM | Asset Disposal Application/Certificate | ✅ Ready |
| KEW.PA-18 | Sijil Pengesahan Pemusnahan Aset Alih Universiti | Asset Destruction Certificate | ✅ Ready |
| KEW.PA-19 | Sijil Pelupusan Harta Tetap | Fixed Asset Disposal Certificate | ✅ Ready |
| KEW.PA-20 | Laporan Tahunan Pelupusan Aset Alih Universiti | Annual Asset Disposal Report | ✅ Ready |
| KEW.PA-21 | Kenyataan Tawaran Tender Pelupusan Aset Alih UTM | Tender Sale Notice | ✅ Ready |
| KEW.PA-22 | Borang Tender Pelupusan Aset Alih Universiti | Tender Sale Form | ✅ Ready |
| KEW.PA-23 | Jadual Buka Tender Pelupusan Aset Alih Universiti | Tender Opening Schedule | ✅ Ready |
| KEW.PA-24 | Kenyataan Tawaran Sebutharga Pelupusan Aset Alih UTM | Quotation Sale Notice | ✅ Ready |
| KEW.PA-25 | Borang Sebutharga Pelupusan Aset Alih Universiti | Quotation Sale Form | ✅ Ready |
| KEW.PA-26 | Jadual Buka Sebutharga Pelupusan Aset Alih Universiti | Quotation Opening Schedule | ✅ Ready |
| KEW.PA-27 | Kenyataan Jualan Lelongan Aset Alih Universiti | Auction Sale Notice (+11 auction rules) | ✅ Ready |
| KEW.PA-27A | Senarai Aset Yang Dilelong | Auction Asset List | ✅ Ready |

### Loss
| Form | Official Name (BM) | English | Status |
|------|--------------------|---------|--------|
| KEW.PA-28 | Laporan Awal Kehilangan Aset Alih Universiti | Initial Loss Report | ✅ Ready |
| KEW.PA-29 | Pelantikan Jawatankuasa Penyiasat Kehilangan | Loss Investigation Committee | ✅ Ready |
| KEW.PA-30 | Laporan Akhir Kehilangan Aset Alih Universiti | Final Loss Report (8-section) | ✅ Ready |
| KEW.PA-31 | Sijil Hapuskira Aset Alih Universiti | Asset Write-off Certificate | ✅ Ready |
| KEW.PA-32 | Laporan Tindakan Kehilangan Aset Alih Universiti | Loss Action Report | ✅ Ready |

### Annual Reports (Admin)
| Form | Official Name (BM) | English | Status |
|------|--------------------|---------|--------|
| KEW.PA-4 | Senarai Daftar Harta Tetap | Fixed Asset Register List | ✅ Ready |
| KEW.PA-5 | Senarai Daftar Inventori | Inventory Register List | ✅ Ready |
| KEW.PA-7 | Senarai Harta Tetap dan Inventori (Mengikut Lokasi) | Asset List by Location | ✅ Ready |
| KEW.PA-8 | Laporan Tahunan Harta Tetap dan Inventori | Annual Asset Report | ✅ Ready |

| Form count | Blade? | JSX? |
|------------|--------|------|
| 34 forms | 35 with Blade templates | All 34 with JSX pages |

> Interactive Directory: Visit `/kewpa` after login to browse, search, and filter all 34 KEW.PA forms.

---

## Key Features

### For Staff Users

- **Personal Dashboard** — asset summary and quick actions at a glance
- **Inline CRUD** — all forms support add/edit/delete directly on the list page (no separate pages)
- **Search & Filter** on every module list page
- **PDF Download** for every KEW.PA form
- **Full KEW.PA Layout** with UTM header/footer
- **Sidebar Navigation** — 7 lifecycle categories for easy discovery
- **Import & Scanner** — upload PDFs, scan S/N barcodes

### For Administrators

- **Everything in Staff** + full access
- **Admin Dashboard** with:
  - System statistics (total assets, total value, active/under-repair counts)
  - Smart alerts (pending receiving, due maintenance, expiring warranties)
  - Pie charts (asset distribution by category and type)
  - Campus comparison (UTM JB vs. UTM KL)
  - High-value asset rankings
  - KEW.PA quick-access grid
- **6 Annual Reports** with PDF download
- **User Management** — add, edit, delete, change roles (superadmin only)
- **Verify Pipeline** — approve imported items, split by S/N, create assets
- **Soft Delete Recycle Bin** — restore deleted assets, receivings, and suppliers

### Procurement Features

- **Supplier Management** — manage suppliers with contact info
- **Unified Import** (`/import`) — PDF, CSV, Excel, or pasted text
- **OCR Extraction** — auto-detect PO/DO/Quotation, extract items with confidence scoring
- **Editable Preview Grid** — review, edit, add, or delete rows before import
- **Scanner Module** (`/verification?scan=1`) — status-aware S/N lookup; the matching row is highlighted and Receive / Verify run from that row's actions
- **Verification Dashboard** (`/verification`) — master items registry, Receive & Verify workflow
- **Partial Receive** — receive fewer items than ordered, remaining tracked
- **Per-S/N Asset Splitting** — items with multiple S/Ns create individual assets
- **MasterItemImportService** — shared import logic across all import interfaces

### User Interface

- **UTM Branding** — maroon (#5C001F) and gold (#F8A617) colour palette
- **Responsive Layout** — optimised for desktop and tablet
- **8-Section Sidebar** — asset lifecycle organised by phase
- **Role Badges** — colour-coded role indicators in sidebar footer
- **Toast Notifications** — instant action feedback
- **Delete Confirmation** — modal dialogs before destructive actions

---

## Login Reference

| Role | Email | Password |
|------|-------|----------|
| **Super Admin** | `user@gmail.com` | `password` |
| **Admin** | `admin@cairo.utm` | `password` |
| **Manager** | `manager@cairo.utm` | `password` |
| **Auditor** | `auditor@cairo.utm` | `password` |
| **Staff** | `ibrahim@cairo.utm` | `password` |

> Deployment admin (`admin@cairo.test`) is also created by `AdminUserSeeder` for environments where the demo accounts are not seeded.

**superadmin** can access everything — user management, admin dashboard, annual reports, verify pipeline, all asset modules.

**admin** can access everything except user management.

**manager/auditor** can view annual reports and the admin dashboard.

**user (staff)** can access personal dashboard, import/scan, all asset management modules (except annual reports and user management), KEW.PA directory.

---

## Installation & Setup

### Prerequisites

| Requirement | Version |
|-------------|---------|
| PHP | ≥ 8.2 |
| Composer | ≥ 2.0 |
| Node.js | ≥ 18 |
| PostgreSQL | ≥ 15 |
| Python | ≥ 3.10 (for OCR pipeline) |
| Tesseract OCR | ≥ 5.0 (for OCR pipeline) |
| Chromium | (for PDF generation) |

### Steps

```bash
# 1. Clone the repository
git clone <repo-url>
cd cairo-inventory

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment configuration
cp .env.example .env
# Edit .env — set DB_CONNECTION=pgsql and your PostgreSQL credentials

# 5. Generate application key
php artisan key:generate

# 6. Run migrations and seeders
php artisan migrate --seed

# 7. Install Python OCR dependencies
pip install -r requirements.txt

# 8. Build frontend assets
npm run build

# 9. Start development server
php artisan serve
# Or use Herd / Valet for production-like setup
```

### PDF Configuration

The system uses **Spatie Laravel-PDF** with **Browsershot** (Chromium). Set the Chromium binary path in `.env`:

```
LARAVEL_PDF_CHROME_PATH=/path/to/chrome
LARAVEL_PDF_NO_SANDBOX=true
```

### OCR Import

Import supplier PDFs via Artisan command:

```bash
php artisan cairo:import-pdf --file=path/to/po.pdf --type=po --supplier=1
php artisan cairo:import-pdf --file=path/to/do.pdf --type=do --supplier=1
```

Or via the web UI at `/import` or `/pdf-import`.

---

## Project Structure

```
cairo-inventory/
├── app/
│   ├── Console/Commands/
│   │   └── ImportPdfData.php              # CLI PDF import command
│   ├── Http/
│   │   ├── Controllers/                   # 37 controllers (28 business + 9 auth)
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   └── AdminUserController.php
│   │   │   ├── AssetController.php              # Assets + Receiving + Placement
│   │   │   ├── AssetDisposalController.php      # Disposal workflows (PA-17/18/19)
│   │   │   ├── AssetInspectionController.php    # Inspections (PA-10/11)
│   │   │   ├── AssetLossReportController.php    # Loss chain (PA-28→32)
│   │   │   ├── AssetMaintenanceController.php   # Maintenance records (PA-13/14)
│   │   │   ├── AssetTransferController.php      # Movement (PA-6)
│   │   │   ├── AssetUpgradeController.php       # Upgrades
│   │   │   ├── CommitteeAppointmentController.php
│   │   │   ├── DamageReportController.php
│   │   │   ├── DisposalSaleController.php       # Asset sales (PA-21→27A)
│   │   │   ├── DisposalSaleItemController.php
│   │   │   ├── ImportController.php             # Web import (CSV/Excel/PDF/text)
│   │   │   ├── KewpaDirectoryController.php     # KEW.PA directory
│   │   │   ├── PdfImportController.php          # OCR PDF import
│   │   │   ├── ReportController.php             # Annual reports
│   │   │   ├── SaleBidController.php
│   │   │   ├── ScanController.php               # Scanner module
│   │   │   ├── SupplierController.php           # Supplier CRUD
│   │   │   ├── VehicleDisposalAssessmentController.php
│   │   │   ├── VerificationController.php       # Receive/Verify pipeline
│   │   │   └── Auth/                            # 9 auth controllers (Breeze)
│   │   └── Requests/
│   │       └── StoreReceivingRequest.php
│   ├── Middleware/
│   │   ├── PermissionMiddleware.php        # 5-role RBAC enforcement
│   │   └── RoleMiddleware.php
│   ├── Models/                             # 21 Eloquent models
│   │   ├── Asset.php, AssetDisposal.php, AssetInspection.php, ...
│   │   ├── AssetLossReport.php, AssetMaintenance.php, AssetPlacement.php
│   │   ├── AssetTransfer.php, AssetUpgrade.php, CommitteeAppointment.php
│   │   ├── DamageReport.php, DisposalSale.php, DisposalSaleItem.php
│   │   ├── FinalLossReport.php, Location.php, MasterItem.php, Receiving.php
│   │   ├── SaleBid.php, Scan.php, Supplier.php, User.php
│   │   └── VehicleDisposalAssessment.php
│   └── Services/
│       ├── AssetTagGenerator.php           # Unified AST-/REC- numbering (Cache::lock)
│       ├── MasterItemImportService.php     # Shared import logic (replaced 3 duplicates)
│       └── OcrService.php                  # Python bridge without re-exec
├── bin/
│   └── cairo-ocr                           # 2,080-line OCR/import CLI (Python)
├── config/
│   ├── cairo.php                           # asset_threshold, receive_no_prefix, registration_enabled
│   └── roles.php                           # 5-role permissions matrix
├── database/
│   ├── migrations/                         # 59 migration files
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminUserSeeder.php             # Creates deployment admin (admin@cairo.test)
│       ├── LocationSeeder.php
│       ├── MasterItemSeeder.php            # 156 canonical master items
│       ├── SupplierSeeder.php
│       └── KewpaDataSeeder.php
├── resources/
│   ├── js/Pages/                           # 65 React page components
│   │   ├── Admin/
│   │   ├── Assets/                         # KEW.PA-1/2/3/6/9/9A/10/13/16/17/28/30/31/32
│   │   ├── CommitteeAppointments/
│   │   ├── DisposalSales/                  # KEW.PA-21→27A
│   │   ├── Disposals/                      # KEW.PA-18/19
│   │   ├── Import/                         # OCR import UI
│   │   ├── Inspections/                    # KEW.PA-11
│   │   ├── Items/                          # Unified items view
│   │   ├── Maintenances/                   # KEW.PA-14
│   │   ├── Profile/
│   │   ├── Reports/                        # KEW.PA-4/5/7/8/12/20
│   │   ├── Scanner/                        # Scanner UI
│   │   ├── Suppliers/                      # Supplier pages
│   │   ├── Verification/                   # Receive/Verify pipeline
│   │   └── Auth/
│   ├── js/Layouts/
│   │   ├── AuthenticatedLayout.jsx         # Sidebar + header + role badges
│   │   └── GuestLayout.jsx
│   ├── js/Components/                      # 16 reusable components
│   └── views/pdfs/                         # 35 KEW.PA PDF Blade templates
├── routes/
│   └── web.php                             # ~200 routes (auth + pipeline + lifecycle)
├── requirements.txt                        # Python OCR dependencies
└── public/build/                           # Compiled frontend assets
```

---

## Development Status

| Component | Status |
|-----------|--------|
| CRUD Frontend (16 modules) | ✅ 100% Complete |
| Sidebar Navigation + Role Badges | ✅ 100% Complete |
| PDF Generation (35 templates) | ✅ Configured & Verified |
| Admin Dashboard | ✅ Fully Built |
| Annual Reports (6) | ✅ Fully Built |
| KEW.PA Directory | ✅ Fully Built |
| Seed Data (demo + canonical) | ✅ Complete |
| **Ground Truth Alignment (4 tiers)** | **✅ Complete** |
| **OCR Engine (2,080 lines)** | **✅ Built & Hardened** |
| **Import Pipeline (PDF/CSV/Excel/Paste)** | **✅ 156 master items imported** |
| **2-Step Receive→Verify Pipeline** | **✅ Exercised 2026-07-27 — 15 assets, 2 PA-1 records** |
| **Per-S/N Asset Splitting** | **✅ Complete** |
| **RBAC (5 roles + email verification)** | **✅ Complete** |
| **Soft Delete + Recycle Bin (19 models, 14 modules)** | **✅ Complete** — Master Items added 2026-10-08 (restore + superadmin-only permanent delete) |
| **Scanner Module (status-aware)** | **✅ Built & integrated into verification page** |
| **Partial Receive Quantity** | **✅ Complete** |
| **budget_vot + voucher_no capture** | **✅ Added to Verify modal** |
| **PO Reference Field** | **✅ Added to Receive modal** |
| **MasterItemImportService** | **✅ Extracted (replaced 3 duplicates)** |
| **AssetTagGenerator** | **✅ Unified AST-/REC- numbering with Cache::lock** |
| **Supplier Dedup** | **✅ Executed — 5 → 2 canonical suppliers via `cairo:dedup-suppliers`** |
| **Registration Gating** | **✅ `REGISTRATION_ENABLED` (default off), routes unregistered when disabled** |
| **OCR Hardening (brand, S/N, qty, format)** | **✅ Complete** |
| Activity Logging | ✅ `spatie/activitylog` — ~988 entries |

### Ground Truth Alignment — Completed ✅

| Tier | What was built | Status |
|------|----------------|--------|
| **Tier 1** | PA-30 (8-section Final Loss Report), PA-27 auction rules (a-k), PA-21→PA-26 sale chain (6 new DB columns, channel-specific templates) | ✅ Complete |
| **Tier 2** | PA-11 (Inventory Inspection), PA-14 (Maintenance Register), PA-31 (Write-off Certificate), PA-32 (Annual Loss Action) | ✅ Complete |
| **Tier 3** | PA-18 (Destruction Certificate), PA-19 (Disposal Certificate) | ✅ Complete |
| **Tier 4** | 14 title corrections across PA-9A→PA-27A to match official PDF names | ✅ Complete |

---

## Database Overview

**17 business tables + 9 Laravel system tables** (59 migrations):

### Canonical Tables (Procurement Pipeline)

| Table | Records | Notes |
|-------|---------|-------|
| `master_items` | **156** | Primary equipment register — deduplicated, unique on item_no |
| `suppliers` | **2** (canonical — dedup executed) | Dotcom, SNS Network |
| `users` | **7** | 1 superadmin, 2 admin, 1 manager, 1 auditor, 2 staff |
| `assets` | **15** | Created on verification — one per S/N (pipeline exercised 2026-07-27) |
| `receivings` | **2** | PA-1 records — created at Verify time |
| `scans` | **0** | Scanner module built, awaiting production use |
| `activity_log` | **~988** | Spatie Activitylog audit trail |
| `media` | **1** | PT-DOT-COM.pdf |
| `locations` | **0** | Location lookup table (LocationSeeder available) |

### Master Items by Status

| Status | Count | With Asset |
|--------|-------|------------|
| `belum_diterima` | 154 | 0 |
| `diterima` | 0 | 0 |
| `verified` | 2 | 15 |
| **Total** | **156** | **15** |

### Demo Data (Phase 1 Seed)

> **Note:** The live DB was reset (2026-07-31) — all demo tables are now empty. `php artisan db:seed` / `DatabaseSeeder` re-seeds them. `DatabaseSeeder` also creates the 5 role accounts (superadmin/admin/manager/auditor/staff) plus master items (156) via `MasterItemSeeder`.

---

## Recent Milestones (2026-07-24)

| Area | What Changed |
|------|-------------|
| **RBAC** | 2 roles → 5 roles (superadmin/admin/manager/auditor/user), PermissionMiddleware, RoleMiddleware, MustVerifyEmail |
| **Soft Delete** | New migration adds `deleted_at` to 19 models. Tong recycle bin with Pulihkan restore on Assets & Receivings pages |
| **Pipeline** | DO/PO abstraction removed. Items go direct to master_items. 2-step Receive→Verify with partial qty support |
| **Verify Modal** | budget_vot + voucher_no fields added for PA-2/PA-3 compliance |
| **Receive Modal** | PO Reference field added between DO No. and Invoice No. |
| **Import Service** | MasterItemImportService extracted — replaces 3 duplicate implementations |
| **Supplier Dedup** | `GET /_dev/dedup-suppliers` route merges Dotcom (3 copies) and SNS (2 copies) |
| **OCR Hardening** | Brand extraction (KNOWN_BRANDS priority), embedded S/N parsing, qty fix, roman numeral → -A/-B/-C format |
| **Sidebar** | Role badges for all 5 roles, Logistik & Penerimaan grouping |
| **Verify Gating** | Verify button hidden from non-admin users — shows "Menunggu pengesahan pentadbir" |
| **Tests** | 117 tests, 538 assertions (pipeline, scanner, lifecycle, PA forms, RBAC, e2e) |

## Recent Milestones (2026-07-31)

| Area | What Changed |
|------|-------------|
| **Pipeline Restructure** | PA-1 creation moved from `receive()` to `verify()` — receiver metadata stored as JSON in `master_items.receive_meta`; PA-1 + Asset created atomically at Verify |
| **Threshold Config** | RM 2,000 extracted to `config('cairo.asset_threshold')` — single source of truth |
| **AssetTagGenerator** | New service — unified `AST-YYYYMMDD-NNNNNN` / `REC-YYYYMMDD-NNNN` numbering with `Cache::lock`; legacy `CAIRO/…` + `RC-` formats retired |
| **Trash Bin — 14 modules** | `restore()` methods + trashed data + Tong toggle UI added to 11 more controllers/pages (Transfers, Damage, Placements, Inspections, Maintenances, Disposals, Loss, Vehicle Disposal, Disposal Sales, Suppliers, Committees); Master Items added 2026-10-08 with restore + superadmin-only permanent delete |
| **RBAC Fix** | `user` role granted `verify` permission |
| **Registration Gating** | `REGISTRATION_ENABLED` (default **false**) — routes unregistered, Register link auto-hides |
| **Security (Phase 0)** | Ungated `/_dev/dedup-*` GET routes removed → artisan commands with `--dry-run`/`--force`; `kewpa9a()` null-deref fixed; session flashes → Inertia toasts (FlashToaster, de-emoji'd icons) |
| **Data Integrity (Phase 1)** | 32 bad OCR regex escapes fixed; `rejectReceiving()` no longer fabricates zero-value REJ- assets (records `rejection_reason`/`rejected_at`); import wrapped in `DB::transaction` + dedup guard (`imported`/`skipped` counts); scanner off-by-one fixed; qty × unit_price cross-check |
| **Supplier Dedup EXECUTED** | 5 → **2** suppliers (Dotcom id 1, SNS id 3); 156 master_items re-pointed via `php artisan cairo:dedup-suppliers --force` |
| **Welcome Page** | 26 stale entries → 34 KEW.PA forms with correct Malay names |
| **Sidebar Cleanup** | Dead `/items` + `/scanner` redirect routes removed; duplicate committee-appointments entry removed |
| **Tests** | **127 passed / 0 failed / 593 assertions** (2026-10-08 — the 2 stale failures are fixed) |

---

## Future Considerations

| Item | Notes |
|------|-------|
| **Fix 2 stale tests** ✅ | Done 2026-10-08 — EndToEndPipelineTest now asserts PA-1 at Verify, RbacTest asserts staff CAN verify → suite 127 pass / 0 fail |
| **Verify-button permission sync** ✅ | Done 2026-10-08 — `can_verify` / `can_delete` / `can_force_delete` props from `VerificationController` via `User::hasPermission()`; no hardcoded role checks left in the pipeline |
| **OCR confidence enforcement** | Confidence computed but nothing downstream acts on it — warn/block below threshold ~50 at import |
| **`acceptReceiving()` validation** | `AssetController.php:51-105` validates only `photo`; mirror `update()` rules + `$request->safe()` |
| **Jul-27 asset fate** | 14 assets created from a qty=1 master item under old split logic — verify against source DO, collapse or keep |
| **Dead-code cleanup** ⚠️ Partial | Done 2026-10-08: `ItemsController` + `Items/Index.jsx`, `ScanController::verifyFromScan()`/`receiveFromScan()`. Remaining: orphaned `Scanner/Index.jsx` + `History.jsx`, `Adminmiddleware.php`, unused seeders |
| **Barcode Label Printing** | Print scannable labels from verification page (currently only from Asset show) |
| **Dashboard Custodian FK** | Replace free-text `custodian_name` matching with user FK |
| **DB-level pagination** | Verification registry + Assets index load all rows into memory — paginate when data grows |
| **`orWhereYear` grouping** | `ReportController` kewpa12/kewpa20 ungrouped `whereYear(...)->orWhereYear(...)` — latent footgun |
| **OCR follow-ups** | F1: `total_amount` capture on scanned DOs; F2: supplier capture noise; F3: 10-min multi-page scans |
| **Docker Deployment** | Containerise Laravel + OCR + PostgreSQL for reproducible environments |
| **i18n / Bilingual Support** | Current UI is mixed BM/EN; add full language toggle |
| **SAGA Integration** | Sync asset data with Malaysia's national SAGA system |
| **E2E Testing** | Automated end-to-end tests for all CRUD workflows |
| **Cleanup** | Remove DummyKewpaSeeder placeholder data, unify CSV/Excel parsers |

---

## Codebase Statistics

| Metric | Count |
|--------|-------|
| React Pages | 65 |
| Reusable Components | 16 |
| Eloquent Models | 21 |
| Business Controllers | 28 |
| Auth Controllers | 9 |
| Total Controllers | **37** |
| Database Migrations | 59 |
| PDF Blade Templates | 35 |
| Business Tables | 17 |
| Route Definitions | **~200** |
| OCR Engine (Python) | 2,080 lines |
| Activity Log Entries | ~988 |
| Master Items Imported | 156 |
| Assets Created (pipeline) | 15 |
| Seed Records | 10+ per module |

---

## License

Copyright © 2026 CAIRO Lab, Universiti Teknologi Malaysia.

Built for thesis and academic research purposes.

---

> *"A comprehensive, digital, and user-friendly asset management system for Universiti Teknologi Malaysia."*
