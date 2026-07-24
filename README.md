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
| **Status** | **Phase 1** (16 KEW.PA lifecycle modules) — all built, seeded with demo data, ground-truth verified. **Procurement pipeline** — 170 master items imported from real supplier documents, 2-step Receive→Verify pipeline ready. Pipeline ready but awaiting production asset entry. **RBAC** — 5 roles with granular permissions, email verification, soft-delete recycle bin. |
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
| **OCR Pipeline** | Python (PyMuPDF + Tesseract) — 2,069-line CLI engine `bin/cairo-ocr` |
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
│  │ web.php  │──│ (28 business +  │──│ (20 models)           │  │
│  │ ~175 rts │  │  9 auth = 37)   │  │                        │  │
│  └──────────┘  └─────────────────┘  └───────────┬────────────┘  │
│                                                  │                │
│  ┌───────────────────────────────────────────────┘                │
│  │  PostgreSQL (56 migrations — 17 business + 9 system tables)   │
│  └──────────────────────────────────────────────────────────────┘ │
│                              │
│  ┌───────────────────────────┘
│  │  Spatie Laravel-PDF + Browsershot (Chromium)
│  │  → 35 KEW.PA form templates (Blade) — all 34 forms
│  └──────────────────────────────────────────────────────────────┘
│                              │
│  ┌───────────────────────────┘
│  │  OCR & Import Pipeline (Python + Tesseract)
│  │  → 2,069-line engine parsing PO/DO/Quotation PDFs
│  │  → CamScanner watermark + confidence scoring
│  │  → CLI (`cairo:import-pdf`) + Web UI (`/import`)
│  │  → Brand extraction (KNOWN_BRANDS priority), embedded S/N parsing
│  └──────────────────────────────────────────────────────────────┘
│                              │
│  ┌───────────────────────────┘
│  │  Services Layer
│  │  → MasterItemImportService (shared import logic)
│  │  → OcrService (Python bridge without re-exec)
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
    → OCR (bin/cairo-ocr — Python + Tesseract, 2,069 lines)
    → Preview & edit items in editable grid
    → Confirm → master_items table (canonical register, unique by item_no)
    → /verification page
        → Receive (mark as diterima) → PA-1 created immediately
        → Verify (admin/superadmin only) → per-S/N split into Assets
            → warranty + budget_vot + voucher_no captured
    → /scanner panel on verification page
        → Scan S/N → auto Receive (PA-1) or auto Verify based on status
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
                                │   (170 items)        │
                                └─────────┬───────────┘
                                          │
                    ┌─────────────────────┼─────────────────────┐
                    │                     │                     │
                    ▼                     ▼                     ▼
            ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
            │ Scanner S/N  │     │  Receive      │     │  Verify      │
            │ (status-     │     │  (all roles)  │     │  (admin/     │
            │  aware)      │     │  → PA-1       │     │  superadmin) │
            │              │     │  created      │     │  → Assets    │
            └──────────────┘     └──────────────┘     └──────────────┘
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
                                 PA-1 created            assets created
                                 (signature block)        (warranty, budget_vot,
                                                          voucher_no captured)
```

- **Receive**: Captures DO#, PO#, receiver name + position, tech officer name + position, damage description, notes, invoice no, **quantity_received** (supports partial). PA-1 created immediately.
- **Verify**: Per-S/N asset splitting, location/custodian/warranty/budget_vot/voucher_no capture, atomic asset tag generation. **Gated to admin/superadmin only.**

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
| OCR engine size | **2,069 lines** (Python, PyMuPDF + Tesseract) |
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
| MATCH | `belum_diterima` | **Receive** — inline form, creates PA-1 |
| MATCH | `diterima` | **Verify** — inline form, creates Asset |
| DUPLICATE | `verified` / has asset | Info only — shows asset tag |
| MISMATCH | Not found | Info only |

---

## RBAC System

### 5 Roles

| Role | Permissions |
|------|-------------|
| **superadmin** | `manage_users`, `verify`, `crud_assets`, `view_reports`, `view_admin` |
| **admin** | `verify`, `crud_assets`, `view_reports`, `view_admin` |
| **manager** | `view_reports` |
| **auditor** | `view_reports` |
| **user** (staff) | Can import, receive, scan, fill forms. Cannot verify or delete. |

### Features

- **PermissionMiddleware + RoleMiddleware** — route-level gating via `->middleware('permission:*')`
- **Email verification** — `MustVerifyEmail` enforced on all routes
- **Role badges** — Distinct coloured badges for all 5 roles in sidebar footer
- **Verify gating** — Verify button hidden from non-admin (shows "Menunggu pengesahan pentadbir")
- **Delete gating** — Hapus button hidden from non-admin/superadmin
- **User management** — `/admin/users` with role assignment (superadmin only)

---

## Soft Delete + Recycle Bin

All 19 deletable models use `Illuminate\Database\Eloquent\SoftDeletes`. The UI:

- **Assets page**: "Tong" filter tab shows soft-deleted assets with "Pulihkan" (Restore) button
- **Receivings page**: "Tong Sampah" bottom section with restore
- **Delete buttons** gated to `['admin', 'superadmin']` only
- **Restore endpoint**: `POST /assets/{id}/restore` / `POST /receivings/{id}/restore`
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
| KEW.PA-4 | Senarai Daftar Harta Tetap | Fixed Asset Register List | ✅ Ready (React-only, no Blade) |
| KEW.PA-5 | Senarai Daftar Inventori | Inventory Register List | ✅ Ready (React-only, no Blade) |
| KEW.PA-7 | Senarai Harta Tetap dan Inventori (Mengikut Lokasi) | Asset List by Location | ✅ Ready |
| KEW.PA-8 | Laporan Tahunan Harta Tetap dan Inventori | Annual Asset Report | ✅ Ready (React-only, no Blade) |

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
- **Scanner Module** (`/verification?scan=1`) — status-aware S/N lookup with auto-action
- **Verification Dashboard** (`/verification`) — master items registry, Receive & Verify workflow
- **Partial Receive** — receive fewer items than ordered, remaining tracked
- **Per-S/N Asset Splitting** — items with multiple S/Ns create individual assets
- **MasterItemImportService** — shared import logic across all import interfaces

### User Interface

- **UTM Branding** — maroon (#5C001F) and gold (#F8A617) colour palette
- **Responsive Layout** — optimised for desktop and tablet
- **7-Section Sidebar** — asset lifecycle organised by phase
- **Role Badges** — colour-coded role indicators in sidebar footer
- **Toast Notifications** — instant action feedback
- **Delete Confirmation** — modal dialogs before destructive actions

---

## Login Reference

| Role | Email | Password |
|------|-------|----------|
| **Super Admin** | `admin@cairo.utm` | `password` |
| **Admin** | `admin2@cairo.utm` | `password` |
| **Manager** | `manager@cairo.utm` | `password` |
| **Auditor** | `auditor@cairo.utm` | `password` |
| **Staff** | `user@gmail.com` | `password` |

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
│   │   │   ├── ItemsController.php              # Unified items view
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
│   ├── Models/                             # 20 Eloquent models
│   │   ├── Asset.php, AssetDisposal.php, AssetInspection.php, ...
│   │   ├── AssetLossReport.php, AssetMaintenance.php, AssetPlacement.php
│   │   ├── AssetTransfer.php, AssetUpgrade.php, CommitteeAppointment.php
│   │   ├── DamageReport.php, DisposalSale.php, DisposalSaleItem.php
│   │   ├── FinalLossReport.php, MasterItem.php, Receiving.php
│   │   ├── SaleBid.php, Scan.php, Supplier.php, User.php
│   │   └── VehicleDisposalAssessment.php
│   └── Services/
│       ├── MasterItemImportService.php     # Shared import logic (replaced 3 duplicates)
│       └── OcrService.php                  # Python bridge without re-exec
├── bin/
│   └── cairo-ocr                           # 2,069-line OCR/import CLI (Python)
├── config/
│   └── roles.php                           # 5-role permissions matrix
├── database/
│   ├── migrations/                         # 56 migration files
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminUserSeeder.php             # Pre-seeds all 5 example accounts
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
│   ├── js/Components/                      # 15 reusable components
│   └── views/pdfs/                         # 35 KEW.PA PDF Blade templates
├── routes/
│   └── web.php                             # ~175 routes (auth + pipeline + lifecycle)
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
| **OCR Engine (2,069 lines)** | **✅ Built & Hardened** |
| **Import Pipeline (PDF/CSV/Excel/Paste)** | **✅ 170 master items imported** |
| **2-Step Receive→Verify Pipeline** | **✅ Ready (not exercised on fresh DB)** |
| **Per-S/N Asset Splitting** | **✅ Complete** |
| **RBAC (5 roles + email verification)** | **✅ Complete** |
| **Soft Delete + Recycle Bin (19 models)** | **✅ Complete** |
| **Scanner Module (status-aware)** | **✅ Built & integrated into verification page** |
| **Partial Receive Quantity** | **✅ Complete** |
| **budget_vot + voucher_no capture** | **✅ Added to Verify modal** |
| **PO Reference Field** | **✅ Added to Receive modal** |
| **MasterItemImportService** | **✅ Extracted (replaced 3 duplicates)** |
| **Supplier Dedup Route** | **✅ Ready (`/dev/dedup-suppliers`)** |
| **OCR Hardening (brand, S/N, qty, format)** | **✅ Complete** |
| Activity Logging | ✅ `spatie/activitylog` — ~970 entries |

### Ground Truth Alignment — Completed ✅

| Tier | What was built | Status |
|------|----------------|--------|
| **Tier 1** | PA-30 (8-section Final Loss Report), PA-27 auction rules (a-k), PA-21→PA-26 sale chain (6 new DB columns, channel-specific templates) | ✅ Complete |
| **Tier 2** | PA-11 (Inventory Inspection), PA-14 (Maintenance Register), PA-31 (Write-off Certificate), PA-32 (Annual Loss Action) | ✅ Complete |
| **Tier 3** | PA-18 (Destruction Certificate), PA-19 (Disposal Certificate) | ✅ Complete |
| **Tier 4** | 14 title corrections across PA-9A→PA-27A to match official PDF names | ✅ Complete |

---

## Database Overview

**17 business tables + 9 Laravel system tables** (56 migrations):

### Canonical Tables (Procurement Pipeline)

| Table | Records | Notes |
|-------|---------|-------|
| `master_items` | **170** | Primary equipment register — deduplicated, unique on item_no |
| `suppliers` | **5** (needs dedup: 3 canonical) | Dotcom, SNS Network, auto-created from import |
| `users` | **7** | 1 superadmin, 2 admin, 1 manager, 1 auditor, 2 staff |
| `assets` | **0** | Created on verification — one per S/N |
| `receivings` | **0** | PA-1 records — created at Receive time |
| `scans` | **0** | Scanner module built, awaiting production use |
| `activity_log` | **~970** | Spatie Activitylog audit trail |
| `media` | **2** | PT-DOT-COM.pdf (291KB) + DOTCOM-DO.pdf (9.5MB) |

### Master Items by Status

| Status | Count | With Asset |
|--------|-------|------------|
| `belum_diterima` | 170 | 0 |
| `diterima` | 0 | 0 |
| `verified` | 0 | 0 |
| **Total** | **170** | **0** |

### Demo Data (Phase 1 Seed)

| Table | Records | Notes |
|-------|---------|-------|
| `assets` | **10** | 38 columns, demo records only (no real data yet) |
| `receivings` | **12** | Legacy seed from fictional companies — no FK to Phase 2 |
| `asset_placements` | 11 | Demo data |
| `asset_disposals` | 10 | Demo data |
| `asset_transfers` | 10 | Demo data |
| `asset_inspections` | 10 | Demo data |
| `asset_maintenances` | 10 | Demo data |
| `asset_loss_reports` | 10 | Demo data |
| `damage_reports` | 11 | Demo data |
| `vehicle_disposal_assessments` | 10 | Demo data |
| `disposal_sales` | 10 | Demo data |
| `asset_upgrades` | 10 | Demo data |
| `committee_appointments` | 12 | Pre-seeded placeholder |
| `disposal_sale_items` | 21 | Demo lots with reserve prices |
| `sale_bids` | 30 | Demo bids |
| `final_loss_reports` | 10 | 8-section investigation schema (PA-30) |

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

---

## Future Considerations

| Item | Notes |
|------|-------|
| **Exercise Pipeline** | Run Receive→Verify on fresh 170 master items to create production assets |
| **Barcode Label Printing** | Print scannable labels from verification page (currently only from Asset show) |
| **Dashboard Custodian FK** | Replace free-text `custodian_name` matching with user FK |
| **Docker Deployment** | Containerise Laravel + OCR + PostgreSQL for reproducible environments |
| **PA-4/5/8 Blade templates** | Add Blade PDF templates for these React-only forms |
| **PA-15/29 letter-format PDF** | Add PDF output for committee appointment letters |
| **i18n / Bilingual Support** | Current UI is mixed BM/EN; add full language toggle |
| **SAGA Integration** | Sync asset data with Malaysia's national SAGA system |
| **E2E Testing** | Automated end-to-end tests for all CRUD workflows |
| **Cleanup** | Remove DummyKewpaSeeder placeholder data, unify CSV/Excel parsers |

---

## Codebase Statistics

| Metric | Count |
|--------|-------|
| React Pages | 65 |
| Reusable Components | 15 |
| Eloquent Models | 20 |
| Business Controllers | 28 |
| Auth Controllers | 9 |
| Total Controllers | **37** |
| Database Migrations | 56 |
| PDF Blade Templates | 35 |
| Business Tables | 17 |
| Route Definitions | **~175** |
| OCR Engine (Python) | 2,069 lines |
| Activity Log Entries | ~970 |
| Master Items Imported | 170 |
| Seed Records | 10+ per module |

---

## License

Copyright © 2026 CAIRO Lab, Universiti Teknologi Malaysia.

Built for thesis and academic research purposes.

---

> *"A comprehensive, digital, and user-friendly asset management system for Universiti Teknologi Malaysia."*
