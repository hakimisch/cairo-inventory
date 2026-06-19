# CAIRO Inventory — UTM Asset Management System

> **A Comprehensive Web-Based Asset Management Platform** for managing the full lifecycle of movable and immovable assets at Universiti Teknologi Malaysia (UTM), covering all 34 KEW.PA forms mandated by the Malaysian Ministry of Finance.

---

## Executive Summary

| Attribute | Detail |
|-----------|--------|
| **Project** | CAIRO Inventory — Digital Asset Lifecycle Management |
| **Target** | UTM Asset Management Centre (Pusat Pengurusan Aset) |
| **Scope** | 34 KEW.PA forms × 16 functional modules + Phase 2 procurement pipeline |
| **Campus Coverage** | UTM Johor Bahru & UTM Kuala Lumpur |
| **Status** | **Phase 1** (16 KEW.PA lifecycle modules) — all built, seeded with demo data, ground-truth verified. **Phase 2** (Procurement OCR pipeline) — 1 real PO (51 items) + 3 real DOs (59 items) live-imported via CLI/web UI. **Assets table** awaiting production data entry. |
| **Purpose** | Thesis demonstration & pilot deployment |
| **Deployment** | AWS Elastic Beanstalk + RDS + S3 (Singapore region) |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | React 18 + Inertia.js + Vite + Chart.js |
| **Backend** | Laravel 12 (PHP 8.4) |
| **Database** | PostgreSQL 18 |
| **PDF Engine** | Spatie Laravel-PDF + Browsershot (Chromium) — 29 Blade templates covering all 34 KEW.PA forms |
| **OCR Pipeline** | Python (PyMuPDF + Tesseract) — 1,201-line CLI engine `bin/cairo-ocr` |
| **UI Components** | Headless UI + Tailwind CSS |
| **Brand Identity** | UTM maroon (`#5C001F`) & gold (`#F8A617`) |
| **Auth** | Laravel Breeze (session-based, 2 roles) |
| **Assets** | Vite build pipeline |

### Why Inertia.js?

Inertia.js bridges Laravel and React without a separate API layer. Data flows directly from Laravel Controllers to React components via `Inertia::render()`, eliminating the need for REST API endpoints while preserving a true single-page application experience.

---

## System Architecture

```
┌───────────────────────────────────────────────────────────────┐
│                    Browser — React SPA (58 page components)     │
│    Inertia.js ─── React Components ─── User Interactions       │
└──────────────────────┬────────────────────────────────────────┘
                       │ Data + Routing (Inertia)
                       ▼
┌───────────────────────────────────────────────────────────────┐
│                     Laravel Backend                            │
│  ┌──────────┐  ┌──────────────┐  ┌────────────────────────┐  │
│  │ Routes   │  │ Controllers  │  │ Eloquent Models        │  │
│  │ web.php  │──│ (27 business +│──│ (17 models)           │  │
│  │ ~130 rts │  │  10 auth + 1 │  │                        │  │
│  │          │  │  base = 38)  │  │                        │  │
│  └──────────┘  └──────────────┘  └───────────┬────────────┘  │
│                                               │                │
│  ┌────────────────────────────────────────────┘                │
│  │  PostgreSQL (26 tables from 34 migrations)                 │
│  └───────────────────────────────────────────────────────────┘
│                              │
│  ┌───────────────────────────┘
│  │  Spatie Laravel-PDF + Browsershot (Chromium)
│  │  → 29 KEW.PA form templates (Blade) — all 34 forms
│  └───────────────────────────────────────────────────────────┘
│                              │
│  ┌───────────────────────────┘
│  │  OCR & Import Pipeline (Python + Tesseract)
│  │  → 1,201-line engine parsing PO/DO/Quotation PDFs
│  │  → CamScanner watermark + confidence scoring
│  │  → CLI (`cairo:import-pdf`) + Web UI (`/import`)
│  └───────────────────────────────────────────────────────────┘
└───────────────────────────────────────────────────────────────┘

┌───────────────────────────────────────────────────────────────┐
│              Deployment: AWS Elastic Beanstalk                 │
│    Elastic Beanstalk (Laravel) ── RDS (PostgreSQL) ── S3      │
│    Tailscale VPN → Developer SSH access (WSL dev env)         │
└───────────────────────────────────────────────────────────────┘
```

### Data Flow

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

## Phase 2 — Procurement & OCR Pipeline 🆕

The Phase 2 pipeline bridges the gap between supplier PDF documents and the asset database — replacing manual data entry with automated OCR extraction, barcode reconciliation, and import workflows.

### Data Pipeline

```
Supplier PO PDF ──→ OCR Engine ──→ Preview Grid (confidence %) ──→ Confirm Import
                      (1,201 lines)       │                              │
                                          │                              ▼
                                    ┌──────┴──────┐            Purchase Order (51 items)
                                    │ Raw text     │                    │
                                    │ fallback     │                    ▼
                                    └──────────────┘          Delivery Order (59 items)
                                                                    │
                                         ┌──────────────────────────┤
                                         ▼                          ▼
                                   Barcode Scan              Verify Item
                                   (/scanner)                (Receive button)
                                         │                          │
                                         ▼                          ▼
                                    Auto-create Asset ─────→ KEW.PA-2/PA-3
```

### Real Data Imported

| Source | Supplier | Type | Extractable | Status |
|--------|----------|------|-------------|--------|
| `PT DOT COM.pdf` | Dotcom Telecom | Purchase Order | **51/51 items** (100%) | ✅ Imported |
| `DOTCOM DO.pdf` | Dotcom Telecom | Delivery Order | **59 items** (33 with serial numbers) | ✅ Imported |
| `DO SNS - First.pdf` | SNS Network | Delivery Order | ~11/18 items (partial — OCR limited) | 🟡 Partial |
| `JADUAL HARGA DOTCOM.pdf` | Dotcom Telecom | Tender pricing | Metadata only | ❌ Reference only |

### Key Numbers

| Metric | Value |
|--------|-------|
| OCR engine size | **1,201 lines** (Python, PyMuPDF + Tesseract) |
| Document type detectors | 4 (PO, DO, Quotation, Unknown) |
| CamScanner detection | >60% threshold → forces full OCR pass |
| Import interfaces | CLI (`cairo:import-pdf`) + Web UI (`/import`, `/pdf-import`) |
| Preview confidence | Per-item %, raw text fallback for low-confidence |
| Suppliers managed | **2 real** (Dotcom Telecom, SNS Network) |

### Import Web UI

- **Unified Import Page** (`/import`) — PDF, CSV, Excel, or pasted text
- **Editable Preview Grid** — review extracted items before commit (add row, delete, edit fields)
- **Confidence Indicators** — colour-coded badges (green/yellow/red) per extracted field
- **Raw Text Fallback** — full OCR output visible alongside structured preview
- **Error Handling** — inline error banner on import failure

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
| 34 forms | 29 with Blade templates | All 34 with JSX pages |

> 🌐 **Interactive Directory:** Visit `/kewpa` after login to browse, search, and filter all 34 KEW.PA forms.

---

## Key Features

### For Regular Staff

- **Personal Dashboard** — asset summary and quick actions at a glance
- **Inline CRUD** — all forms support add/edit/delete directly on the list page (no separate pages)
- **Search & Filter** on every module list page
- **PDF Download** for every KEW.PA form
- **Full KEW.PA Layout** with UTM header/footer
- **Sidebar Navigation** — 7 lifecycle categories for easy discovery

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
- **User Management** — add, edit, delete, change roles

### Procurement & OCR Features

- **Supplier Management** — manage 2 real suppliers with contact info
- **Purchase Order Import** — OCR-extract 51 items from supplier PDF
- **Delivery Order Import** — parse 59 DO items with serial numbers
- **Batch Import** (`/delivery-orders/batch-import`) — upload multiple PDFs at once
- **Scanner Module** (`/scanner`) — manual S/N entry with dynamic import
- **Verification Dashboard** (`/delivery-orders/verification`) — PO/DO progress summary
- **Confidence Scoring** — per-field extraction quality indicators
- **Raw Text Fallback** — full OCR output for low-confidence documents

### User Interface

- **UTM Branding** — maroon (#5C001F) and gold (#F8A617) colour palette
- **Responsive Layout** — optimised for desktop and tablet
- **7-Section Sidebar** — asset lifecycle organised by phase
- **Toast Notifications** — instant action feedback
- **Delete Confirmation** — modal dialogs before destructive actions

---

## Login Reference

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@cairo.utm` | `password` |
| **Staff** | `user@gmail.com` | `password` |

**Admin** can access:
- Admin dashboard, annual reports (PA-4/5/7/8/12/20), user management, new receiving registration, all asset modules

**Staff** can access:
- Personal dashboard, all asset management modules (except annual reports and user management), KEW.PA directory

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

Or via the web UI at `/import` or `/delivery-orders/batch-import`.

---

## Project Structure

```
cairo-inventory/
├── app/
│   ├── Http/
│   │   ├── Controllers/               # 38 controllers (27 business + 10 auth + 1 base)
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
│   │   │   ├── DeliveryOrderController.php      # Phase 2 — DO management
│   │   │   ├── DisposalSaleController.php       # Asset sales (PA-21→27A)
│   │   │   ├── DisposalSaleItemController.php   # Sale items
│   │   │   ├── DoLineItemController.php         # Phase 2 — DO line items
│   │   │   ├── ImportController.php             # Phase 2 — Web import
│   │   │   ├── ItemsController.php              # Phase 2 — Unified view
│   │   │   ├── KewpaDirectoryController.php     # KEW.PA directory
│   │   │   ├── PdfImportController.php          # Phase 2 — OCR import
│   │   │   ├── ReportController.php             # Annual reports
│   │   │   ├── SaleBidController.php            # Sale bids
│   │   │   ├── ScanController.php               # Phase 2 — Barcode scanner
│   │   │   ├── SupplierController.php           # Phase 2 — Supplier CRUD
│   │   │   ├── VehicleDisposalAssessmentController.php
│   │   │   └── Auth/                            # 10 auth controllers (Breeze)
│   │   └── Requests/
│   ├── Models/                         # 17 Eloquent models
│   │   ├── Asset.php, AssetDisposal.php, AssetInspection.php, ...
│   │   ├── AssetLossReport.php, AssetMaintenance.php, AssetPlacement.php
│   │   ├── AssetTransfer.php, AssetUpgrade.php, CommitteeAppointment.php
│   │   ├── DamageReport.php, DeliveryOrder.php, DisposalSale.php
│   │   ├── DisposalSaleItem.php, DoLineItem.php, FinalLossReport.php
│   │   ├── PurchaseOrder.php, Receiving.php, SaleBid.php
│   │   ├── Scan.php, Supplier.php, User.php
│   │   └── VehicleDisposalAssessment.php
│   └── ...
├── bin/
│   └── cairo-ocr                      # 1,201-line OCR/import CLI (Python)
├── database/
│   ├── migrations/                     # 34 migration files
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── KewpaDataSeeder.php         # Demo data (10+ records/module)
├── resources/
│   ├── js/Pages/                       # 58 React page components
│   │   ├── Admin/
│   │   ├── Assets/                     # KEW.PA-1/2/3/6/9/9A/10/13/16/17/28/30/31/32
│   │   ├── CommitteeAppointments/
│   │   ├── DeliveryOrders/            # Phase 2 — DO pages
│   │   ├── DisposalSales/             # KEW.PA-21→27A
│   │   ├── Disposals/                 # KEW.PA-18/19
│   │   ├── Import/                    # Phase 2 — OCR import UI
│   │   ├── Inspections/               # KEW.PA-11
│   │   ├── Items/                     # Phase 2 — Unified items view
│   │   ├── Maintenances/              # KEW.PA-14
│   │   ├── Profile/
│   │   ├── Reports/                   # KEW.PA-4/5/7/8/12/20
│   │   ├── Scanner/                   # Phase 2 — Barcode/scanner UI
│   │   ├── Suppliers/                 # Phase 2 — Supplier pages
│   │   └── Auth/
│   ├── js/Layouts/
│   │   ├── AuthenticatedLayout.jsx     # Sidebar + header
│   │   └── GuestLayout.jsx
│   ├── js/Components/                  # 15 reusable components
│   └── views/pdfs/                     # 29 KEW.PA PDF Blade templates
├── routes/
│   └── web.php                         # ~130 routes (auth + asset + procurement)
├── requirements.txt                    # Python OCR dependencies
└── public/build/                       # Compiled frontend assets
```

---

## Development Status

| Component | Status |
|-----------|--------|
| CRUD Frontend (16 modules) | ✅ 100% Complete |
| Sidebar Navigation | ✅ 100% Complete |
| PDF Generation (29 templates) | ✅ Configured & Verified |
| Admin Dashboard | ✅ Fully Built |
| Annual Reports (6) | ✅ Fully Built |
| KEW.PA Directory | ✅ Fully Built |
| Seed Data (10+ records/module) | ✅ Complete |
| **Ground Truth Alignment (4 tiers)** | **✅ Complete** |
| **Phase 2 — OCR Engine (1,201 lines)** | **✅ Built & Verified** |
| **Phase 2 — PO/DO Import Pipeline** | **✅ Real data imported (51 PO + 59 DO items)** |
| **Phase 2 — Supplier Management** | **✅ 2 real suppliers** |
| **Phase 2 — Scanner Module** | **✅ Manual S/N entry, camera 🔄** |
| **Phase 2 — Verification Dashboard** | **✅ DO progress tracking** |
| **Phase 2 — Scan→Asset auto-creation** | 🟡 Built but not wired (manual step required) |
| Mobile Responsiveness | ⚠️ Basic (desktop-first priority) |
| Automated Tests | 📋 Not yet started |
| Activity Logging | ✅ `spatie/activitylog` — 131 entries |

### Ground Truth Alignment — Completed ✅

| Tier | What was built | Status |
|------|----------------|--------|
| **Tier 1** | PA-30 (8-section Final Loss Report), PA-27 auction rules (a-k), PA-21→PA-26 sale chain (6 new DB columns, channel-specific templates) | ✅ Complete |
| **Tier 2** | PA-11 (Inventory Inspection), PA-14 (Maintenance Register), PA-31 (Write-off Certificate), PA-32 (Annual Loss Action) | ✅ Complete |
| **Tier 3** | PA-18 (Destruction Certificate), PA-19 (Disposal Certificate) | ✅ Complete |
| **Tier 4** | 14 title corrections across PA-9A→PA-27A to match official PDF names | ✅ Complete |

---

## Database Overview

**26 tables** (17 business + 9 Laravel system):

### Real Data (Phase 2 Procurement)

| Table | Records | Notes |
|-------|---------|-------|
| `suppliers` | **2** | Dotcom Telecom Sdn Bhd + SNS Network (M) Sdn Bhd |
| `purchase_orders` | **1** | PO #PPTK170300122025000272 (Dotcom, 51 items) |
| `purchase_order_items` | **51** | With unit_price, category, brand, model |
| `delivery_orders` | **3** | 1 real DO (59 items) + 2 test imports |
| `do_line_items` | **70** | 59 real (33 with serial numbers) + 11 test |
| `media` | **2** | PT-DOT-COM.pdf (291KB) + DOTCOM-DO.pdf (9.5MB) |
| `scans` | **0** | Scanner module built, awaiting production data |
| `users` | **3** | 2 admin + 1 staff (Ts. Dr. Mohd Ibrahim) |
| `activity_log` | **131** | All import + seeding operations tracked |

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
| `final_loss_reports` | 0 | 8-section investigation schema (PA-30) |

> **Note:** The `assets` table contains demo seed data only. The Phase 2 pipeline has imported real PO/DO data (51 items, 59 DO items), but the Scan→Asset creation step is awaiting production workflow.

---

## Future Considerations

| Item | Notes |
|------|-------|
| **Scan→Asset Auto-Creation** | Wire scanner to auto-create assets after barcode verification |
| **Production Asset Data Entry** | Convert 59 DO items → real PA-2/PA-3 asset records |
| **Docker Deployment** | Containerise Laravel + OCR + PostgreSQL for reproducible environments |
| **Granular Permissions** | Add read-only, editor, admin roles |
| **PDF Fallback** | If Browsershot proves unstable, switch to DomPDF |
| **PA-4/5/8 Blade templates** | Add Blade PDF templates for these React-only forms |
| **PA-15/29 letter-format PDF** | Add PDF output for committee appointment letters |
| **i18n / Bilingual Support** | Current UI is mixed BM/EN; add full language toggle |
| **Barcode/QR Labels** | Print scannable labels from KEW.PA-2 registration |
| **SAGA Integration** | Sync asset data with Malaysia's national SAGA system |
| **E2E Testing** | Automated end-to-end tests for all CRUD workflows |
| **SNS DO OCR** | Improve OCR for SNS Network PDF format (currently ~61% extraction) |

---

## Codebase Statistics

| Metric | Count |
|--------|-------|
| React Pages | 58 |
| Reusable Components | 15 |
| Eloquent Models | 17 |
| Business Controllers | 27 |
| Auth Controllers | 10 |
| Total Controllers | **38** |
| Database Migrations | 34 |
| PDF Blade Templates | 29 |
| Database Tables | 26 |
| Web Routes | **~130** |
| OCR Engine (Python) | 1,201 lines |
| Activity Log Entries | 131 |
| Real PO Items Imported | 51 |
| Real DO Items Imported | 59 + 11 partial |
| Seed Records | 10+ per module |

---

## License

Copyright © 2026 CAIRO Lab, Universiti Teknologi Malaysia.

Built for thesis and academic research purposes.

---

> *"A comprehensive, digital, and user-friendly asset management system for Universiti Teknologi Malaysia."*
