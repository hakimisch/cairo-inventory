# CAIRO Inventory — Sistem Pengurusan Aset UTM

> **Sistem Pengurusan Aset Alih dan Tak Alih** berasaskan web untuk pengurusan kitaran hayat aset Universiti Teknologi Malaysia (UTM), merangkumi 32 borang KEW.PA piawaian Kementerian Kewangan Malaysia.

---

## 📋 Senarai Kandungan

- [Ringkasan](#ringkasan)
- [Tech Stack](#tech-stack)
- [Modul Sistem (16 Modul)](#modul-sistem-16-modul)
- [Peta Borang KEW.PA (32 Borang)](#peta-borang-kewpa-32-borang)
- [Ciri-ciri Utama](#ciri-ciri-utama)
- [Panduan Log Masuk](#panduan-log-masuk)
- [Pemasangan & Persediaan](#pemasangan--persediaan)
- [Struktur Kod](#struktur-kod)
- [Arkitektur](#arkitektur)
- [Status Pembangunan](#status-pembangunan)
- [Pertimbangan Masa Depan](#pertimbangan-masa-depan)

---

## Ringkasan

CAIRO Inventory ialah sistem pengurusan aset komprehensif yang dibina khusus untuk **Pusat Pengurusan Aset UTM**. Sistem ini menguruskan kitaran hayat penuh aset UTM — dari penerimaan, pendaftaran, pergerakan, penyelenggaraan, pemeriksaan, pelupusan, sehinggalah ke jualan dan pelaporan kehilangan.

**Objektif:**
- Mendigitalkan kesemua 32 borang KEW.PA piawaian kerajaan
- Menyediakan platform berpusat untuk pengurusan aset kampus UTM JB dan UTM KL
- Membolehkan penjanaan laporan tahunan dan PDF borang secara automatik
- Memudahkan proses audit aset dengan rekod digital yang lengkap

---

## Tech Stack

| Lapisan | Teknologi |
|---------|-----------|
| **Frontend** | React 19 + Inertia.js + Vite |
| **Backend** | Laravel 11 (PHP 8.4) |
| **Database** | PostgreSQL 18 |
| **PDF Generation** | Spatie Laravel-PDF + Browsershot (Chromium) |
| **Charts** | Chart.js |
| **CSS** | Inline styles + UTM brand palette |
| **Environment** | WSL (dev) / Windows Herd (prod) |

**Kelebihan Inertia.js:** Membolehkan pembangunan aplikasi React single-page tanpa API. Data dihantar terus dari Laravel ke komponen React, menyediakan pengalaman UX yang lancar tanpa memerlukan API endpoints yang berasingan.

---

## Modul Sistem (16 Modul)

| # | Modul | KEW.PA | Penerangan | CRUD |
|---|-------|--------|------------|------|
| 1 | **Penerimaan Aset** | PA-1 / PA-1A | Terima aset baru, daftar pembekal, dokumen PO/DO/Invois | ✅ Lengkap |
| 2 | **Pendaftaran Aset** | PA-2 / PA-3 | Daftar aset dengan 34 medan, gambar, spesifikasi | ✅ Lengkap |
| 3 | **Pergerakan Aset** | PA-6 | Pindah aset antara lokasi dan pemegang | ✅ Lengkap |
| 4 | **Penempatan/Pinjaman** | PA-9A | Pinjam aset kepada kakitangan/pelajar | ✅ Lengkap |
| 5 | **Pemeriksaan Aset** | PA-10 / PA-11 | Rekod pemeriksaan berkala aset | ✅ Lengkap |
| 6 | **Aduan Kerosakan** | PA-9 | Laporan kerosakan dan tindakan pembaikan | ✅ Lengkap |
| 7 | **Penyelenggaraan** | PA-13 / PA-14 | Rekod penyelenggaraan kontrak dan kos | ✅ Lengkap |
| 8 | **Naik Taraf Aset** | Bahagian B (PA-2) | Tambah RAM/SSD/komponen lain pada aset sedia ada | ✅ Lengkap |
| 9 | **Pelupusan Aset** | PA-17 / PA-18 / PA-19 | Cadangan dan keputusan pelupusan aset | ✅ Lengkap |
| 10 | **Pelupusan Kenderaan** | PA-16 | Penilaian kenderaan untuk dilupuskan | ✅ Lengkap |
| 11 | **Jualan Aset (Tender)** | PA-21 → PA-27A | Jualan melalui tawaran, sebutharga, lelongan | ✅ Lengkap |
| 12 | **Kehilangan Aset** | PA-28 → PA-32 | Laporan kehilangan, siasatan, keputusan | ✅ Lengkap |
| 13 | **Jawatankuasa** | PA-15 / PA-29 | Pelantikan jawatankuasa pelupusan/siasatan | ✅ Lengkap |
| 14 | **Dashboard Admin** | — | Statistik, carta, makluman sistem | ✅ Dibina |
| 15 | **Laporan Tahunan** | PA-4/5/7/8/12/20 | 6 laporan tahunan (admin sahaja) | ✅ Dibina |
| 16 | **Direktori KEW.PA** | /kewpa | Carian grid semua 32 borang KEW.PA | ✅ Dibina |

**Kesemua 16 modul — CRUD penuh, tiada gap.**

---

## Peta Borang KEW.PA (32 Borang)

### Logistik & Penerimaan
| Borang | Nama | Status |
|--------|------|--------|
| KEW.PA-1 / PA-1A | Penerimaan Aset (Asset Receiving) | ✅ Sedia |

### Pendaftaran Aset
| Borang | Nama | Status |
|--------|------|--------|
| KEW.PA-2 | Pendaftaran Aset (Asset Registration) | ✅ Sedia |
| KEW.PA-3 | Kad Aset (Asset Card) | ✅ Sedia |

### Pergerakan & Pemeriksaan
| Borang | Nama | Status |
|--------|------|--------|
| KEW.PA-6 | Daftar Pergerakan (Asset Movement) | ✅ Sedia |
| KEW.PA-9 | Aduan Kerosakan (Damage Report) | ✅ Sedia |
| KEW.PA-9A | Pinjaman Aset (Asset Loan) | ✅ Sedia |
| KEW.PA-10 / PA-11 | Pemeriksaan Aset (Asset Inspection) | ✅ Sedia |
| KEW.PA-12 | Perakuan Pemeriksaan Tahunan | ✅ Sedia |

### Penyelenggaraan
| Borang | Nama | Status |
|--------|------|--------|
| KEW.PA-13 / PA-14 | Rekod Penyelenggaraan (Maintenance) | ✅ Sedia |

### Pelupusan & Jualan
| Borang | Nama | Status |
|--------|------|--------|
| KEW.PA-15 / PA-29 | Pelantikan Jawatankuasa | ✅ Sedia |
| KEW.PA-16 | Perakuan Pelupusan Kenderaan | ✅ Sedia |
| KEW.PA-17 / PA-18 / PA-19 | Laporan Pelupusan (Disposal) | ✅ Sedia |
| KEW.PA-20 | Laporan Pelupusan Aset Tahunan | ✅ Sedia |
| KEW.PA-21 | Notis Jualan (Sale Notification) | ✅ Sedia |
| KEW.PA-22 | Iklan Jualan (Sale Advertisement) | ✅ Sedia |
| KEW.PA-23 | Surat Tawaran Jualan (Award Letter) | ✅ Sedia |
| KEW.PA-24 | Sijil Jualan (Sale Certificate) | ✅ Sedia |
| KEW.PA-25 | Pindah Milik Jualan (Asset Transfer) | ✅ Sedia |
| KEW.PA-26 | D.O. Jualan (Delivery Order) | ✅ Sedia |
| KEW.PA-27 | Laporan Jualan (Sale Report) | ✅ Sedia |
| KEW.PA-27A | Ringkasan Jualan (Sale Summary) | ✅ Sedia |

### Kehilangan
| Borang | Nama | Status |
|--------|------|--------|
| KEW.PA-28 → PA-32 | Laporan Kehilangan (Loss Report) | ✅ Sedia |

### Laporan Tahunan (Admin)
| Borang | Nama | Status |
|--------|------|--------|
| KEW.PA-4 | Senarai Harta Tetap | ✅ Sedia |
| KEW.PA-5 | Senarai Inventori | ✅ Sedia |
| KEW.PA-7 | Laporan Kedudukan Aset | ✅ Sedia |
| KEW.PA-8 | Laporan Tahunan | ✅ Sedia |

> 🌐 **Direktori interaktif:** Layari `/kewpa` selepas log masuk untuk carian dan penapis semua 32 borang.

---

## Ciri-ciri Utama

### Untuk Pengguna Biasa (Staf)

- **Dashboard peribadi** — lihat ringkasan aset dan tindakan
- **CRUD sebaris** — semua borang mempunyai borang tambah/sepadam/rekod terus pada halaman senarai (tiada halaman berasingan)
- **Carian & penapis** pada setiap halaman senarai modul
- **Muat turun PDF** untuk setiap borang KEW.PA
- **Paparan borang KEW.PA** lengkap dengan header/footer UTM
- **Navigasi sidebar** lengkap dengan 7 bahagian kitaran hayat aset

### Untuk Admin

- **Semua ciri pengguna biasa** + akses penuh
- **Dashboard admin** dengan:
  - Statistik sistem (jumlah aset, jumlah nilai, aset aktif/baik pulih)
  - Makluman pintar (penerimaan belum selesai, penyelenggaraan perlu, waranti tamat)
  - Carta pai taburan aset mengikut kategori dan jenis
  - Carta perbandingan kampus (UTM JB vs UTM KL)
  - Ranking aset bernilai tinggi
  - Grid akses pantas KEW.PA
- **6 laporan tahunan** dengan muat turun PDF
- **Pengurusan pengguna** — tambah, edit, padam, tukar peranan
- **Data penuh semua modul** — 10 aset contoh + 100+ rekod modul

### Antara Muka

- **Jenama UTM** — warna maroon (#5C001F) dan emas (#F8A617)
- **Susun atur responsif** — sesuai untuk desktop dan tablet
- **Sidebar boleh navigasi** dengan 7 bahagian kitaran hayat
- **Toast notifications** untuk maklum balas tindakan
- **Konfirmasi sebelum hapus** — dialog pengesahan untuk tindakan padam

---

## Panduan Log Masuk

| Peranan | Emel | Kata Laluan |
|---------|------|-------------|
| **Admin** | `admin@cairo.utm` | `password` |
| **Staf** | `user@gmail.com` | `password` |

**Admin** boleh mengakses:
- Dashboard admin
- Laporan tahunan (PA-4/5/7/8/12/20)
- Pengurusan pengguna
- Daftar penerimaan baru
- Semua modul pengurusan aset

**Staf** boleh mengakses:
- Dashboard peribadi
- Semua modul pengurusan aset (kecuali laporan tahunan dan pengurusan pengguna)
- Direktori KEW.PA

---

## Pemasangan & Persediaan

### Prasyarat

| Keperluan | Versi |
|-----------|-------|
| PHP | ≥ 8.1 |
| Composer | ≥ 2.0 |
| Node.js | ≥ 18 |
| PostgreSQL | ≥ 15 |
| Chromium | (untuk PDF) |

### Langkah Pemasangan

```bash
# 1. Clone repositori
git clone <repo-url>
cd cairo-inventory

# 2. Pasang kebergantungan PHP
composer install

# 3. Pasang kebergantungan Node
npm install

# 4. Salin fail persekitaran
cp .env.example .env
# Edit .env — tetapkan DB_CONNECTION=pgsql dan maklumat PostgreSQL

# 5. Jana kunci aplikasi
php artisan key:generate

# 6. Jalankan migrasi dan seeder
php artisan migrate --seed

# 7. Bina aset frontend
npm run build

# 8. Jalankan pelayan pembangunan
php artisan serve
# atau gunakan Herd/Valet
```

### Konfigurasi PDF

Sistem menggunakan **Spatie Laravel-PDF** dengan **Browsershot** (Chromium) untuk penjanaan PDF. Tetapkan laluan Chromium dalam `.env`:

```
LARAVEL_PDF_CHROME_PATH=/path/to/chrome
LARAVEL_PDF_NO_SANDBOX=true
```

---

## Struktur Kod

```
cairo-inventory/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php   # Dashboard admin
│   │   │   │   └── UserController.php             # Pengurusan pengguna
│   │   │   ├── AssetController.php                # Aset + Penerimaan + Penempatan
│   │   │   ├── AssetDisposalController.php         # Pelupusan
│   │   │   ├── AssetInspectionController.php       # Pemeriksaan
│   │   │   ├── AssetLossReportController.php       # Kehilangan
│   │   │   ├── AssetMaintenanceController.php      # Penyelenggaraan
│   │   │   ├── AssetTransferController.php         # Pergerakan
│   │   │   ├── AssetUpgradeController.php          # Naik taraf
│   │   │   ├── CommitteeAppointmentController.php  # Jawatankuasa
│   │   │   ├── DamageReportController.php          # Kerosakan
│   │   │   ├── DisposalSaleController.php          # Jualan aset
│   │   │   ├── DisposalSaleItemController.php      # Item jualan
│   │   │   ├── KewpaDirectoryController.php        # Direktori KEW.PA
│   │   │   ├── ReportController.php                # Laporan tahunan
│   │   │   ├── SaleBidController.php               # Bidaan jualan
│   │   │   └── VehicleDisposalAssessmentController.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── Asset.php
│   │   ├── AssetDisposal.php
│   │   ├── AssetInspection.php
│   │   ├── AssetLossReport.php
│   │   ├── AssetMaintenance.php
│   │   ├── AssetPlacement.php
│   │   ├── AssetTransfer.php
│   │   ├── AssetUpgrade.php
│   │   ├── CommitteeAppointment.php
│   │   ├── DamageReport.php
│   │   ├── DisposalSale.php
│   │   ├── DisposalSaleItem.php
│   │   ├── Receiving.php
│   │   ├── SaleBid.php
│   │   ├── User.php
│   │   └── VehicleDisposalAssessment.php
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── KewpaDataSeeder.php                    # Data contoh 15 modul
├── resources/
│   ├── js/Pages/                                   # 60 komponen React
│   │   ├── Admin/
│   │   ├── Assets/
│   │   ├── CommitteeAppointments/
│   │   ├── DisposalSales/
│   │   ├── Kewpa/                                  # Direktori KEW.PA
│   │   ├── Profile/
│   │   ├── Reports/
│   │   └── ...
│   ├── js/Layouts/
│   │   ├── AuthenticatedLayout.jsx                 # Sidebar + header
│   │   └── GuestLayout.jsx
│   ├── js/Components/                              # 15 komponen guna semula
│   └── views/pdfs/                                 # 22 templat PDF Blade
├── routes/
│   └── web.php                                     # ~246 laluan
└── public/build/                                   # Aset yang telah dibina
```

---

## Arkitektur

```
┌─────────────────────────────────────────────────────┐
│                    Browser (React SPA)               │
│  Inertia.js ─── Komponen React ─── Interaksi UI     │
└──────────────────┬──────────────────────────────────┘
                   │ Data + Routing (Inertia)
                   ▼
┌─────────────────────────────────────────────────────┐
│                   Laravel Backend                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────────────┐  │
│  │ Routes   │  │Controllers│  │ Models (Eloquent)│  │
│  │ web.php  │─▶│ (20 biz)  │─▶│ (15 model)      │  │
│  └──────────┘  └──────────┘  └────────┬─────────┘  │
│                                       │             │
│  ┌────────────────────────────────────┘             │
│  │   PostgreSQL 18 (25 jadual)                      │
│  └──────────────────────────────────────────────────┘
│                                       │
│  ┌────────────────────────────────────┘
│  │   Spatie Laravel-PDF + Browsershot (Chromium)
│  │   → PDF borang KEW.PA (22 templat Blade)
│  └──────────────────────────────────────────────────┘
└─────────────────────────────────────────────────────┘
```

### Aliran Data

1. Pengguna melayari URL → Laravel route → Controller menghantar data ke komponen React melalui `Inertia::render()`
2. React merender halaman lengkap (termasuk sidebar dan header) berdasarkan data
3. Interaksi CRUD → Borang React menghantar permintaan PUT/POST/DELETE melalui Inertia
4. Inertia menguruskan navigasi tanpa muat semula halaman penuh
5. PDF → Controller menggunakan Spatie Laravel-PDF untuk menjana PDF dari templat Blade, kemudian menghantar respons muat turun

---

## Status Pembangunan

| Komponen | Status |
|----------|--------|
| CRUD Frontend (16 modul) | ✅ 100% Lengkap |
| Sidebar Navigasi | ✅ 100% Lengkap |
| PDF Generation | ✅ Dikonfigurasi & Disahkan |
| Dashboard Admin | ✅ Dibina Sepenuhnya |
| Laporan Tahunan (6) | ✅ Dibina Sepenuhnya |
| Direktori KEW.PA | ✅ Dibina Sepenuhnya |
| Data Benih (10+ rekod/modul) | ✅ Lengkap |
| Responsif Mudah Alih | ⚠️ Asas (keutamaan desktop) |
| Ujian Automatik | 📋 Belum dimulakan |
| Pengelogan Aktiviti | 📋 Belum dimulakan |

**Ringkasan:** Kesemua 14 langkah pembangunan telah selesai. Sistem bersedia untuk demonstrasi tesis dan penggunaan percubaan.

---

## Pertimbangan Masa Depan

| Item | Catatan |
|------|---------|
| **Docker deployment** | WSL-only; kontainer untuk persekitaran boleh ulang |
| **Kebenaran pengguna** | Tambah peranan granular (baca sahaja, editor, admin) |
| **Pemantauan PDF** | Jika Browsershot tidak stabil, guna DomPDF sebagai ganti |
| **i18n / dwibahasa** | UI campuran BM/ING; pertimbangkan suis bahasa penuh |
| **Label barcode/QR** | Cetak label KEW.PA-2 dengan kod boleh imbas |
| **Log audit** | Rekod setiap perubahan aset untuk kebolehkesanan |
| **Integrasi SAGA** | Segerakkan data aset dengan sistem SAGA kebangsaan |
| **Ujian E2E** | Ujian hujung-ke-hujung automatik untuk semua aliran CRUD |

---

## Lesen

Hak Cipta © 2026 CAIRO Lab, Universiti Teknologi Malaysia.

Dibina untuk tujuan tesis dan penyelidikan akademik.

---

> *"Sistem Pengurusan Aset UTM yang menyeluruh, digital, dan mesra pengguna."*
