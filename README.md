# CAIRO Inventory — UTM Asset Management System

> **A Comprehensive Web-Based Asset Management Platform** for managing the full lifecycle of movable and immovable assets at Universiti Teknologi Malaysia (UTM), covering all 32 KEW.PA forms mandated by the Malaysian Ministry of Finance.

---

## Executive Summary

| Attribute | Detail |
|-----------|--------|
| **Project** | CAIRO Inventory — Digital Asset Lifecycle Management |
| **Target** | UTM Asset Management Centre (Pusat Pengurusan Aset) |
| **Scope** | 32 KEW.PA government forms × 16 functional modules |
| **Campus Coverage** | UTM Johor Bahru & UTM Kuala Lumpur |
| **Status** | **100% Complete** — All modules built, seeded, and verified |
| **Purpose** | Thesis demonstration & pilot deployment |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | React 18 + Inertia.js + Vite + Chart.js |
| **Backend** | Laravel 12 (PHP 8.4) |
| **Database** | PostgreSQL |
| **PDF Engine** | Spatie Laravel-PDF + Browsershot (Chromium) — 22 Blade templates |
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
│                    Browser — React SPA (50 pages)              │
│    Inertia.js ─── React Components ─── User Interactions       │
└──────────────────────┬────────────────────────────────────────┘
                       │ Data + Routing (Inertia)
                       ▼
┌───────────────────────────────────────────────────────────────┐
│                     Laravel Backend                            │
│  ┌──────────┐  ┌──────────────┐  ┌────────────────────────┐  │
│  │ Routes   │  │ Controllers  │  │ Eloquent Models        │  │
│  │ web.php  │──│ (20 biz +    │──│ (16 models)            │  │
│  │ ~246 rts │  │  10 auth)    │  │                        │  │
│  └──────────┘  └──────────────┘  └───────────┬────────────┘  │
│                                              │                │
│  ┌───────────────────────────────────────────┘                │
│  │  PostgreSQL (25+ tables from 35 migrations)                │
│  └───────────────────────────────────────────────────────────┘
│                              │
│  ┌───────────────────────────┘
│  │  Spatie Laravel-PDF + Browsershot (Chromium)
│  │  → 22 KEW.PA form templates (Blade)
│  └───────────────────────────────────────────────────────────┘
└───────────────────────────────────────────────────────────────┘
```

### Data Flow

1. **Request** → User navigates a URL → Laravel route resolves
2. **Data** → Controller fetches/validates data → passes via `Inertia::render()`
3. **Render** → React renders full page (sidebar, header, content) from received props
4. **CRUD** → Inline forms submit PUT/POST/DELETE through Inertia — no page reload
5. **PDF** → Controller renders Blade template → Spatie/Browsershot generates PDF → download response

---

## 16 Modules — Full CRUD Coverage

| # | Module | KEW.PA Forms | Description | Status |
|---|--------|--------------|-------------|--------|
| 1 | **Asset Receiving** | PA-1 / PA-1A | Receive new assets, register suppliers, PO/DO/Invoice docs | ✅ Complete |
| 2 | **Asset Registration** | PA-2 / PA-3 | Register assets (34 fields), photos, specifications | ✅ Complete |
| 3 | **Asset Movement** | PA-6 | Transfer assets between locations and custodians | ✅ Complete |
| 4 | **Asset Placement/Loan** | PA-9A | Loan assets to staff or students | ✅ Complete |
| 5 | **Asset Inspection** | PA-10 / PA-11 | Periodic asset inspection records | ✅ Complete |
| 6 | **Damage Reports** | PA-9 | Fault reporting and repair actions | ✅ Complete |
| 7 | **Maintenance** | PA-13 / PA-14 | Contract maintenance and cost records | ✅ Complete |
| 8 | **Asset Upgrades** | PA-2 (Section B) | Add RAM/SSD/components to existing assets | ✅ Complete |
| 9 | **Asset Disposal** | PA-17 / PA-18 / PA-19 | Dispose proposal and decision workflow | ✅ Complete |
| 10 | **Vehicle Disposal** | PA-16 | Vehicle valuation for disposal | ✅ Complete |
| 11 | **Asset Sale (Tender)** | PA-21 → PA-27A | Sale via tender, quotations, auction | ✅ Complete |
| 12 | **Asset Loss** | PA-28 → PA-32 | Loss report, investigation, decision | ✅ Complete |
| 13 | **Committees** | PA-15 / PA-29 | Appointment of disposal/investigation committees | ✅ Complete |
| 14 | **Admin Dashboard** | — | Statistics, charts, system alerts | ✅ Complete |
| 15 | **Annual Reports** | PA-4/5/7/8/12/20 | 6 annual reports (admin only) | ✅ Complete |
| 16 | **KEW.PA Directory** | /kewpa | Searchable grid of all 32 KEW.PA forms | ✅ Complete |

> **All 16 modules — full CRUD with zero gaps.**

---

## 32 KEW.PA Form Map

### Logistics & Receiving
| Form | Name | Status |
|------|------|--------|
| KEW.PA-1 / PA-1A | Asset Receiving | ✅ Ready |

### Asset Registration
| Form | Name | Status |
|------|------|--------|
| KEW.PA-2 | Asset Registration | ✅ Ready |
| KEW.PA-3 | Asset Card | ✅ Ready |

### Movement & Inspection
| Form | Name | Status |
|------|------|--------|
| KEW.PA-6 | Asset Movement Register | ✅ Ready |
| KEW.PA-9 | Damage Report | ✅ Ready |
| KEW.PA-9A | Asset Loan | ✅ Ready |
| KEW.PA-10 / PA-11 | Asset Inspection | ✅ Ready |
| KEW.PA-12 | Annual Inspection Certificate | ✅ Ready |

### Maintenance
| Form | Name | Status |
|------|------|--------|
| KEW.PA-13 / PA-14 | Maintenance Record | ✅ Ready |

### Disposal & Sale
| Form | Name | Status |
|------|------|--------|
| KEW.PA-15 / PA-29 | Committee Appointment | ✅ Ready |
| KEW.PA-16 | Vehicle Disposal Certificate | ✅ Ready |
| KEW.PA-17 / PA-18 / PA-19 | Disposal Report | ✅ Ready |
| KEW.PA-20 | Annual Asset Disposal Report | ✅ Ready |
| KEW.PA-21 | Sale Notification | ✅ Ready |
| KEW.PA-22 | Sale Advertisement | ✅ Ready |
| KEW.PA-23 | Award Letter | ✅ Ready |
| KEW.PA-24 | Sale Certificate | ✅ Ready |
| KEW.PA-25 | Asset Ownership Transfer | ✅ Ready |
| KEW.PA-26 | Sale Delivery Order | ✅ Ready |
| KEW.PA-27 | Sale Report | ✅ Ready |
| KEW.PA-27A | Sale Summary | ✅ Ready |

### Loss
| Form | Name | Status |
|------|------|--------|
| KEW.PA-28 → PA-32 | Loss Report (Chain) | ✅ Ready |

### Annual Reports (Admin)
| Form | Name | Status |
|------|------|--------|
| KEW.PA-4 | Fixed Asset Register | ✅ Ready |
| KEW.PA-5 | Inventory List | ✅ Ready |
| KEW.PA-7 | Asset Position Report | ✅ Ready |
| KEW.PA-8 | Annual Report | ✅ Ready |

> 🌐 **Interactive Directory:** Visit `/kewpa` after login to browse, search, and filter all 32 KEW.PA forms.

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

# 7. Build frontend assets
npm run build

# 8. Start development server
php artisan serve
# Or use Herd / Valet for production-like setup
```

### PDF Configuration

The system uses **Spatie Laravel-PDF** with **Browsershot** (Chromium). Set the Chromium binary path in `.env`:

```
LARAVEL_PDF_CHROME_PATH=/path/to/chrome
LARAVEL_PDF_NO_SANDBOX=true
```

---

## Project Structure

```
cairo-inventory/
├── app/
│   ├── Http/
│   │   ├── Controllers/               # 30 controllers (20 business + 10 auth)
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   └── UserController.php
│   │   │   ├── AssetController.php              # Assets + Receiving + Placement
│   │   │   ├── AssetDisposalController.php      # Disposal workflows
│   │   │   ├── AssetInspectionController.php    # Inspections
│   │   │   ├── AssetLossReportController.php    # Loss chain
│   │   │   ├── AssetMaintenanceController.php   # Maintenance records
│   │   │   ├── AssetTransferController.php      # Movement
│   │   │   ├── AssetUpgradeController.php       # Upgrades
│   │   │   ├── CommitteeAppointmentController.php
│   │   │   ├── DamageReportController.php
│   │   │   ├── DisposalSaleController.php       # Asset sales
│   │   │   ├── DisposalSaleItemController.php   # Sale items
│   │   │   ├── KewpaDirectoryController.php     # KEW.PA directory
│   │   │   ├── ReportController.php             # Annual reports
│   │   │   ├── SaleBidController.php            # Sale bids
│   │   │   └── VehicleDisposalAssessmentController.php
│   │   └── Requests/
│   ├── Models/                         # 16 Eloquent models
│   └── ...
├── database/
│   ├── migrations/                     # 35 migration files
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── KewpaDataSeeder.php         # Sample data (10+ records/module)
├── resources/
│   ├── js/Pages/                       # 50 React page components
│   │   ├── Admin/
│   │   ├── Assets/
│   │   ├── CommitteeAppointments/
│   │   ├── DisposalSales/
│   │   ├── Kewpa/
│   │   ├── Profile/
│   │   ├── Reports/
│   │   └── Auth/
│   ├── js/Layouts/
│   │   ├── AuthenticatedLayout.jsx     # Sidebar + header
│   │   └── GuestLayout.jsx
│   ├── js/Components/                  # 15 reusable components
│   └── views/pdfs/                     # 22 KEW.PA PDF Blade templates
├── routes/
│   └── web.php                         # ~246 routes
└── public/build/                       # Compiled frontend assets
```

---

## Development Status

| Component | Status |
|-----------|--------|
| CRUD Frontend (16 modules) | ✅ 100% Complete |
| Sidebar Navigation | ✅ 100% Complete |
| PDF Generation (22 templates) | ✅ Configured & Verified |
| Admin Dashboard | ✅ Fully Built |
| Annual Reports (6) | ✅ Fully Built |
| KEW.PA Directory | ✅ Fully Built |
| Seed Data (10+ records/module) | ✅ Complete |
| Mobile Responsiveness | ⚠️ Basic (desktop-first priority) |
| Automated Tests | 📋 Not yet started |
| Activity Logging | 📋 Not yet started |

**Summary:** All 16 development milestones completed. System is ready for thesis demonstration and pilot deployment.

---

## Future Considerations

| Item | Notes |
|------|-------|
| **Docker Deployment** | Currently WSL-only; containerise for reproducible environments |
| **Granular Permissions** | Add read-only, editor, admin roles |
| **PDF Fallback** | If Browsershot proves unstable, switch to DomPDF |
| **i18n / Bilingual Support** | Current UI is mixed BM/EN; add full language toggle |
| **Barcode/QR Labels** | Print scannable labels from KEW.PA-2 registration |
| **Audit Log** | Record every asset change for traceability |
| **SAGA Integration** | Sync asset data with Malaysia's national SAGA system |
| **E2E Testing** | Automated end-to-end tests for all CRUD workflows |

---

## Codebase Statistics

| Metric | Count |
|--------|-------|
| React Pages | 50 |
| Reusable Components | 15 |
| Eloquent Models | 16 |
| Business Controllers | 20 |
| Total Controllers | 30 |
| Database Migrations | 35 |
| PDF Blade Templates | 22 |
| Application Routes | ~246 |
| Seed Records | 10+ per module |

---

## License

Copyright © 2026 CAIRO Lab, Universiti Teknologi Malaysia.

Built for thesis and academic research purposes.

---

> *"A comprehensive, digital, and user-friendly asset management system for Universiti Teknologi Malaysia."*
