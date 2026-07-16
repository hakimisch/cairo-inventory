<?php

namespace Database\Seeders;

use App\Models\MasterItem;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class MasterItemSeeder extends Seeder
{
    /**
     * Seed the master_items table with all 156 items from the master Excel.
     * Run with: php artisan db:seed --class=MasterItemSeeder
     */
    public function run(): void
    {
        // ── Create suppliers ────────────────────────────────────────────────
        $suppliers = [];
        foreach ([
            'DOTCOM Telecommunications Sdn. Bhd.',
            'SNS Network (M) Sdn. Bhd.',
        ] as $name) {
            $supplier = Supplier::firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
            $suppliers[$name] = $supplier->id;
        }

        // ── Map supplier name → ID ──────────────────────────────────────────
        $sid = fn($name) => $suppliers[$name] ?? null;

        // ── Insert all items ────────────────────────────────────────────────
        $items = [
            [
                'item_no'         => '2.1.1.1',
                'category'        => 'Office',
                'name'            => 'Office Desktop',
                'specification'   => 'i.
The specification should be at least:
ii.
Form Factor & Dimensions: 8.2 L small-form-factor chassis,
92.5 × 291.7 × 339.5 mm (W × D × H), ≈ 4.63 kg
iii.
Processor: 13th Gen Intel Core i5-13420H (8 cores: 4 P-cores +
4 E-cores, 12 threads; P-core up to 4.6 GHz, E-core up to 3.4
GHz; 12 MB cache)
iv.
Memory: Dual-channel DDR5-5200 via two SO-DIMM slots, up
to 64 GB
14 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
v.
Storage & Expansion: Up to two M.2 PCIe 4.0 ×4 SSDs
(2242/2280, up to 1 TB each); one low-profile PCIe 4.0 ×16
slot; one PCIe 3.0 ×1 slot; third M.2 slot for WLAN
vi.
Graphics: Integrated Intel UHD Graphics supporting 4K @ 60
Hz (HDMI 2.1) or 1920 × 1200 @ 60 Hz (VGA); optional 4 GB
GDDR6 discrete GPU
vii. Power Supply: Fixed 180 W (85 % efficiency) PSU with remote-
boot ("Smart Power On") support
viii. Connectivity: Gigabit Ethernet; Intel Wi-Fi 6 (802.11ax 2×2) +
Bluetooth 5.2
ix.
Front I/O: 1× USB-C 3.2 Gen 1 (5 Gbps, 15 W PD), 2× USB-A
3.2 Gen 1 (5 Gbps), headphone/mic combo jack, mic-only jack
x.
Rear I/O: 2× USB-A 2.0, 2× USB-A 3.2 Gen 1 (one with Smart
Power On), HDMI 2.1, VGA, RJ-45 LAN, line-out; optional
serial/parallel ports
xi.
Security & Manageability: Firmware TPM 2.0; BIOS-level
admin/hard-disk/power-on passwords; Secure Boot; Smart USB
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan',
                'brand'           => 'DELL QBS1250',
                'serial_numbers'  => 'T0NKCH4, 1JNKCH4, 40NKCH4, 41NKCH4, 50NKCH4, T2MKCH4, BZMKCH4, SZMKCH4, BZMKCH4, CZMKCH4, DHNKCH4, F0NKCH4, GHNKCH4, HHNKCH4',
                'country_of_origin' => null,
                'unit_price'      => 5926.00,
                'total_price'     => 82964.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 14,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.1.2',
                'category'        => 'Office',
                'name'            => 'Standard Monitor',
                'specification'   => 'The specification should be at least:
i.
E24-30, 23.8" Full HD (1920 × 1080) IPS panel with WLED
backlight and 3-side near-edgeless bezels (16:9 aspect, 93
PPI)
ii.
Brightness & Color: 250 cd/m² typical brightness; 1300:1
contrast ratio; 99 % sRGB coverage; 8-bit color depth;
178°/178° viewing angles
iii. Refresh Rate & Response: Up to 100 Hz refresh rate; 4 ms
(Extreme Mode) / 6 ms (Normal Mode) response time
iv. Connectivity: HDMI 1.4 ×1, DisplayPort 1.2 ×1, VGA ×1; 3.5
mm audio-out jack
v.
Audio: Dual integrated 1 W stereo speakers
vi. Ergonomics: Tilt –5° to 22°; swivel ±360°; lift up to 155 mm;
pivot ±90°; VESA 100 × 100 mm mount compatible
14 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
vii. Dimensions & Weight: 539.4 × 178.6 × 497.2 mm (W × D × H
with stand); 4.7 kg
viii. Power & Compliance: Approx. 20 W typical power draw; TÜV
Rheinland Eye Comfort, Flicker-Free, Low Blue Light; TCO
Certified; RoHS compliant
ix. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'DELL E2425HSM',
                'serial_numbers'  => 'BLZLRC4, BMILRC4, BQDMRC4, BQGLRC4, BOHKRC4, BOHMRC4, BOLLRC4, BQNKRC4, C2DKRC4, FACMRC4, FADKRC4, FAFMRC4, FAHMRC4, FAJLRC4',
                'country_of_origin' => null,
                'unit_price'      => 513.00,
                'total_price'     => 7182.00,
                'sodo_bayaran'    => 'B36201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 14,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.1.3',
                'category'        => 'Office',
                'name'            => 'High Specification Desktop with Powerful Single Core CPU',
                'specification'   => 'The specification should be at least:
i. PC
a. Processor: i9-14900K (24 Cores, 32 Threads, up to 5.8GHz)
b. OS: Windows 11, Professional
c. Memory: Trident Z5 RGB 128 GB (4×32 GB) DDR5-6400
CL32
d. Graphic: Astral GeForce RTX 5090, 32GB, GDDR7, OC
e. Storage: 2 x 990 PRO 2 TB NVMe M.2 SSD
f. Motherboard: Maximus Z790 Dark Hero
g. Cooling System: Ryujin III 360 ARGB with high quality
thermal paste
h. Power Supply: HX1200 Platinum with ultra-low ripple/noise
i.
Connectivity: Wi-Fi 7, Bluetooth v5.3, HDMI Port
j.
Case: Strix Helios GX601, featuring clean lines, good cable
management, and very good airflow
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
k. Warranty: at least 3 years
l.
Installation and cable managements included
ii. Peripherals
a. Mouse: MX Master 3S, Ergonomic, fast scroll, USB-C
charging, wireless, comes with charging cable or docking
station with extra large size mouse pad that covers both
keyboard and the mouse
b. Keyboard: Strix Flare II Animate gaming mechanical
keyboard with AniMe Matrix™ LED display, 8000 Hz polling
rate, NX mechanical switches or MX switches, swappable
switches, metal media controls and a wrist rest with light
diffuser
c. Headphones: WH-1000XM5, over-ear, closed-back wireless,
30 mm dynamic, carbon-fiber diaphragm and battery up to 30
hr (ANC on), 40 hr (ANC off)
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
k',
                'brand'           => 'DELL QBS1250',
                'serial_numbers'  => 'T0NKCH4, 1JNKCH4, 40NKCH4, 41NKCH4, 50NKCH4, T2MKCH4, BZMKCH4, SZMKCH4, BZMKCH4, CZMKCH4, DHNKCH4, F0NKCH4, GHNKCH4, HHNKCH4',
                'country_of_origin' => null,
                'unit_price'      => 29482.00,
                'total_price'     => 29482.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.1.4',
                'category'        => 'Office',
                'name'            => 'High Specification Desktop with Powerful Multi-Core CPU',
                'specification'   => 'The specification should be at least :
i. PC
a. Processor: Ryzen 9 9950X3D (16 Cores, 32 Threads, up to
5.7GHz)
b. OS: Windows 11, Professional
c. Memory: Trident Z5 Neo RGB 128 GB (2×64 GB or 4×32 GB)
DDR5-6400 CL30
d. Graphic: Astral GeForce RTX 5090, 32GB, GDDR7, OC
e. Storage: 2 x 990 PRO 2 TB NVMe M.2 SSD
f.
Motherboard: Strix X870E-E Gaming Wi-Fi
g. Cooling System: Ryujin III 360 ARGB with high quality thermal
paste
h. Power Supply: HX1200 Platinum with ultra-low ripple/noise
i.
Connectivity: Wi-Fi 7, Bluetooth v5.3, HDMI Port
j.
Case: Strix Helios GX601, featuring clean lines, good cable
management, and very good airflow
k. Installation and cabling included
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
ii. Smartherals
a. Mouse: MX Master 3S, Ergonomic, fast scroll, USB-C
charging, wireless, comes with charging cable or docking
station with extra large size mouse pad that covers both
keyboard and the mouse
b. Keyboard: Strix Flare II Animate gaming mechanical
keyboard with AniMe Matrix™ LED display, 8000 Hz polling
rate, NX mechanical switches or Cherry MX switches,
swappable switches, metal media controls, and a wrist rest
with light diffuser
c. Headphones: WH-1000XM5, over-ear, closed-back wireless,
30 mm dynamic, carbon-fiber diaphragm and battery up to 30
hr (ANC on), 40 hr (ANC off)
d. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
___',
                'brand'           => 'DELL QBS1250',
                'serial_numbers'  => 'T0NKCH4, 1JNKCH4, 40NKCH4, 41NKCH4, 50NKCH4, T2MKCH4, BZMKCH4, SZMKCH4, BZMKCH4, CZMKCH4, DHNKCH4, F0NKCH4, GHNKCH4, HHNKCH4',
                'country_of_origin' => null,
                'unit_price'      => 42441.00,
                'total_price'     => 42441.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.1.5',
                'category'        => 'Office',
                'name'            => 'High Specification Monitor',
                'specification'   => 'i.
32-inch IPS, Non-Glare
ii.
Resolution & Density: 3840 × 2160 (4K UHD), 138 PPI
iii.
Backlight & HDR: LED with VESA Display HDR 600 (HDR10)
iv.
Brightness: 400 cd/m² (typical) · 600 cd/m² (peak HDR)
v.
Color Gamut & Accuracy: 100 % sRGB · 100 % Adobe RGB ·
98 % DCI-P3 · 84 % Rec.2020 · ΔE less than 2 (factory-
calibrated)
vi.
Response & Refresh: less than 5 ms GTG · more than 100 Hz ·
Adaptive-Sync support
vii. Connectivity: 1 × DisplayPort 1.4 · 2 × HDMI 2.0
viii. Ergonomics: Tilt +23 / −5 ° · Swivel ±60 ° · Pivot ±90 °
ix.
Height 0 – 130 mm · VESA 100 × 100 mm
x.
Warranty: at least 3 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 4553.00,
                'total_price'     => 9106.00,
                'sodo_bayaran'    => 'B36201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.1.6',
                'category'        => 'Office',
                'name'            => 'External Storage',
                'specification'   => 'i. External SSD NVME M.2 enclosure
a. USB 3.2 Gen 2 Type-C (10 Gbps)
b. Design: Tool-free aluminium housing with built-in thermal pad
for heat dissipation
c. Warranty: at least 3 years
ii. 1 TB NVMe M.2 SSDs
a. 980 EVO 1 TB
b. Form Factor: M.2 2280 · PCIe 4.0 ×4, NVMe 1.4
c. Sequential Read: up to 5 000 MB/s · Write: up to 4 200 MB/s
d. Random Read: up to 680 000 IOPS · Write: up to 800 000
IOPS
iii.  Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'UGREEN USB-C 3.1 Gen2 + Samsung 990 EVO PLUS 1TB',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 676.00,
                'total_price'     => 676.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.1.7',
                'category'        => 'Office',
                'name'            => 'Middle Specification Laptop',
                'specification'   => 'i.
P16s Gen 3 (16″) mobile business model laptop with Core Ultra
5 125H (14 cores: 4 P-cores up to 4.5 GHz + 8 E-cores up to
3.6 GHz + 2 low-power E-cores; 18 MB cache)
ii.
OS: Windows 11 Pro
iii. Memory: 16 GB DDR5-5600 MT/s (2×8 GB SO-DIMM, dual-
channel; expandable to 96 GB)
iv. Storage: 1 TB PCIe 4.0 ×4 M.2 NVMe SSD (Opal 2.0)
v.
Display: 16″ WUXGA (1920×1200) IPS anti-glare, 300 nits,
16:10 aspect, 60 Hz
vi. Connectivity: Intel Wi-Fi 6E AX211 (2×2 802.11ax) + Bluetooth
5.3; Gigabit Ethernet (RJ-45)
vii. Ports: HDMI 2.1 (4K@60 Hz), 2× Thunderbolt 4 USB-C (40
Gbps, USB PD 3.0, DisplayPort 2.1), 2× USB-A 3.2 Gen 1 (5
Gbps; one Always On), RJ-45 Ethernet
4 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
viii. Peripherals: Mouse Ergonomic, fast scroll, USB-C charging,
wireless
ix. Accessories: Bag, 512 GB External SSD with military-grade
enclosure, 128GB Pendrive
x.
Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
2.1.2
NETWORK FOR OFFICE EQUIPMENTS',
                'brand'           => 'Lenovo ThinkPad P16s',
                'serial_numbers'  => 'PF5ZR5BL, PF5ZR0M6, PF5ZSGH0, PF5ZSGHB',
                'country_of_origin' => null,
                'unit_price'      => 12330.00,
                'total_price'     => 49320.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 4,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.2.1',
                'category'        => 'Office',
                'name'            => 'Access Points',
                'specification'   => 'i.
RG-AP9220, Wi-Fi 7 Dual-Radio 3.570 Gbps Indoor Access
Point
ii.
Dual-Radio Configuration:
a. 2 GHz: 2x2 MIMO, two spatial streams
b. 5 GHz: 2x2 MIMO, two spatial streams
iii.
Combined Peak Data Rate: Up to 3.570 Gbps
iv.
Ethernet Ports:
a. 1 × 2.5GBASE-T
b. 1 × 2.5GE SFP
v.
Power Supply: Supports both DC and PoE (IEEE 802.3af/at)
vi. Antenna: Built-in smart antennas
vii. Management Modes: Fat, Fit, and Cloud modes; compatible
with Ruijie SNC
viii. Security protocols: WPA3, WPA2, WPA, 802.1X, and PPSK
11 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
ix. Operating temperature: 0°C to 50°C (32°F to 122°F)
x.
Operating Humidity: 5% RH to 95% RH (non-condensing)
xi. MTBF: 200,000 hours (approximately 22 years at 25°C)
xii. Warranty: at least 3 years
xiii. Installation and cabling included
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'RUIJIE RG-AP9220',
                'serial_numbers'  => 'G1UQBCK002102, G1UQBCK008009, G1UQBCK001726, G1UQBCK004466, G1UQBCK001751, G1UQBCK002406, G1UQBCK000244, G1UQBCK002431, G1UQBCK001920, G1UQBCK001747, G1UQBCK00061A',
                'country_of_origin' => null,
                'unit_price'      => 1631.00,
                'total_price'     => 17941.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 11,
                'status'          => 'diterima',
                'received_date'   => '2026-04-14',
                'do_no'           => 'UTMCARD/2026/004',
            ],
            [
                'item_no'         => '2.1.2.2',
                'category'        => 'Office',
                'name'            => 'Access Switches',
                'specification'   => 'i.
Network switch:
a.
Model: Catalyst C9200 series (48 Ports)
b.
Configuration: Ports: 4 x 1G ethernet ports
c.
Uplink: 2 x 1G SFP Uplink Ports (optional module-based)
d.
PoE: Enabled ports: 4
e.
Standard: IEEE 802.3af/at (PoE/PoE+)
f.
Power budget: Up to 240W (depending on power supply
used)
g.
Performance:
h.
Switching capacity: Up to 56 Gbps
i.
Forwarding rate: Up to 83.33 Mpps
j.
Warranty: at least 3 years
k.
Installation and cabling included
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
ii. Software with Cisco IOS XE with the following features:
a. Layer 2 and Layer 3 switching
b. Advanced security (802.1x, MACsec)
c. Full IPv4/IPv6 support
d. Energy Efficient Ethernet (EEE)
e. Web UI and CLI management
f. Installation and setup included
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
iii.
Redundancy:
a. Power supply: 1 x Modular Power Supply (expandable)
b. Fans: Redundant field-replaceable
c. Mounting: Rack mountable (1U)
d. Dimensions: Approx. 44.5 cm x 35 cm x 4.45 cm
e. Warranty: at least 3 years
f. Installation and cabling included
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAI',
                'brand'           => 'CISCO C9200-48P-E',
                'serial_numbers'  => 'FVH30031PAW, FVH30031PNH',
                'country_of_origin' => null,
                'unit_price'      => 32249.00,
                'total_price'     => 64498.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'diterima',
                'received_date'   => '2026-04-14',
                'do_no'           => 'UTMCARD/2026/004',
            ],
            [
                'item_no'         => '2.1.2.3',
                'category'        => 'Office',
                'name'            => 'Cable',
                'specification'   => 'i.
The specification should at least be:
a. Fibre from riser to server room to connect to UTM network
b. Fibre from riser level 1 to riser level 2
c. Fibre from lab to server room
d. Electrical cabling (new Distribution panel (DB) in server
room
e. UTP patch cord 50 feet
f. UTP CAT 6A: From server room to smart lab (come with
faceplate) - 5 points
g. Warranty: at least 3 years
h. Include installation and wiring
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => '10 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 15052.00,
                'total_price'     => 15052.00,
                'sodo_bayaran'    => 'B35399',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.2.4',
                'category'        => 'Office',
                'name'            => 'Transceiver',
                'specification'   => 'i.
10GbE transceivers to connect between server room & UTM
network
a.
10GBASE-SR SFP+ 850nm 300m
b.
Duplex LC/UPC Module
c.
Warranty: at least 3 years
d.
Installation and cabling included
ii.
10GbE transceivers to connect between Smart lab & server
room
a.
10GBASE-SR SFP+ 850nm 300m
b.
Duplex LC/UPC Module
c.
Installation and cabling included
4 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'CISCO SFP-10G-SR',
                'serial_numbers'  => 'FNS294509GQ, FNS294509H8, FNS294509HA, FNS284509HG',
                'country_of_origin' => null,
                'unit_price'      => 2175.00,
                'total_price'     => 8700.00,
                'sodo_bayaran'    => 'B27399',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 4,
                'status'          => 'diterima',
                'received_date'   => '2026-04-14',
                'do_no'           => 'UTMCARD/2026/004',
            ],
            [
                'item_no'         => '2.1.2.5',
                'category'        => 'Office',
                'name'            => 'Wi-Fi Router',
                'specification'   => 'i. Wireless Standard: Rapture GT-BE98 Pro, Quad-band WiFi 7
(802.11be): 2.4 GHz, 5 GHz, and two independent 6 GHz bands;
backwards-compatible with IEEE 802.11a/b/g/n/ac/ax
ii. Maximum Aggregate Throughput:
a. 2.4 GHz: up to 1 376 Mb/s
b. 5 GHz: up to 5 764 Mb/s
c. 6 GHz-1: up to 11 529 Mb/s
d. 6 GHz-2: up to 11 529 Mb/s
e. Total: Approx. 30 198 Mb/s
iii. Antenna Configuration: 8× external high-gain antennas (4×4
MIMO per band)
iv. CPU & Memory: 2.6 GHz quad-core processor; 256 MB flash + 2
GB DDR4 RAM
v. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 811.00,
                'total_price'     => 811.00,
                'sodo_bayaran'    => 'B36201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.2.6',
                'category'        => 'Office',
                'name'            => 'Mesh Wi-Fi',
                'specification'   => 'i.
Wi-Fi Standard: Wi-Fi 7 (802.11be)
ii.
Wi-Fi Bands: Tri-band (2.4GHz, 5GHz, 6GHz)
iii. Maximum Speeds: Up to 10 Gbps total bandwidth
iv. Mesh System: Yes
v.
Coverage Area: Up to 7,100 sq. ft. (3-pack)
vi. Ethernet Ports: 2 × 2.5 Gbps LAN/WAN ports per unit
vii. Antennas: Internal high-gain antennas
viii. Security Features: WPA3, VPN support
ix. Include proper wiring & installation
x.
Warranty: at least 3 years
xi. Installation and cabling included
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 2088.00,
                'total_price'     => 0.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.2.7',
                'category'        => 'Office',
                'name'            => 'Wi-Fi Router with SIM Card Slot',
                'specification'   => 'i.
Network Support: 5G NR (Sub-6GHz) / 4G LTE
ii. Wi-Fi Standard: Wi-Fi 6 (802.11ax)
iii. Wi-Fi Bands: Dual band (2.4GHz & 5GHz)
iv. Maximum Wi-Fi Speed: Up to 3.3 Gbps
v. Ethernet Ports: 2 × Gigabit LAN/WAN ports
vi. SIM Card Slot: 1 × Nano SIM slot
vii. Antenna: Internal high-gain antennas
viii. Maximum Connected Devices: Up to 32 devices
ix. Security Features: WPA3, VPN pass-through, MAC filtering,
Firewall
x. Management: Web-based UI, Mobile App
xi. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
2.1.3
SECURITY ACCESS FOR OFFICE',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 811.00,
                'total_price'     => 811.00,
                'sodo_bayaran'    => 'B36201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.3.1',
                'category'        => 'Office',
                'name'            => 'CCTV',
                'specification'   => 'i.
Minimum 1080p (2MP), ideally 2K (4MP) or 4K (8MP)
ii.
Must support PoE (Power over Ethernet), standard IEEE
802.3af/at
iii.
Built-in microSD card slot, supports 128GB or more
iv.
Infrared (IR) LEDs, min. 10 meters range, ideally full-color night
vision
v.
Bullet or Dome type, vandal-proof casing if public areas
vi.
Motion detection, person/vehicle detection, alert notifications
vii. Accessible via mobile app and web browser
viii. Use High Endurance SD Cards, e.g., SanDisk High Endurance
or Samsung PRO Endurance with 256GB storage
ix.
Warranty: at least 3 years
x.
Installation and cabling included
15 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'Dahua IPC-HDBW2449E-S-IL; item Healthcare',
                'serial_numbers'  => 'BH0B7E1PAGCB3F5, BH0B7E1PAG1C64E',
                'country_of_origin' => null,
                'unit_price'      => 1591.00,
                'total_price'     => 23865.00,
                'sodo_bayaran'    => 'B36299',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 15,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.1.3.2',
                'category'        => 'Office',
                'name'            => 'POE Switch',
                'specification'   => 'i. 16 PoE ports
a. Warranty: at least 3 years
b. Installation and cabling included
ii. 4 to 8 PoE ports
a. Warranty: at least 3 years
b. Installation and cabling include
2 units',
                'brand'           => 'HIKVISION DS-3E1318P; 2 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3163.00,
                'total_price'     => 6326.00,
                'sodo_bayaran'    => 'B36201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.3.3',
                'category'        => 'Office',
                'name'            => 'Smart Doorbell with Camera, Audio and Tablet',
                'specification'   => 'i. Smart Doorbell Camera and Audio:
a. Resolution: At least 1080p Full HD (or higher like 2K or 4MP)
b. Wide Angle View: Minimum 140° field of view (to cover full
doorway)
c. Night Vision: Infrared (IR) with no red glow, range at least 5–
10 meters
d. HDR support: Handles backlight/sunlight exposure well
e. Two-Way Audio: Built-in mic + speaker
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
f. Noise Cancellation: Reduces background noise during
conversation
g. Local Storage: Via microSD card (e.g. 64GB–128GB) or
built-in eMMC
h. Wi-Fi: 2.4 GHz (dual band with 5 GHz is a bonus)
i.
Mobile App: Compatible with Android & iOS
j.
Tablet Access: App must support large-screen control &
alerts
k. Remote Unlock Integration: Can be linked to smart door
lock/app
l.
Hardwired: Connects to existing wiring (for non-stop power)
m. Power-over-Ethernet (PoE): Optional for stable connection &
power
n. AES Encryption: Protects video & data
o. Tamper Alerts: Sends alert if removed or disconnected
p. Secure Login: App supports 2FA or biometric login
q. Face recognition (advanced models)
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
r. IP65 or higher waterproof rating
s. Anti-theft alarm or lock screw
t. Warranty: at least 3 years
u. Installation and cabling included
ii. Tablet with a',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 6487.00,
                'total_price'     => 6487.00,
                'sodo_bayaran'    => 'B35299',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.3.4',
                'category'        => 'Office',
                'name'            => 'Multiple Door Access Control System',
                'specification'   => 'The specification should at least be:
i.
Multi-door controllers serve as central hubs, monitoring and
programming hardwired or wirelessly networked access point
a.
Access Method: Keypads, Card Readers and Biometric
b.
Access Point: 8
c.
Warranty: at least 3 years
d.
Installation and cabling must be included
ii. Electromagnetic Locks (compatible with above controllers): 8
pieces
a.
Holding Force & Power: 600 lb (≈280 kg) fail-safe mag-
lock.
b.
Operates on 12 V DC at approx. 600 mA; built-in back-EMF
suppression diode for controller protection
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
c.
Mounting & Features: Clamp-on glass-door bracket kit for
8–12 mm tempered glass; includes LED status indicator for
lock/unlock feedback
d.
Warranty: at least 3 years
e.
Installation and cabling must be included
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
2.1.4
MULTIMEDIA EQUIPMENTS FOR OFFICE',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 37136.00,
                'total_price'     => 37136.00,
                'sodo_bayaran'    => 'B36299',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.4.1',
                'category'        => 'Office',
                'name'            => 'Portable PA System',
                'specification'   => 'i.
Portable PA System that can offer clear audio suitable for
classroom settings. It includes a built-in 4-channel mixer,
Bluetooth streaming, and comes with a rechargeable battery
providing up to 12 hours of operation and wireless mics
ii.
Accessories: Wireless Microphone with belt pack transmitter x 2
and batteries
iii. Warranty: at least 3 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'SHURE SLXD14/153T; 2 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 8467.00,
                'total_price'     => 16934.00,
                'sodo_bayaran'    => 'B35403',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.4.10',
                'category'        => 'Office',
                'name'            => 'Smart TV 65 inch for Meeting Room',
                'specification'   => 'i.
65" Interactive Display WMB Flip Pro LH65WMBWBGCXXS
ii.
Display: 65″ UHD 4K (3840 × 2160) LED panel
iii. Touch & Interaction: InGlass™ touch technology, 26 ms latency, 2
048 pressure levels, up to 20 simultaneous touch points
iv. Connectivity: 2× HDMI In, 1× DisplayPort In, 3× USB-A, 1× USB-C
(DP Alt-Mode + 65 W PD), RJ-45 LAN, NFC reader, Wi-Fi &
Bluetooth, wireless screen sharing via SmartView+ (up to 50
devices, 4-view) & AirPlay 2
v.
Software & Functionality: Tizen-based digital whiteboard with
pen/brush modes and eraser gestures, built-in web browser, Office
365 and other productivity apps
vi. Accessories: Dual-sided passive styluses, VESA 400 × 400 mm
wall-mount kit, optional mobile trolley stand, quick-start guide &
installation hardware
vii. Warranty: at least 3 years
viii. Installation and cabling must be included
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
2.1.5
SUPPORT EQUIPMENTS FOR OFFICE',
                'brand'           => 'SAMSUNG LH65WMBWBGC + NORTH BAYOU trolley',
                'serial_numbers'  => 'OLY7HNJL200005, OLY7HNJXB00006',
                'country_of_origin' => null,
                'unit_price'      => 14528.00,
                'total_price'     => 29056.00,
                'sodo_bayaran'    => 'B35403',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'diterima',
                'received_date'   => '2026-04-14',
                'do_no'           => 'UTMCARD/2026/004',
            ],
            [
                'item_no'         => '2.1.4.2',
                'category'        => 'Office',
                'name'            => 'DSLR Camera',
                'specification'   => 'i. Camera featuring a 26.0-megapixel APS-C Exmor R CMOS
sensor, BIONZ XR image processing engine, 4K video
recording at up to 60p, 759-point phase-detection autofocus,
ISO sensitivity range of 100 to 32,000, 3.0-inch vari-angle
LCD touchscreen, and compatibility with E-mount lenses
ii. Accessories: SD Card, Extra Battery, Charger, Bag
iii. Warranty: at least 3 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 7034.00,
                'total_price'     => 14068.00,
                'sodo_bayaran'    => 'B35501',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.4.3',
                'category'        => 'Office',
                'name'            => 'Handheld Video Camera',
                'specification'   => 'i.
Camera:
a.
Sensor: 1-inch CMOS
b.
Pixels:  9.4 MP (video), 12 MP (photo)
c.
Lens: f/2.0, 20mm equivalent focal length
d.
ISO Range: Photo: 100-6400, Video: 100-6400 (up to 12800 in
certain modes)
ii.
Video Recording:
a.
Max Video Resolution: 4K (3840 × 2160) at 120 fps
b.
Bitrate: Up to 130 Mbps
c.
Formats: MP4 (H.264 / H.265)
iii.
Stabilization:
a.
Gimbal: 3-axis mechanical stabilization (Tilt, Roll, Pan)
b.
Max Control Speed: 120°/s
iv.
Display & Controls:
a.
Screen: 2-inch rotatable OLED touchscreen
b.
Resolution: 314 × 556 pixels
v.
Warranty: at least 3 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'DJI OSMO POCKET 3; 2 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3257.00,
                'total_price'     => 514.00,
                'sodo_bayaran'    => 'B36501',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.4.4',
                'category'        => 'Office',
                'name'            => '1 Meter HDMI Cables',
                'specification'   => 'i.
Ultra High-Speed HDMI 2.1 (48 Gbps)
ii.
Supports 8K@60 Hz & 4K@120 Hz, HDR, VRR
iii. Gold-plated Type-A connectors
iv. Braided/TPE jacket with strain relief
v.
UL/CE/RoHS certified
10 units',
                'brand'           => '10 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 18.00,
                'total_price'     => 180.00,
                'sodo_bayaran'    => 'B27107',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.4.5',
                'category'        => 'Office',
                'name'            => '2 Meter HDMI Cables',
                'specification'   => 'i.
Ultra High-Speed HDMI 2.1 (48 Gbps)
ii.
Supports 8K@60 Hz & 4K@120 Hz, HDR, VRR
iii.
Gold-plated Type-A connectors
iv.
Braided/TPE jacket with strain relief
v.
UL/CE/RoHS certified
10 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => '10 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 21.00,
                'total_price'     => 210.00,
                'sodo_bayaran'    => 'B27107',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.4.6',
                'category'        => 'Office',
                'name'            => '5 Meter HDMI Cables',
                'specification'   => 'i.
Ultra High-Speed HDMI 2.1 (48 Gbps)
ii. Supports 8K@60 Hz & 4K@120 Hz, HDR, VRR
iii. Gold-plated Type-A connectors
iv. Braided/TPE jacket with strain relief
v. UL/CE/RoHS certified
10 units',
                'brand'           => '10 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 37.00,
                'total_price'     => 370.00,
                'sodo_bayaran'    => 'B27107',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.4.7',
                'category'        => 'Office',
                'name'            => 'HDMI to VGA converter',
                'specification'   => 'i.
Active HDMI→VGA scaler
ii.
Up to 1080p@60 Hz output
iii. 3.5 mm audio out jack
iv. USB-powered (5 V DC)
v.
Compact aluminium/PVC enclosure
10 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => '10 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 15.00,
                'total_price'     => 150.00,
                'sodo_bayaran'    => 'B27107',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.4.8',
                'category'        => 'Office',
                'name'            => 'VGA to HDMI converter',
                'specification'   => 'i.
Active VGA→HDMI scaler with audio embeds
ii.
Up to 1080p@60 Hz output
iii. 3.5 mm audio in jack
iv. USB-powered (5 V DC)
v.
Compact ABS enclosure
10 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => '10 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 0.00,
                'total_price'     => 200.00,
                'sodo_bayaran'    => 'B27107',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.4.9',
                'category'        => 'Office',
                'name'            => 'Smart TV 75 inch for Training Room',
                'specification'   => 'i.
Flip Pro 75″ (model LH75WMBWLGCXXS)
ii.
Display: 75″ UHD 4K (3840 × 2160) LED panel
iii.
Touch & Interaction: InGlass™ touch technology, 26 ms latency, 2
048 pressure levels, up to 20 simultaneous touch points
iv.
Connectivity: 2× HDMI In, 1× DisplayPort In, 3× front USB-A, 1×
rear USB-A, 1× USB-C (DP Alt-Mode + 65 W PD), RJ-45 LAN,
NFC reader, wireless screen sharing via SmartView+ and AirPlay
2
v.
Software & Functionality: Tizen-based digital whiteboard with
pen/brush modes and eraser gestures, built-in web browser, Office
365 and other productivity apps
vi.
Accessories: Dual passive styluses, VESA 400 × 400 wall-mount
kit (trolley stand optional), quick-start guide and installation
hardware
vii. Warranty: at least 3 years
viii. Installation and cabling must be included
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'SAMSUNG LH75WMBWLGC + NORTH BAYOU trolley',
                'serial_numbers'  => 'OLLJHNLL200009',
                'country_of_origin' => null,
                'unit_price'      => 18891.00,
                'total_price'     => 18891.00,
                'sodo_bayaran'    => 'B35403',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-04-14',
                'do_no'           => 'UTMCARD/2026/004',
            ],
            [
                'item_no'         => '2.1.5.1',
                'category'        => 'Office',
                'name'            => '3D Printer',
                'specification'   => 'i.
X1 Carbon Lidar High Speed 3D Printer (Optional Multicolor
upgrade with AMS)
ii.
CoreXY motion system with linear rails, up to 500 mm/s print
speed and 20 000 mm/s² acceleration
iii. 256 × 256 × 256 mm heated build volume (up to 110 °C on 220
V / 120 °C on 110 V) with PEI or Cool Plate surface
iv. Direct-drive dual-gear extruder feeding a 0.4 mm hardened-
steel nozzle rated to 300 °C
v.
Bambu Micro LiDAR auto-bed leveling (7 µm resolution), plus
filament run-out, door-open, and power-loss recovery sensors
vi. Integrated 1080p chamber-monitoring camera for remote print
observation
vii. Stock AMS multi-material system handling 4 spools
(expandable to 16 spools)
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
viii. 5″ 1280×720 touchscreen UI running Klipper-based firmware
(Bambu S1 board)
ix. Wi-Fi and Ethernet connectivity (plus SD/USB) for remote
control via Bambu App or web interface
x.
Supports PLA, PETG, TPU, ABS, ASA, PA, PC, and fiber-
reinforced polymers
xi. 100–240 VAC input (peak approx.850 W), idle approx. 22 W;
footprint 389 × 389 × 457 mm; weight approx. 14 kg
xii. Accessories: Power cable, Quick Start Guide, basic tool kit
(Allen wrenches, nozzle-cleaning needle), PEI spring-steel build
plate, Cool Plate surface, spare hardened-steel nozzles,
silicone sock, filament dust filter, spare PTFE tubing & couplers,
spare GT2 belts & pulleys, PLA filament (1 kg spool), PETG
filament (1 kg spool), TPU filament (0.75 kg spool), ABS
filament (1 kg spool), Nylon filament (1 kg spool)
xiii. Warranty: at least 3 years
xiv. Installation and cabling included
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION,',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 12175.00,
                'total_price'     => 24350.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.5.10',
                'category'        => 'Office',
                'name'            => 'Portable Induction Heating Module',
                'specification'   => 'i.
Power Output: 2000W max
ii.
Control Panel: Touch control with 9–10 heat levels
iii. Safety Features: Auto shut-off, overheat detection, child lock
iv. Top Surface: Crystal glass (easy to clean)
v.
Dimension (L x W): Approx. 290 mm x 370 mm
vi. Compliance: IEC 60335, SIRIM-certified plug
vii. Warranty: at least 3 years
viii. Installation and cabling included
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'MIDEA C20-RT2002',
                'serial_numbers'  => '5416600003A4B041C00642',
                'country_of_origin' => null,
                'unit_price'      => 196.00,
                'total_price'     => 196.00,
                'sodo_bayaran'    => 'B36303',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.5.11',
                'category'        => 'Office',
                'name'            => 'Transparent Display Containment Unit',
                'specification'   => 'i.
Dimension (W x D x H): 900 mm x 400 mm x 1800 mm
ii.
Structure: 6 mm tempered glass with aluminium/stainless
steel frame
iii. Shelving: 4-tier adjustable glass shelves
iv. Features: Lockable glass swing/sliding doors, anti-dust
sealing
v.
Use Case: Display of technical references, models, awards
vi. Warranty: at least 3 years
vii. Installation included
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'IKEA MILSBO; 3 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 2095.00,
                'total_price'     => 6285.00,
                'sodo_bayaran'    => 'B36199',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.5.12',
                'category'        => 'Office',
                'name'            => 'Secured Vertical File Management Locker',
                'specification'   => 'i.
Dimension (W x D x H): 900 mm x 450 mm x 1800 mm
ii.
Construction: Cold-rolled steel with anti-rust epoxy powder
coat
iii.
Configuration: 4–5 fixed shelves for document and binder
storage
iv.
Features: Lockable doors, label holder slots, reinforced
structure
v.
Use Case: Secure archiving of lab manuals, safety records,
SOPs
vi.
Warranty: at least 3 years
vii. Installation included
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => '| Harga dipadankan secara fuzzy: SERVER FOR WAREHOUSE MANAGEMENT SYSTEM (WMS) (S__',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 297854.00,
                'total_price'     => 297854.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.5.13',
                'category'        => 'Office',
                'name'            => 'Laminator',
                'specification'   => 'i.
Maximum Document Width: Up to 303 mm (A3 size)
ii.
Compatible Pouch Thickness: 75 µm and 100 µm
iii. Warm-Up Time: Approximately 4–6 minutes
iv. Lamination Speed: Around 230–250 mm/min
v.
Rollers: Dual-roller heat system with manual release lever for
jam prevention
vi. Cold Lamination Mode: Yes (compatible with self-adhesive
pouches)
vii. Power Consumption & Source: 220–240 V AC,
approx. 550 W
viii. Dimensions: Approx. 495 mm × 140 mm × 72 mm
ix. Weight: Approx. 1.6–1.85 kg
x.
Warranty: at least 3 years
xi. Accessories: 500 pieces of A4 and A3 Plastic Laminate
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1149.00,
                'total_price'     => 1149.00,
                'sodo_bayaran'    => 'B35101',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.5.14',
                'category'        => 'Office',
                'name'            => 'Binding Machine',
                'specification'   => 'i.
Punching Capacity: Up to 20–25 sheets of 70 gsm – 80 gsm
paper per lift
ii.
Binding Capacity: Up to 500 sheets using plastic combs
iii. Hole Configuration: 21 holes (standard A4 spacing)
iv. Maximum Binding Thickness: Supports combs up to 30 mm
(standard) and 50 mm (elliptical)
v.
Adjustable Side Margin: Yes (adjustable margin control)
vi. Material & Build: Heavy-duty steel construction
vii. Dimensions: Approximately 420 mm × 350 mm × 230 mm
viii. Weight: Around 11 kg
ix. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'GEOMASTER GM-2500 III',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 500.00,
                'total_price'     => 500.00,
                'sodo_bayaran'    => 'B36101',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.5.15',
                'category'        => 'Office',
                'name'            => 'Heavy Duty Paper Puncher',
                'specification'   => 'i.
Drilling Capacity: Up to 50 mm of paper (approximately 500
sheets of 75 gsm stock)
ii.
Paper Width & Work Surface: Handles up to 420 mm width;
working plate around 460 mm × 255 mm
iii. Hole Diameter Options: Interchangeable drill bits ranging from
3 mm to 8 mm
iv. Adjustable Margin: Range between 6–30 mm
v.
Drilling Pitch Patterns: Supports 1, 2, 3, 4, and 6-hole
configurations
vi. Operation Modes: Electric and manual drilling options
vii. Power Consumption: 120 W at 220 V / 50 Hz
viii. Dimensions: Approximately 460 mm × 430 mm × 500 mm
ix. Weight: Roughly 24 kg
x.
Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 5176.00,
                'total_price'     => 5176.00,
                'sodo_bayaran'    => 'B35101',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.5.16',
                'category'        => 'Office',
                'name'            => 'Heavy Duty Stapler',
                'specification'   => 'i.
Stapling capacity: Up to 215 sheets of standard 80 gsm paper
in a single press
ii.
Staple size compatibility: 1/4" to 1-1/4" heavy-duty staples (6
different staple sizes)
iii. Stapling type: Flat clinch for neat and compact stapling
iv. Mechanism: No-Jam™ technology for smooth operation
v.
Construction: All-metal, durable heavy-duty build
vi. Throat depth: Adjustable up to 2-3/4" (70 mm)
vii. Base: Non-skid base for stable operation
viii. Dimensions (L x W x H): Approx. 400 mm × 150 mm × 250 mm
ix. Weight: Around 2.8 kg
x.
Warranty: at least 3 years
xi. Accessories: 200 pieces of Stapler Bullets
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.2
AGRICULTURE LABORATORY
2.2.1
ICT EQUIPMENTS FOR AGRICULTURE LAB',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1494.00,
                'total_price'     => 1494.00,
                'sodo_bayaran'    => 'B36101',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.5.2',
                'category'        => 'Office',
                'name'            => 'Laser Engraving and Cutting Machine',
                'specification'   => 'i.
D1 Pro-20W High Accuracy DIY Laser Engraving & Cutting
Machine, Plug Type: UK Plug (Metal Gray)
ii.
Frame & Build Dimensions: All-metal construction; weight
Approx. 5.5 kg; footprint 600 mm × 590 mm × 150 mm
iii. Laser & Power: 20 W diode laser module; machine power draw
120 W; laser power consumption 84 W
iv. Laser Spot & Accuracy: Laser spot size 0.08 mm × 0.10 mm;
engraving accuracy 0.08 mm × 0.10 mm (20 W diode) / 0.03
mm × 0.03 mm (1064 nm IR)
v. Working Area: 430 mm × 390 mm (expandable to 920 mm ×
430 mm with optional enclosure)
vi. Precision & Speed: Motion precision 0.01 mm; maximum
engraving/cutting speed 400 mm/s
vii. Cutting capacity: Single-pass cuts up to 10 mm basswood; 8
mm black acrylic
viii. Max workpiece height: 50 mm
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
ix. Safety features: Four limit switches (X/Y home), flame detector,
shake sensor, and emergency-stop switch
x. Connectivity & Software: USB, Wi-Fi, and TF-card interfaces;
controlled via XCS LightBurn software
xi. Power supply: AC 100–220 V, 50–60 Hz
xii. Warranty: at least 3 years
xiii. Installation and cabling included
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 122264.00,
                'total_price'     => 22264.00,
                'sodo_bayaran'    => 'B35101',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.5.3',
                'category'        => 'Office',
                'name'            => 'All-in-one Printer',
                'specification'   => 'i.
Printer:
a.
Printing with maximum resolution: 4800 x 1200
optimized dpi
b.
ISO Print Speed: Black: 15 ISO ppm, Color: 8 ISO ppm
ii.
Ink System:
a.
Type: EcoTank Ink Bottles
b.
Colors: Cyan, Magenta, Yellow, Black
iii. Copying:
a.
ISO Copy Speed:
b.
Black: 11 ISO cpm
c.
Color: 5.5 ISO cpm
iv. Scanning:
a.
Hardware Resolution: 1200 x 2400 dpi
b.
Maximum Resolution: 9600 dpi interpolated
c.
Color Bit Depth: 48-bit input / 24-bit output
d.
Maximum Scan Area: 8.5" x 14"
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
v.
Paper Handling:
a.
Input capacity: at least 250 sheets
b.
Supported paper sizes: 3.5" x 5" to 8.5" x 47.2"
c.
Standard sizes: Letter, Legal, A4, A6, Executive
vi. Connectivity:
a.
Hi-Speed USB
b.
Wi-Fi 4 (802.11 b/g/n)
c.
Ethernet (10/100 Mbps)
d.
Wi-Fi Direct
vii. Warranty: at least 3 years
viii. Installation and cabling included
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'EPSON ECOTANK L3250; 3 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 690.00,
                'total_price'     => 2070.00,
                'sodo_bayaran'    => 'B35201',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.1.5.4',
                'category'        => 'Office',
                'name'            => 'Paper Shredder',
                'specification'   => 'i.
Shredding capacity: Accepts up to 12 sheets of 20 lb. bond
paper per pass
ii.
Cut type and size: Micro-cut shredding into particles
measuring 5/32 by 15/32 inches (0.4 cm x 1.2 cm)
iii. Continuous run time: Operates continuously for up to 60
minutes before requiring a cool-down period.
iv. Anti-Jam feature: Equipped with an auto-reverse function to
clear paper jams
v.
Bin capacity: 5.0-gallon pullout waste bin
vi. Warranty: at least 3 years
vii. Installation and cabling included
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3446.00,
                'total_price'     => 6892.00,
                'sodo_bayaran'    => 'B36101',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.5.5',
                'category'        => 'Office',
                'name'            => 'Modular Cold Storage and Preservation Unit',
                'specification'   => 'i.
Type: 4-door hybrid cooling system
ii.
Capacity: Approx. 525 liters (319L refrigeration, 206L
freezer)
iii. Energy efficiency: Inverter-based compressor with eco mode
iv. Features: Multi-zone temperature control, anti-bacterial lining
v.
Certification: CE Certified, R600a refrigerant, RoHS
compliant
vi. Warranty: at least 3 years
vii. Installation and cabling included
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'TOSHIBA GR-RF531WI-PMY',
                'serial_numbers'  => 'A07DZ-0058',
                'country_of_origin' => null,
                'unit_price'      => 4190.00,
                'total_price'     => 4190.00,
                'sodo_bayaran'    => 'B35301',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.5.6',
                'category'        => 'Office',
                'name'            => 'Compact Microwave Heating Unit',
                'specification'   => 'i.
Capacity: 25 liters internal chamber
ii.
Output Power: 900W (Microwave)
iii. Features: Quick reheat, defrost mode, child lock safety
iv. Safety Standard: Compliant with IEC 60335
v.
Warranty: at least 3 years
vi. Installation and cabling included
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'PANASONIC NN-ST34NBMPQ',
                'serial_numbers'  => '5095250354.0',
                'country_of_origin' => null,
                'unit_price'      => 541.00,
                'total_price'     => 541.00,
                'sodo_bayaran'    => 'B36304',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.1.5.7',
                'category'        => 'Office',
                'name'            => 'Integrated Water Dispensing and Filtration System',
                'specification'   => 'i.
Output Modes: Hot (≥85°C), Cold (≤10°C), Ambient
ii. Filtration: Multi-stage RO/Nano Positive filter (sediment,
carbon, UV)
iii. Features: Energy-saving mode, leak detector, auto-clean
function
iv. Certification: NSF Certified, Halal Certified
v. Warranty: at least 3 years
vi. Installation and cabling included
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3783.00,
                'total_price'     => 3783.00,
                'sodo_bayaran'    => 'B35101',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.5.8',
                'category'        => 'Office',
                'name'            => 'Technical Square Collaborative Platform',
                'specification'   => 'i.
6 seats configuration
ii.
Shape: Square
iii. Dimension (L x W x H): 1800 mm x 900 mm x 750 mm
iv. Material: Scratch-resistant laminated MDF with powder-
coated steel legs
v.
Load capacity: Engineered to support 6 personnel in a
collaborative workspace
vi. Features: Anti-slip feet, optional cable passthrough
vii. Warranty: at least 3 years
viii. Installation and cabling included
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 4055.00,
                'total_price'     => 4055.00,
                'sodo_bayaran'    => 'B36103',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.1.5.9',
                'category'        => 'Office',
                'name'            => 'Compact Air Convection Unit',
                'specification'   => 'i.
Capacity: 6 liters
ii.
Power rating: Approx. 1500W
iii. Features: Adjustable temp (80–200°C), digital timer, auto shut-
off
iv. Safety: Overheat protection, insulated handle
v.
Warranty: at least 3 years
vi. Installation and cabling included
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.',
                'brand'           => 'HANABISHI HA2683',
                'serial_numbers'  => '289.0',
                'country_of_origin' => null,
                'unit_price'      => 406.00,
                'total_price'     => 406.00,
                'sodo_bayaran'    => 'B36304',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.2.1.1',
                'category'        => 'Agriculture Lab',
                'name'            => 'High Performance AI Mini Supercomputer',
                'specification'   => 'i.
67 INT8 TOPS
ii.
NVIDIA ampere architecture with 1024 CUDA
cores and 32 tensor cores
iii. core Arm® Cortex®-A78AE v8.2 64-bit CPU
1.5MB L2 + 4MB L3
iv. 8GB 128-bit LPDDR5 102 GB/s
v.
512 NVMe SSD
vi. 7W-25
8 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 17000.00,
                'total_price'     => 136000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 8,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.1.2',
                'category'        => 'Agriculture Lab',
                'name'            => 'High Performance Laptop - For Outdoor Robot Controls',
                'specification'   => 'i.
i7 processor HX series (14th Gen) or higher
ii.
Windows 11 Pro for business
iii. 16" FHD+(1920 x 1200), 144Hz, IPS-level
iv. HM770
v. RTX™ 4050 laptop GPU 6GB GDDR6 powers advanced AI
with 194 AI TOPS Up to 2370MHz boost clock 115W
maximum graphics power with dynamic boost
vi. DDR5-5600 96GB 2 slots or higher
vii. 1x M.2 SSD slot (NVMe PCIe Gen4), 1x M.2 SSD slot (NVMe
PCIe Gen5) compatible
viii. Firmware Trusted Platform Module (fTPM) 2.0
webcam shutter Kensington lock
ix. 1x Type-C (USB3.2 Gen2 / DisplayPort™/ Power delivery 3.0)
3x Type-A USB3.2 Gen1 1x HDMI™ 2.1 (8K @ 60Hz / 4K @
120Hz) 1 x RJ45
x. 4-Cell Battery, 65 Watt-hour (Wh)
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xi. Accessories:
a. Monitor
b. mouse
c. keyboard
d. external hard drive
e. headset
f. backpack
g. laptop harness
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'MSI Cyborg 15 A13VEK-887MY',
                'serial_numbers'  => 'K2511N0068521, K2511N0068520, K2509N0107911',
                'country_of_origin' => null,
                'unit_price'      => 19000.00,
                'total_price'     => 57000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.1.3',
                'category'        => 'Agriculture Lab',
                'name'            => 'Embedded AI Systems',
                'specification'   => 'i.
2048-core ampere architecture
ii.
GPU with 64GB tensor cores or higher
iii.
12-core Arm® Cortex®-A78AE v8.2 64-bit CPU
iv.
2x NVDLA v2.0
v.
PVA v2.0
vi.
64GB 256-bit LPDDR5
vii.
64GB eMMC 5.1
viii. Reference carrier board
ix.
MIPI CSI-2 camera connector
x.
PCIe x 16 connector (supports x 8)
xi.
Gigabit ethernet
xii.
M.2 key E, M.2. key M
xiii. 2x USB Type-C or higher
xiv. 4 x USB 3.2 type-A or higher
xv.
Micro-USB 2.0 (for device mode)
xvi. DisplayPort dual-mode
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xvii. microSD slot
xviii. 40-pin header
xix. At least with: GPIOs, I2C, I2S, SPI, UART
xx.
Power, Force recovery, reset buttons
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 10000.00,
                'total_price'     => 30000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.1.4',
                'category'        => 'Agriculture Lab',
                'name'            => 'High Performance PC - Workstation',
                'specification'   => 'i.
Processor: Ultra 5 245K or higher
ii.
GPU: GeForce RTX 4070 12GB (MSI / Gigabyte) or higher
iii. Cooler: PCCOOLER RT400 ARGB BK
iv. Motherboard: Z890 gaming plus Wi-Fi
v. RAM:  Ripjaws S5 32GB DDR5 6000Mhz (2 x 16GB)
vi. SSD: C910 500GB SSD
vii. Power supply: CSK650 (80+ Bronze)
viii. Casing: AX61 Elite (4 x ARGB Fans)
ix. Accessories: Power cable, HDMI cable, 1 display port
x. SSD: G50 NVMe Gen4 1TB
xi. Keyboard & Mouse: MK120 combo
xii. LCD TV 24-inch
xiii. Warranty: at least 3 years
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'MSI MS7E 5060TS2X Desktop; termasuk monitor MSI PRO MP275',
                'serial_numbers'  => 'MS-7E32 5060TS2Xi9-MY-020B2410001981, MS-7E32 5060TS2Xi9-MY-020B2410001983, MS-7E32 5060TS2Xi9-MY-020B2410001982',
                'country_of_origin' => null,
                'unit_price'      => 11623.00,
                'total_price'     => 34869.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.1.5',
                'category'        => 'Agriculture Lab',
                'name'            => 'Single Board Computer (SBC)',
                'specification'   => 'i.
Processor: Broadcom BCM2712
ii.
2.4GHz quad-core, 64-bit arm cortex-A76 CPU, with
512KB L2 caches and a 2MB shared L3 cache
iii.
Video Core VII GPU, supporting OpenGL ES 3.1, Vulkan 1.2
iv.
4Kp60 HEVC decoder
v.
LPDDR4X-4267 SDRAM
vi.
Dual-band 802.11ac Wi-Fi
vii. Bluetooth 5.0 / Bluetooth Low Energy (BLE)
viii. Micro SD card slot, with support for high-speed
SDR104 mode
ix.
2 × USB 3.0 ports, supporting simultaneous
x.
2 × USB 2.0 ports
xi.
Gigabit Ethernet, with PoE+ support (requires PoE+ HAT)
xii. 2 × 4 lane MIPI camera/display transceivers
xiii. PCIe 2.0 x 1 interface for fast peripherals
18 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xiv. 5V/5A DC power via USB-C, with power delivery support
xv.
Raspberry Pi standard 40-pin header
xvi. Real-Time Clock (RTC), powered from external battery
xvii. On-board power button
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.2.2
ROBOTIC EQUIPMENTS FOR AGRICULTURE',
                'brand'           => 'Raspberry Pi 5 Board 8GB',
                'serial_numbers'  => '0000911044687676, 0000911044687784, 0000911044643137, 0000911044693724, 0000911044687687, 0000911044687675, 0000911044685252, 0000911044687690, 0000911044685230, 0000911044684866, 0000911044684859, 0000911044687711, 0000911044687682, 0000911044687695, 0000911044684855, 0000911044687688, 0000911044685235, 0000911044692929',
                'country_of_origin' => null,
                'unit_price'      => 680.00,
                'total_price'     => 12240.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 18,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.2.1',
                'category'        => 'Agriculture Lab',
                'name'            => 'Autonomous Mobile Robot (AMR)/UGV – Package 1',
                'specification'   => 'i.
Weight: 145-150 kg
ii.
Dimension: Approx. 1023 mm x 778 mm x 400 mm
iii.
Speed: Approx. 1.5 m/s
iv.
IP Rating: IP54
v.
Chassis height: Approx. 120 mm
vi.
Track width: Approx. 150 mm
vii.
Curb weight: Approx. 180 kg
viii. Battery type: Lithium battery
ix.
Battery parameters: 60AH
x.
Power drive motor: 2 × 1500W Brushless servo motor
xi.
IP Grade: IP22
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xii.
Control mode: Remote control/Command control mode
xiii. RC transmitter: 2.4G/extreme distance 200M
xiv. System interface: CAN
xv.
Warranty: at least 4 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 32000.00,
                'total_price'     => 96000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.2.2',
                'category'        => 'Agriculture Lab',
                'name'            => 'Autonomous Mobile Robot (AMR)/UGV - Package 2',
                'specification'   => 'i.
Chassis type: Modular robot chassis
ii.
Material: High-strength aluminium or reinforced plastic
iii.
Dimensions: Approx. 1060 mm × 672 mm × 435 mm
iv.
Weight: Approx. 145 – 150 kg
v.
Speed: Approx. 0 – 8 km/h
vi.
Load capacity: Designed to carry 40–100 kg
vii. IP rating: IP64 (dust and splash-proof)
viii. Power supply: 48V Lithium Iron Phosphate (LiFePO4) battery
ix.
Motor type: Brushless DC motor with high torque
x.
Drive type: Tank drive with two independent motors
xi.
Mounting: Compatible with various payloads (sensors, cameras,
arms)
xii. Communication: CAN bus for controlling motors and sensors
xiii. Applications: Used for mobile robotic platforms, autonomous
vehicles, and research projects
xiv. Warranty: at least 4 years
4 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 32000.00,
                'total_price'     => 96000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 4,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.2.3',
                'category'        => 'Agriculture Lab',
                'name'            => 'Universal Robot (COBOT) - UR5',
                'specification'   => 'i.
Robot type: Collaborative 6-Axis Robotic Arm (COBOT)
ii.
Reach: Approx. 850 mm
iii. Payload Capacity: Approx. 5 kg
iv. Degrees of Freedom (DOF):  6 (with full rotational flexibility)
v.
Repeatability:  ±0.03 mm
vi. Weight: Approx. 16 kg
vii. Power Supply:  24V DC
viii. Communication Protocols:  Modbus TCP, Ethernet/IP,
Profinet
ix. Safety Features:  Force sensing, collision detection, and
customizable speed/force limits
x.
Mounting: Flexible mounting options (tabletop, ceiling, or wall-
mounted)
xi. Programming interface:  Polyscope GUI, URScript, ROS
(Robot Operating System) compatibility
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xii. End-of-Arm tool compatibility:  Grippers, cameras, sensors,
and welding tools
Mode of operation:  Easy programming with no specialized
training required safe human-robot interaction
xiii. Applications:  Assembly, packaging, CNC, testing, and
research in automation environment
xiv. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 160000.00,
                'total_price'     => 160000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.2.4',
                'category'        => 'Agriculture Lab',
                'name'            => 'Torque Reaction Arm',
                'specification'   => 'i.
Dual tilting arms expand the range of movement
ii.
Dual tilting arms allow for reach below the mounting point
iii. The connections between the two arm sections allow for
unrestricted movement
iv. All head member options available
v.
Brake options for complete or partial lockup
vi. Vertical reach: Approx. 52 inch
vii. Horizontal reach: Approx 57 inch
viii. Max weight: Approx 53 lbs
ix. Torque: Approx. 0-300 Nm
x.
Warranty: at least 2 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 18000.00,
                'total_price'     => 8000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.2.5',
                'category'        => 'Agriculture Lab',
                'name'            => 'Soft Robotic Arm',
                'specification'   => 'i.
Diameter: Approx. 80 mm
ii.
Length of bellows segments: Approx. 150 mm
iii. Length of rotary drives: Approx. 150 mm
iv. overall length: 1050 mm
v.
Degrees of freedom: 7 DOF or higher
vi. Dead weight of bellows segments: 500 gm
vii. Dead weight of rotary drives: 750 gm
viii. Working pressure: 6 bar or higher
ix. Repetition accuracy: +/- 5 mm
x.
Warranty: at least 2 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 60000.00,
                'total_price'     => 120000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.2.6',
                'category'        => 'Agriculture Lab',
                'name'            => 'Bench-Mounted Torque Reaction Arm',
                'specification'   => 'i.
Type: Torque Arm
ii.
Series: QTA
iii. Max torque capacity: 150 Nm
iv. Max weight capacity: 4.54 kg
v.
Max working reach: 1321 mm
vi. Vertical travel: 22 inch
vii. Vertical travel: 559 mm
viii. Overall height: 2000 mm
ix. Warranty: at least 2 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.2.3
DRONES FOR AGRICULTURE',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 8000.00,
                'total_price'     => 8000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.3.1',
                'category'        => 'Agriculture Lab',
                'name'            => 'Agriculture Drone',
                'specification'   => 'i.
Weight: Approx. 39.9 kg (exclude battery) /52 kg (include
battery)
ii.
Max take-off weight for spraying: 92 kg (at sea level)
iii. Max take-off weight for spreading: 103 kg (at sea level)
iv. Max diagonal wheelbase:  2200 mm
v.
Dimensions: Approx. 2800 mm × 3085 mm × 820 mm (arms
and propellers unfolded)
vi. 1590 mm × 1900 mm × 820 mm (arms unfolded and
propellers folded)
vii. 1115 mm × 750 mm × 900 mm (arms and propellers)
viii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 35000.00,
                'total_price'     => 35000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.3.2',
                'category'        => 'Agriculture Lab',
                'name'            => 'Hybrid Petrol Powered Drone',
                'specification'   => 'i.
Take-off weight: 64 kg
ii.
Wheelbase: Approx. 1965 mm
iii. Size (L x W x H): Approx. 760 mm x 720 mm x 850 mm
(folded)
iv. Size (L x W x H): Approx. 1520 mm x 1530 mm x 710 mm
(unfolded)
v.
Fuel consumption:  5.9 L/hour
vi. Spray width: Approx. 6 m
vii. Spray rate: Approx. 6.5 L/min
viii. Operating liquid tank capacity: Approx. 20 L
ix. Maximum liquid tank capacity: Approx. 22 L
x.
Fuel tank capacity: Approx. 3.5 L
xi. Flying speed:  9 m/s
xii. Loiter accuracy (RTK Enabled)
xiii. Horizontal & vertical ±0.1 m
xiv. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 150000.00,
                'total_price'     => 150000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.3.3',
                'category'        => 'Agriculture Lab',
                'name'            => 'Development Drone with Built-in ROS Support',
                'specification'   => 'i.
36 x 70 mm SWaP-optimized for GPS-denied navigation,
obstacle avoidance
ii.
Powered by Qualcomm QRB5165: 8 cores up to 3.091GHz,
8GB LPDDR5
iii. Seven simultaneous MIPI inputs such as EO, thermal, TOF,
and CV cameras
iv. Pre-integrated thermal camera support for FLIR Lepton® and
Boson®
v.
Open-source software: OpenCV, ROS 2, Docker, PX4 and
ArduPilot integrated flight controller
vi. Integrated flight controller on DSP with TDK® ICM-42688
IMU and ICP-10111 Barometer
vii. The QRB5165’s Qualcomm AI Engine pushes 15 TOPS of AI
performance with industry leading power consumption
viii. Onboard QRB5165: Hexagon tensor accelerator, GPU, DSP,
NPU
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
ix. Weight: Approx. 1347 gm (1011 g without battery)
x.
Wheelbase (diagonal motor-to-motor distance): Approx. 480
mm
xi. Motors: 2216-880KV motors
xii. 4 Propellers: 10 in. / 254 mm
xiii. Frame kit: S500 V2
xiv. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 55000.00,
                'total_price'     => 55000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.3.4',
                'category'        => 'Agriculture Lab',
                'name'            => 'Development Drone - Includes Flight Controller',
                'specification'   => 'i.
Autopilot flight controller: Pixhawk 6C
ii.
GPS: M10 GPS module
iii. Telemetry radio: SiK telemetry radio V3
iv. Frequency: 433 MHz
v.
X650 V2 frame kit
vi. 4 Motors - T-Motor MN4014 KV330 motor with XT30 plug
vii. 4 LED - NeoPixel WS2812 LED (Setup instruction)
viii. 4 ESCs - Tekko32 F4 45A ESC with XT30 plugs
ix. Gemfan 1555 carbon fiber propellers (4 pieces)
x.
Power distribution board (XT60 plug for battery & XT30 plug
for ESCs & peripherals)
xi. RadioMaster TX16S RC controller
xii. RadioMaster R81 receiver
xiii. Depth camera mount
xiv. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 15000.00,
                'total_price'     => 15000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.3.5',
                'category'        => 'Agriculture Lab',
                'name'            => 'FPV Drone',
                'specification'   => 'i.
Max speed:
a.
Manual mode: 140 km/h (87 mph)
b.
Sport mode: Approx. 97 km/h (60 mph)
c.
Normal mode: Approx. 54 km/h (33 mph)
ii.
Max flight time: Up to 20 minutes
iii. Max flight distance: Approx. 16.8 km (in optimal conditions)
iv. Max service ceiling: Approx.  6000 m above sea level
v.
Camera system: Sensor: 1/2.3” CMOS, Effective Pixels: 12
MP, Lens: 14.66 mm equivalent, 150° FOV, f/2.8
vi. Video resolution:
a. 4K at 50/60 fps
b. 1080p at 50/60/100/120 fps
c. Max video bitrate: 120 Mbps
vii.
Stabilization: RockSteady EIS (Electronic image
stabilization)
8 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
viii. OcuSync 3.0 (O3):
a. Low latency HD video feed up to 10 km
b. 50 Mbps video transmission
ix.
Remote controller: DJI FPV remote controller 2
x.
Optional: DJI motion controller (joystick-free, intuitive)
xi.
Battery type: 6S LiPO 2000 mAH
xii.
Charging hub: Multi-battery hub available: 2 – 4 pieces
xiii. Charging time: 50 minutes per battery
xiv. Accessories:
a. FPV goggles V2 or higher: 1 piece
b. Remote controller: 1 piece
c. Intelligent flight battery: 1 piece
d. Propellers (spare included): 1 piece
e. Charging hub: 1 piece
f. USB-C cables and tools: 1 piece
g. ND filters, motion controller, carrying case: 1 piece
xv.   Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengen',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 8000.00,
                'total_price'     => 64000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 8,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.3.6',
                'category'        => 'Agriculture Lab',
                'name'            => 'MATRICE 350 SERIES-PART08-TB65 Intelligent Flight Battery',
                'specification'   => 'i.
Capacity 5880 mAh
ii. Voltage 44.76 V
iii. Li-ion battery type
iv. Energy: 263.2 Wh
v. Weight: 1.35 kg
vi. Compatible with Matrice 350 RTK and Matrice 300 RTK
vii. Warranty: at least 3 years
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.2.4
LiDAR FOR AGRICULTURE',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 6000.00,
                'total_price'     => 18000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.4.1',
                'category'        => 'Agriculture Lab',
                'name'            => '3D Depth Sensing Device',
                'specification'   => 'i.
Depth technology
ii.
Structured light
iii. Depth range: 0.6m to 8m
iv. Wavelength:  850 nm
v.
Processor: Orbbec ASIC
vi. Warranty: at least 3 years
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 4000.00,
                'total_price'     => 20000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.4.2',
                'category'        => 'Agriculture Lab',
                'name'            => 'LiDAR Rangefinder',
                'specification'   => 'i.
Detection range:
a.
10 cm to 4000 cm at 90% reflectivity, 100K lux
b.
10 cm to 1350 cm at 10% reflectivity, 100K lux
ii.
Accuracy: ±5 cm (10 cm to 5 m), ±1% (5 to 4000 cm)
iii.
Distance resolution: 1 cm
iv.
Frame rate: 100 Hz
v.
Repeatability: ±2 cm, 0.1~35 m @ 90% reflectivity
vi.
Anti-ambient light: Function under 100k Lux Ambient light
vii.
Operating temperature: -20 ºC to 60 ºC
viii.
Protection level: IP65
ix.
Warranty: at least 3 years
9 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3500.00,
                'total_price'     => 31500.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 9,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.4.3',
                'category'        => 'Agriculture Lab',
                'name'            => '3D LiDAR (Sensor)',
                'specification'   => 'i.
Weight: Approx. 590 g
ii.
Return type: Dual returns
iii. Channels: 16
iv. Range:100 m
v.
Points per second: Approx. 600,000
vi. Vertical FOV: +15°
vii. Horizontal FOV: 360°
viii. Design: Rugged and protective for harsh environments
ix. Connectors: RJ45 / M12
x.
Warranty: at least 3 years
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 37000.00,
                'total_price'     => 111000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.4.4',
                'category'        => 'Agriculture Lab',
                'name'            => '4D LiDAR',
                'specification'   => 'i.
Field of View (FOV): 360° × 90°
ii.
Scanning: Ultra-wide-angle scanning
iii. Minimum detection distance: 0.05 m near blind spot
iv. Range: 30 m @ 90% reflectivity
v.
Point rate: 21600 points/s
vi. Frequency: Effective frequency
vii. Weight: 230 gm
viii. Size: Approx. 75 mm × 75 mm × 65 mm
ix. Warranty: at least 3 years
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.2.5
SENSORS FOR AGRICULTURE',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 7000.00,
                'total_price'     => 35000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.5.1',
                'category'        => 'Agriculture Lab',
                'name'            => 'Flow Sensors',
                'specification'   => 'i.
Sensor:  MT9V034 global shutter CMOS camera
ii.
Lens: 2.8 mm default lens; wide-angle lens
iii. Processor:STM32F427 Cortex-M4 32-bit ARM processor
operating at 168 MHz
iv. Frame Rate: up to 400 Hz, enabling responsive optical flow
tracking
v.
Resolution:  752 × 480 pixels
vi. Field of View (FOV):  Approximately 35° to 60°
vii. Flow Accuracy: 1.0 pixel per frame in well-textured surfaces
viii. Altitude Range: 0.3 m to 5 m, ideal for low-altitude indoor or
GPS-denied flights
4 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
ix. Interfaces: I2C, UART, and SPI interfaces
x.
Output: Provides real-time data including optical flow vectors,
flow quality, gyroscope data, and distance measurements
xi. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 9700.00,
                'total_price'     => 38800.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 4,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.5.2',
                'category'        => 'Agriculture Lab',
                'name'            => 'Motion Capture System',
                'specification'   => 'i.
Capture volume:
a. 20 feet × 20 × 8 feet
b. 6 m × 6 m × 2 m setup area: 30 feet × 30 feet
c. 9 m × 9 m HMDs/objects: Approx. 6
ii.
Infrared optical tracking camera with global shutter
iii. Frame rate:  at least 360 FPS (Flex 13), and 420 FPS
(PrimeX 13)
iv. Resolution: 1.3 Mega Pixels (Flex 13) or up to 4.2 Mega
Pixels (PrimeX 41)
v.
FOV:  56° to 82°
vi.
Latency: Less than 10 ms
vii. Synchronization: Genlock, Sync in/out, or Ethernet time
protocol (IEEE 1588)
viii. Connection:  Gigabit Ethernet or USB 3.0
ix.
Multiple OptiTrack cameras (6–12 for setups)
x.
Calibration wand and square
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xi.
Synchronization hub (for multiple camera sync)
xii. Passive markers (spherical, reflective)
xiii. Mounting brackets
xiv. Ethernet cables and power supplies
xv. Software license
xvi. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 109000.00,
                'total_price'     => 109000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.5.3',
                'category'        => 'Agriculture Lab',
                'name'            => 'Computer Vision Sensors',
                'specification'   => 'i.
4 TOPS of processing power (1.4 TOPS for AI - RVC2 NN
performance)
ii.
Run any AI model
iii. Encoding: H.264, H.265, MJPEG - 4K/30FPS, 1080P/60FPS
iv. Warranty: at least 3 years
4 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 2000.00,
                'total_price'     => 8000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 4,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.5.4',
                'category'        => 'Agriculture Lab',
                'name'            => 'High-Precision GNSS Receivers/RTK Positioning Systems',
                'specification'   => 'i.
MX23A18SF1 multifunctional interface: CAN, RS232, RS422,
PPS IN, PPS OUT
ii.
FAKRA_Cx antenna interface: FAKRA_Dx
iii. Model: INS622-3B
iv. Size: Approx. 116 mm x 100.1 mm x 27.4 mm
v.
Net weight: 320 gm or less
vi. Working temperature: -40 °C to 85 °C
vii. Power supply: 9V-32VDC
viii. Power consumption: 10W (rated voltage 12V/24V) or less
ix. Data refresh rate: 200 Hz
x.
Warranty: at least 3 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 2000.00,
                'total_price'     => 4000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.5.5',
                'category'        => 'Agriculture Lab',
                'name'            => 'Inertial Navigation Systems (INS)',
                'specification'   => 'i.
Gyroscope: 3-axis gyroscope (X, Y, Z) for measuring angular
velocity
ii.
Accelerometer: 3-axis accelerometer (X, Y, Z) for detecting
linear acceleration
iii.
Magnetometer: 3-axis magnetometer (X, Y, Z) for magnetic
field orientation
iv. Barometer: Built-in barometric sensor for altitude and
atmospheric pressure sensing
v.
Total Axes: Combined 10-axis sensor system for
comprehensive motion tracking
vi. Built-in Kalman Filter Algorithm For fusing IMU data and
delivering stable orientation output.
vii. Output formats: Euler angles: Roll, Pitch, Yaw, Quaternion,
Linear acceleration, Angular velocity, Magnetic field vectors
altitude/pressure data (via Barometer)
viii. Operating Voltage: 3.3V – 5V DC
6 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
ix. Power consumption: Low power consumption (less than 150
mW)
x.
Enclosure:  custom metal case for shielding and heat
dissipation
xi. Mounting: Mountable via standard holes or embedded in
chassis
xii. ROS support: available
xiii. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 2000.00,
                'total_price'     => 12000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 6,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.5.6',
                'category'        => 'Agriculture Lab',
                'name'            => 'Motion Tracking Sensors',
                'specification'   => 'i.
IP rating: IP67 – dustproof and waterproof enclosure
ii.
Sensor fusion engine: Integrated with IMU, GNSS, visual
odometry, and compass
iii. Update rate: High-frequency data fusion for real-time
performance
iv. Interface options: CAN, RS232, PPS, Ethernet, FAKRA
antenna interface
v.
Power supply: 9V – 32V DC input
vi. Power consumption: less than 10W typical
vii. Operating temperature: -40°C to +85°C
viii. Form factor: Approx. 116 mm × 100.1 mm × 27.4 mm
ix. weight: less than 320 gm
x.
Warranty: at least 2 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 36000.00,
                'total_price'     => 36000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.5.7',
                'category'        => 'Agriculture Lab',
                'name'            => 'Temperature and Humidity Sensor',
                'specification'   => 'i.
Protection: Mesh-protected and weather-proof enclosure
ii.
Humidity accuracy:  ±2% RH (typical at 25°C)
iii. Temperature accuracy:  ±0.3°C (typical from 0–65°C)
iv. Humidity repeatability:  ±0.1% RH
v.
Temperature repeatability:  ±0.1°C
vi. Resolution: 0.01°C (temperature), 0.01% RH (humidity)
vii. Operating range: -40°C to +125°C (temperature), 0% to
100% RH (humidity)
viii. Response time:  Less than 8 seconds (humidity)
ix. Supply voltage:  2.4V to 5.5V DC
x.
Interface: I2C digital output
xi. Current consumption: 1.5 µA (standby), 550 µA (measuring)
xii. Mounting: Suitable for outdoor installation with mounting
holes
xiii. Warranty: at least 2 years
20 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => '36 unit, tiada nombor siri',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1500.00,
                'total_price'     => 30000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 20,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.5.8',
                'category'        => 'Agriculture Lab',
                'name'            => 'Temperature and Humidity Sensor - Less IP Rating',
                'specification'   => 'i.
Sensor type: Temperature and humidity digital sensor
ii.
Accuracy:  ±2% RH (humidity), ±0.3°C (temperature)
iii. Repeatability:  ±0.1% RH (humidity), ±0.1°C (temperature)
iv. Resolution:  0.01°C (temperature), 0.01% RH (humidity)
v.
Operating range: -40°C to +125°C (temperature), 0% to
100% RH (humidity)
vi. Interface: I2C digital output (with clock stretching support)
vii. Supply voltage: Approx. 2.4V to 5.5V DC
viii. Current consumption: Approx. 150 µA (measuring), less than
0.5 µA (standby)
ix. Response time: Less than 8 seconds for humidity
x.
Features: Enhanced signal processing, high reliability, long-
term stability
xi. Enclosure: Bare sensor module (requires housing for outdoor
use)
xii. Warranty: at least 2 years
36 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => '36 unit, tiada nombor siri',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1500.00,
                'total_price'     => 54000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 36,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.5.9',
                'category'        => 'Agriculture Lab',
                'name'            => 'Soil Moisture Sensor',
                'specification'   => 'i.
Product model S-temp & VWC & EC-02A
ii.
Interface RS-485
iii. Protocol MODBUS-RTU RS485
iv. Power supply: 3.6 ~ 30V DC
v.
Current consumption: 6mA @ 24V DC
vi. Operating temperature: -40℃ to +85℃
vii. Electrode: Probe material anti-corrosion
viii. Sealing material black flame-retardant epoxy resin
ix. IP Rating IP68
x.
Cable length: 5 m
xi. Warranty: at least 2 years
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.2.6
MOTORS FOR AGRICULTURE',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1000.00,
                'total_price'     => 3000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.6.1',
                'category'        => 'Agriculture Lab',
                'name'            => 'Servo Motors',
                'specification'   => 'i.
Single axis (controls 1 motor)
ii.
High performance servo control of BLDC, PMAC, and ACIM
motors
iii. Torque, velocity, position, and trajectory control modes
iv. Sensorless speed control
v.
Operating Voltage: 12V to 56V
vi. Operating current:  70A to 120A
vii. Control over USB, UART (Isolated), STEP/DIR (Isolated),
analog voltage, PWM (Isolated), and CAN (Isolated)
viii. Warranty: at least 2 years
30 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => '| Harga dipadankan secara fuzzy: SERVOMOTORS(AGRICULTURELAB) = ~~ ~—~&',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 300.00,
                'total_price'     => 9000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 30,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.6.2',
                'category'        => 'Agriculture Lab',
                'name'            => 'Motors Encoder',
                'specification'   => 'i.
Encoder type: Absolute
ii.
Output type: RS-485
iii. Pulses per revolution: 16384
iv. Voltage supply: 3.8V to 5.5V
v.
Actuator type: Open centre
vi. Warranty: at least 2 years
12 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1000.00,
                'total_price'     => 12000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 12,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.6.3',
                'category'        => 'Agriculture Lab',
                'name'            => 'Motor Controller',
                'specification'   => 'i.
Board type:  STM32 Nucleo-144 development board
ii.
Microcontroller:  STM32H753ZIT6 (Arm Cortex-M7, 480 MHz,
2MB flash, 1MB RAM)
iii. Operating voltage: Approx. 3.3V
iv. USB interface: USB FS device and USB HS with ULPI
v.
Communication interfaces: USART, SPI, I2C, CAN FD, Ethernet
vi. Debug interface: ST-LINK/V3 embedded debugger/programmer
vii. Expansion connectors: ST morpho (Arduino Uno V3-compatible
and ST Zio)
viii. Supported IDEs: STM32CubeIDE, Keil MDK, IAR EWARM,
ix. Power supply options: USB, External 5V, ST-LINK
x.
User interfaces: 3 user LEDs, 2 push-buttons (user + reset)
xi. Target use:  Prototyping, motor control, IoT, real-time systems,
and sensor interfacing
xii. Warranty: at least 2 years
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'STMICROELECTRONICS NUCLEO-H753ZI',
                'serial_numbers'  => 'MB1364-H753ZI-E01 A254900844, MB1364-H753ZI-E01 A254900588, MB1364-H753ZI-E01 A254900268',
                'country_of_origin' => null,
                'unit_price'      => 1000.00,
                'total_price'     => 3000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.6.4',
                'category'        => 'Agriculture Lab',
                'name'            => 'Outrunner Brushless Motor',
                'specification'   => 'i.
Motor type: Outrunner brushless DC motor
ii.
Power rating: 1600W
iii. Voltage rating: 160 kV (RPM per Volt)
iv. Voltage compatibility: 12S LiPo (up to 50.4V max)
v.
Shaft diameter: Approx. 8 mm
vi. Mounting: Bolt-on flange type
vii. Cooling: Air-cooled via external rotation
viii. Weight: Approx. 400–500 grams
ix. Applications: Electric skateboards, robotics, DIY electric
vehicles, propulsion systems
x.
Warranty: at least 2 years
8 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.2.7
COMMUNICATION EQUIPMENT FOR AGRICULTURE',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1000.00,
                'total_price'     => 8000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 8,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.7.1',
                'category'        => 'Agriculture Lab',
                'name'            => 'Dual Band Outdoor Wi-Fi Omni Antenna with Marine Mount',
                'specification'   => 'i.
Antenna type: Dual band outdoor omni-directional antenna
ii.
Frequency bands: 2.4 GHz and 5 GHz
iii. Gain: 9 dBi (5 GHz), 5 dBi (2.4 GHz)
iv. Connector type: N-Female
v.
Mount type: Marine mount included for pole or wall
installation
vi. Weatherproof rating: IP67 – suitable for outdoor and marine
environments
vii. Polarization: Vertical
viii. Voltage Stage Wave Ration (VSWR): 2.0 or less
ix. Impedance:  50 Ohms
10 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
x.
Length: Approx. 400 mm
xi. Application: Wi-Fi extension, mesh networks, outdoor APs,
long-range connectivity
xii. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'ALFA AOA-2458-59-TFL',
                'serial_numbers'  => '25310M100134, 256KOM100336, 256KOM100337, 25310M100149, 25310M100169, 256KOM100309, 25310M100167, 256KOM100342, 256KOM100358, 25310M100148',
                'country_of_origin' => null,
                'unit_price'      => 500.00,
                'total_price'     => 5000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.7.2',
                'category'        => 'Agriculture Lab',
                'name'            => 'Wi-Fi Range Extender',
                'specification'   => 'i.
Dimensions without mount: Approx.170 mm x 208 mm x 66.5
mm
ii.
With mount: Approx. 170 mm x 208 mm x 121.8 mm
iii. Wi-Fi coverage: 465 m² (5,000 ft²) open space
iv. Range extension: 0.5 km with integrated directional super
antenna
v.
Frequency bands: Dual-band 2.4 GHz and 5 GHz
vi. Gain: High-gain directional/omni antennas (software-
switchable)
vii. IP rating: IP67 – weatherproof and dustproof
viii. Mounting: Wall or pole mountable (including brackets)
ix. Max clients: Supports more than 300 connected devices
x.
Power supply: PoE (Power over Ethernet) supported
xi. Use case: Long-range outdoor Wi-Fi deployment, campus
mesh, rural extension
xii. Warranty: at least 3 years
4 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'Ubiquiti Access Point U7 Pro Outdoor',
                'serial_numbers'  => '1COBBOC8781, 1COBBOC7BD2, 1COBBOC87E1, 1COBBOC796B',
                'country_of_origin' => null,
                'unit_price'      => 1500.00,
                'total_price'     => 6000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 4,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.7.3',
                'category'        => 'Agriculture Lab',
                'name'            => 'Robot Long Range Communication Device',
                'specification'   => 'i.
Device type:  Outdoor wireless radio (CPE/Bridge)
ii.
Frequency bands: Dual band (2.4 GHz & 5 GHz)
iii. Max throughput: 300+ Mbps
iv. Weatherproof rating: IP67 – fully sealed for outdoor
environments
v.
Connector type: Direct N-type male connector (integrates
directly with antennas)
vi. Power supply: Passive PoE (Power over Ethernet)
vii. Compatibility: Plug-and-play with airMAX antennas
viii. Use case: Long-range point-to-point or point-to-multipoint
wireless communication
ix. Installation: Tool-less, easy attachment to compatible
antennas
x.
Applications: Drone communication, robotic field comms,
rural networking, IoT mesh backhaul
xi. Warranty: at least 3 years
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.2.8
LABORATORY AND PROTOTYPING EQUIPMENTS FOR
AGRICULTURE',
                'brand'           => 'TP-LINK 5GHz 300Mbps 13dBi Outdoor CPE CPE510; tiada nombor siri',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 4000.00,
                'total_price'     => 3000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.8.1',
                'category'        => 'Agriculture Lab',
                'name'            => 'Hot Air Soldering Station (700 W)',
                'specification'   => 'i.
Input voltage 220V
ii.
Output power soldering 24V / air gun 220V
iii. Temperature range: 100°C to 500°C
iv. Airflow type: Hot air (forced convection)
v.
Usage: Suitable for soldering, desoldering, SMT rework, and
heat shrinking
vi. Display type:  Digital or analog (varies by model)
vii. Control features: Temperature and airflow adjustment knobs
or buttons
viii. Applications:  PCB repair, prototyping, electronics assembly
ix. Include installation of fuming chamber for safety
x.
Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'PRO\'SKIT SN-396 Soldering Helping Hand with LED Magnifier; tiada nombor siri',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 500.00,
                'total_price'     => 0.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.8.2',
                'category'        => 'Agriculture Lab',
                'name'            => 'Hot Air Soldering Equipment (700 W)',
                'specification'   => 'i.
Nozzle compatibility:  Interchangeable nozzles
ii.
Magnifying lens size: Diameter 90 mm & diameter 20 mm
iii. Magnification: 2.5 x (6D) + 6.25 x (21D)
iv. Magnification material: Acrylic
v.
Light source: 16 pieces SMD LEDs
vi. Neck length (for magnifier): 265 mm
vii. Neck length (for alligator clips): 250 mm
viii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'Pro\'sKit SS-989C',
                'serial_numbers'  => '24024041248.0',
                'country_of_origin' => null,
                'unit_price'      => 500.00,
                'total_price'     => 500.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.8.3',
                'category'        => 'Agriculture Lab',
                'name'            => 'Pelican Hard Shell Laptop',
                'specification'   => 'i.
Material: High-impact polypropylene (or equivalent Pelican-
grade polymer)
ii.
Protection rating:  Waterproof, dustproof, and crushproof
iii. Sealing mechanism: O-ring sealed with automatic pressure
equalization valve
iv. Interior: Customizable foam inserts for secure laptop and
accessory fit
v.
Latches: Double-throw latches for secure and easy opening
vi. Carrying options:  Fold-down handle; optional shoulder strap
or wheels (depending on model)
vii. Dimensions: Fits 17" laptops
viii. Weight: Approx. 3 – 5 kg
ix. Use case:  Safe transport and storage of laptops in field and
industrial environments
x.
Warranty: at least 3 years
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'PELICAN 1490 Protector Laptop Case; tiada nombor siri',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1000.00,
                'total_price'     => 5000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.8.4',
                'category'        => 'Agriculture Lab',
                'name'            => 'LiPo Battery',
                'specification'   => 'i.
Cell configuration: 3S (3 cells in series)
ii.
Nominal voltage: 11.1V
iii. Capacity: 6000 mAh
iv. Discharge rate: 60C continuous / 120C burst
v.
Connector type: XT60
vi. Dimensions: Approx. 160 mm × 48 mm × 29 mm
vii. Weight: Approx. 480 gm
viii. Applications: RC vehicles, robotics, drones, and mobile
power systems
ix. Protection features:  Requires proper LiPo-compatible
charger and handling for safety
x.
Warranty: at least 3 years
10 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 500.00,
                'total_price'     => 5000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.8.5',
                'category'        => 'Agriculture Lab',
                'name'            => '3D Printer - Filament Based',
                'specification'   => 'i.
Printer type: High-speed FDM 3D printer
ii.
Print technology: Fused Deposition Modelling (FDM)
iii. Build volume: Approx. 256 mm × 256 mm × 256 mm
iv. Supported materials: PLA, ABS, PETG, TPU, Nylon, Carbon
fiber, Polycarbonate
v.
Extruder type: Direct drive with hardened steel nozzle
vi. Max print speed: Up to 500 mm/s
vii. Max acceleration: 20,000 mm/s²
viii. Bed levelling: Dual auto bed levelling (sensor + lidar-based)
ix. AI features: First layer inspection, spaghetti failure detection
x.
Lidar resolution: 7 μm
xi. Chamber type: Fully enclosed for better temperature control
xii. Connectivity: Wi-Fi, SD card, Bambu studio software, Cloud
sync
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xiii. Accessories: Automatic Material System (AMS) for multi-color
printing
xiv. Use case: Prototyping, engineering parts, multi-material
printing, educational and industrial use
xv. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 25000.00,
                'total_price'     => 25000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.8.6',
                'category'        => 'Agriculture Lab',
                'name'            => 'Ergonomic Lab Chair',
                'specification'   => 'i.
Design:  Adjustable and ergonomic for extended sitting
comfort
ii.
Seat material: High-density foam with breathable mesh or
fabric upholstery
iii. Backrest: Adjustable lumbar support with mesh back.
iv. Armrests: 3D or 4D adjustable armrests (varies by model)
v.
Seat height adjustment: Gas lift mechanism for smooth height
control
vi. Tilt mechanism: Multi-angle reclining with lockable positions
vii. Base: Heavy-duty metal or reinforced nylon base with
smooth-rolling casters
viii. Mobility: Swivel and wheeled for easy movement
xvi. Application: Suitable for workstations, control rooms, and
prolonged sitting environments
xvii. Warranty: at least 3 years
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'SPACE Ergonomic Seat Pro; tiada nombor siri',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 600.00,
                'total_price'     => 1800.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.2.8.7',
                'category'        => 'Agriculture Lab',
                'name'            => 'Steel Filing Cabinet Black',
                'specification'   => 'i.
Custom design for lab requirement
ii.
High quality cold rolling steel
iii. Safety features for lab
iv. Finishing by epoxy powder coating
v.
Dimensions (H x W x D): Approx.185 cm x 85 cm x 39 cm
vi. Warranty: at least 3 years
8 units',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1000.00,
                'total_price'     => 8000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 8,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.2.8.8',
                'category'        => 'Agriculture Lab',
                'name'            => 'Smart Desk - L Shape',
                'specification'   => 'i.
Height-adjustable ergonomic desk with smart reminder
ii.
Custom design for lab purpose: Approx. 1800 mm x 800 mm
iii. Desk thickness: Maximum 25 mm
iv. Warranty: at least 3 years
7 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.3
HEALTHCARE LABORATORY
2.3.1
ICT EQUIPMENT FOR HEALTHCARE LAB',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 5000.00,
                'total_price'     => 35000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 7,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.1.1',
                'category'        => 'Healthcare Lab',
                'name'            => 'Laptop',
                'specification'   => 'The specification shall have at least:
i.
i7-12700H processor
ii.
RTX 4050 Laptop GPU – 6GB GDDR6 VRAM
iii.
16GB of DDR5-5200MHz
iv.
512GB NVMe M.2 SSD
v.
Support Wi-Fi 6e or Wi-Fi 6
vi.
Windows 11
vii. 24 Full HD IPS Monitor with 100 Hz refresh rate
viii. Warranty: at least 3 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
ix.
Includes necessary accessories:
a.
wireless headphones
b.
wireless mouse
c.
wireless keyboard',
                'brand'           => 'HP VICTUS 15-fa2320TX',
                'serial_numbers'  => '5CD5388KFY, 5CD5388KNY',
                'country_of_origin' => null,
                'unit_price'      => 1000.00,
                'total_price'     => 5000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.3.1.2',
                'category'        => 'Healthcare Lab',
                'name'            => 'SMART TV for Lab Demonstration',
                'specification'   => 'i.
42” Smart TV
ii.
Display: 42" Screen with 4K UHD (3840 x 2160) Resolution
iii.
Touch & Interaction: InGlass™ technology with up to 4
touchpoints
iv.
Connectivity: HDMI In, HDMI Out, USB ports, NFC reader
and can Screen sharing via Wi-Fi
v.
Software & Functionality: Built-in digital whiteboard, Web
browsing
vi.
Accessories: TV mounting with tyre, Stylus
vii. Warranty: at least 3 years
6 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'Viewsonic IN6521C-1C; sebahagian 1 unit daripada 6',
                'serial_numbers'  => 'YF32612A301D',
                'country_of_origin' => null,
                'unit_price'      => 17000.00,
                'total_price'     => 102000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 6,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.3.1.3',
                'category'        => 'Healthcare Lab',
                'name'            => '65-inch Smart TV for Entrance Display',
                'specification'   => 'i.
65" Smart TV
ii.
Display: 65" Screen with 4K UHD (3840 x 2160)
Resolution
iii.
Touch & Interaction: InGlass™ technology with up to 4
touchpoints
iv.
Connectivity: HDMI In, HDMI Out, USB ports, NFC reader
and can Screen sharing via Wi-Fi
v.
Software & Functionality: Built-in digital whiteboard, Web
browsing
vi.
Accessories: TV mounting with tyre, Stylus
vii.
Include TV wall embedding installation and wiring
viii.
Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.3.2
ROBOTIC EQUIPMENT FOR HEALTHCARE LAB',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3840.00,
                'total_price'     => 3840.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.2.1',
                'category'        => 'Healthcare Lab',
                'name'            => 'Hand Robot',
                'specification'   => 'i.
Hand Robot is a pioneering pneumatic hand exerciser,
meticulously engineered to transform hand rehabilitation and
training
ii.
Hand Rehabilitation Robotic Includes:
a.
Robot controller: 1 piece
b.
Air massage hand glove: 1 piece
c.
Robot glove (M, L, XL left and right side): 6 pieces
d.
Wireless mirror glove (Left and right): 2 pieces
e.
Hand Robot carry bag: 1 piece
f.
Fast charging charger: 1 piece
g.
Fast charging cable: 1 piece
h.
Massage ball: 1 piece
i.
Adjustable hand splint: 1 piece
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
j.
Wrist support set (Left and right): 1 piece
k.
Glove anti-slip pad: 1 piece
l.
Robot glove single adapter: 1 piece
m. Robot glove dual adapter: 1 piece
iii. User manual
iv. Weight: 5 kg
v.
Space dimension: 50 cm x 50 cm
vi. Hand robotic certification
vii. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 15000.00,
                'total_price'     => 30000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.2.2',
                'category'        => 'Healthcare Lab',
                'name'            => 'Exoskeleton Walk Assist Robot',
                'specification'   => 'i.
Left and right arm exoskeleton robot with maximum 15 Nm
force and weighted less than 5 kg
ii.
Left and right hip assisted exoskeleton robot with maximum
torque of 15 Nm and weighted less than 5 kg
iii.
Detachable rechargeable battery set
iv.
Software application with tablet
v.
Weight: 15 kg
vi.
Space dimension: 1 m x 1 m
vii.
Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 9000.00,
                'total_price'     => 9000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.2.3',
                'category'        => 'Healthcare Lab',
                'name'            => 'Collaboration 6 DOF Robot with VR Set',
                'specification'   => 'i.
Payload: 3 kg
ii.
Reach: 500 mm
iii.
Repeatability: ±0.03 mm
iv.
DOF: 6 rotating joints
v.
Joint range: +/- 360degree
vi.
Force sensing, Tool flange: Force range: 30 N,
Torque range: 10 Nm
vii.
1x Robot controller & Tech pendant
viii.
6 Degrees of Freedom (6 DoF) for precise head and body
ix.
tracking
x.
Including high-end Windows PC
xi.
Application development for sensor data storage
xii.
Robotic gripper with 50 mm stroke, 20 - 130n grip force, 5 kg
payload, closing speed of 20 – 150 mm/s
xiii.
Weight: 15 kg
xiv.
Dimension: 1 m x 1 m
xv.
Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.3.3
SENSORS FOR HEALTHCARE LAB',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 300000.00,
                'total_price'     => 300000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.3.1',
                'category'        => 'Healthcare Lab',
                'name'            => 'Wireless Electromyography Sensor',
                'specification'   => 'i.
Signal Acquisition: High-quality signal acquisition with ease
of use and a compact, lightweight design
ii.
Wireless EMG Sensor with input impedance greater than 100
ohms
iii.
Software for data acquisition
iv.
Sensor receiver with range of up to 30 m
v.
Charging adapter and cable
vi.
Instrument case
vii. User manual
viii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 60000.00,
                'total_price'     => 60000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.3.2',
                'category'        => 'Healthcare Lab',
                'name'            => 'APDM Wearable Moveo Explorer',
                'specification'   => 'i.
System type: Wearable motion analysis system
ii.
Sensor configuration: 6 sensors for full-body motion capture
iii. Assessment software protocols and report for Walk, Sway,
TUG, CTBIS, Sit to Stand, 360 ̊ turn, and database
iv. Kinematic analysis software for joint movement
v.
Docking station with power supply and USB cable
vi. Access point wireless receiver with USB cable
vii. Sensor straps (2 small, 2 large, 1 sternum, 1 lumbar)
viii. Wireless remote
ix. Footplate
x.
Carrying case
xi. Weight: 3 kg
xii. Space dimension: 1 m x 1 m
xiii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.3.4
HEALTHCARE DEVICES AND EQUIPMENTS',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 350000.00,
                'total_price'     => 350000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.1',
                'category'        => 'Healthcare Lab',
                'name'            => 'FIBOD Digital Scale',
                'specification'   => 'i.
Balance board FIBOD 3.0
ii.
Balance plate with motion sensor: 1 piece
iii. Capacity: 200 kg for 1 piece
iv. Rechargeable build-in battery with minimum of 6 hours
capacity: 1 piece
v.
Charging adapter with light indicator and charging power
output: 8.4V, 0.2A: 1 piece
vi. Adjustable difficulties with foam set: 3 pieces
vii. Virtual reality training games modules: 4 pieces
viii. Double leg assessment module: 1 piece
ix. Single leg assessment module: 1 piece
x.
Individual user report modules: 1 piece
xi. Unlimited user profile modules: 1 piece
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xii. Processing controller with remote control set: 1 piece
xiii. Advance supporting stand: 1 piece
xiv. Detachable harness support: 1 piece
xv. Weight: 30 kg
xvi. Space requirement: 1.0 m x 1.5 m
xvii. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 20000.00,
                'total_price'     => 20000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.10',
                'category'        => 'Healthcare Lab',
                'name'            => 'Vital Sign Measurements Systems (no touch)',
                'specification'   => 'i.
Contactless vital sign system
ii.
Contactless vital sign software that can detect heart rate,
breathing rate, blood pressure, heart rate variability, mental
stress, skin age and general wellness score
iii. Tablet with standing kiosk:
a.
Tablet enclosure: Adjustable to fit various tablet sizes
(commonly supports 9.7”–12.9” tablets such as iPad
Air/Pro).
b.
Height: Approx. 1000-1200 mm (suitable for standing
users).
c.
Base: Weighted for stability, diameter approx. 300-400
mm
iv. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 5000.00,
                'total_price'     => 5000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.11',
                'category'        => 'Healthcare Lab',
                'name'            => 'Dumbbell Set with Bench',
                'specification'   => 'i.
Dumbbell set with seat
ii.
Adjustable gym seat with adjustable inclination
iii. Neoprene dumbbell set with rack
a.
0.45 kg
b.
1 kg
c.
1.5 kg
d.
2 kg
e.
2.5 kg
f.
3 kg
g.
3.5 kg
h.
4 kg
i.
5 kg
j.
6 kg
k.
7 kg
iv. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 6000.00,
                'total_price'     => 6000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.12',
                'category'        => 'Healthcare Lab',
                'name'            => 'Electroencephalography (EEG) Biofeedback Headset',
                'specification'   => 'i.
EEG headset with signal processing algorithm
ii.
Certified with Federal Communications Commission (FCC),
Conformité Européenne (CE), and State Radio Regulatory
Commission of China (SRRC). UL certified rechargeable
lithium battery,
iii. Weight: 0.5 kg
iv. Dimension: 0.5 m x 0.5 m
v.
Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 7000.00,
                'total_price'     => 7000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.13',
                'category'        => 'Healthcare Lab',
                'name'            => 'Running Machine',
                'specification'   => 'i.
Running area: 50" × 20"
ii.
Variable response cushioning system
iii. Variable speed of 0.8 − 16 km/h / 0.5 − 10 mph
iv. Energy saver mode, tablet/reading rack
v.
Driving system of 2.0 HP
vi. Time, distance, calories, speed, heart rate display
vii. Distance, calories, steps, custom, intervals program
viii. Compatible with popular fitness apps
ix. Bluetooth 4.0 FTMS
x.
Contact grips and Bluetooth HR
xi. Product dimension: 159 m x 82 m x 130 m
xii. Product weight: 50.8 kg / 112 lbs
xiii. Max user weight:  113 kg / 250 lbs
xiv. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 5000.00,
                'total_price'     => 5000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.14',
                'category'        => 'Healthcare Lab',
                'name'            => 'Optical Heart Rate Monitor Monitoring (OHR)',
                'specification'   => 'Sensor type: Optical heart rate
i.
Wearing options:
a.
Armband (Upper arm or forearm)
b.
Swimming goggles strap
c.
Under tight clothing (Chest Area)
ii.
Connectivity:  Bluetooth (BLE) with minimum or higher
broadcast range: 15000 cm
iii. ANT+ – Compatible with fitness apps, smartwatches, and
bike computers
iv. Internal memory 16 MB – Stores workout data when not
connected
v.
Heart rate monitoring and accuracy
vi. Heart rate range: 30 – 240 bpm
vii. Measurement technology: 6-LED optical HR sensor
viii. Sampling rate: 1 Hz (one measurement per second)
ix. Accuracy: Comparable to chest strap HR monitors
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
x.
Modes: Live HR mode (real-time heart rate transmission)
xi. Recording mode (stores data for later transfer)
xii. Swimming mode (designed for underwater HR tracking)
xiii. Battery and charging
a. Battery type: Rechargeable lithium-polymer
b. Battery life: Up to 30 hours per full charge
c. Charging time: 1.5 hours
d. Charging interface: Magnetic USB adapter
xiv. Memory and data storage:
a. Built-in memory: Up to 600 hours of HR data
b. Data transfer: Via polar flow app (Bluetooth sync)
xiiv. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1500.00,
                'total_price'     => 1500.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.15',
                'category'        => 'Healthcare Lab',
                'name'            => 'Camera Thermal System',
                'specification'   => 'i.
Thermal camera able to detect temperature
ii.
Software that able to detect multiple people body
temperature at the same time
iii.
Abnormality alarm detection if over temperature
iv.
Minimum Infrared part:
a. Resolution: 256×192
b. Pixel size: 12 μm
c. NETD: ≤50 mK
d. Frame rate: 25 Hz
e. Focal length: 3.2 mm
f. Field angle: 56° × 42°
v.
Visible part:
a. Resolution: 1280×720
b. Field angle: FOV 80°
c. Focal length: 4.4 mm
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
vi. Accuracy of temperature measurement
a. Measuring range: 30~45℃
b. Accuracy: ±0.5℃ or ±0.9℉ (Environment temperature
10℃~40℃)
c. Measurement distance: ≤4 m (10–15 feet)
vii. Machine interface and power source:
a. HDMI
b. Power input: AC100–240V, 50/60 Hz, 0.5 A
c. Power output: DC5V 3 A
viii. Software:
a. Support history inquiry, screening and export to local disk
Live preview
b. Live preview of visible and infrared image
c. Intelligent face recognition and tracking
ix. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 10000.00,
                'total_price'     => 10000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.16',
                'category'        => 'Healthcare Lab',
                'name'            => 'Portable Ultrasound System',
                'specification'   => 'i.
Device type: Portable ultrasonic imaging system
ii.
Imaging technology: High-resolution ultrasound imaging
iii. Display: 15-inch LED monitor for clear visualization
iv. Frequency range: 1 MHz to 15 MHz (adjustable based on
application)
v.
Applications: Emergency medicine, musculoskeletal imaging,
anesthesia, and clinical examinations
vi. Portability: Compact and lightweight, ideal for point-of-care
use
vii. Probe type: Multiple probes (linear, convex, or phased array
depending on model)
viii. Imaging modes: B-mode, M-mode, Color doppler (varies by
model)
ix. Battery life: 4–6 hours of continuous operation (depending on
usage)
x.
Power supply: Rechargeable battery or AC power supply
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xi. Minimum Connectivity: USB Type - A, HDMI, and Wi-Fi 6 for
data transfer and image sharing
xii. Operating Temperature: 0°C to 40°C
xiii. Storage Temperature: -20°C to 60°C
xiv. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 100000.00,
                'total_price'     => 100000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.17',
                'category'        => 'Healthcare Lab',
                'name'            => 'CCTV',
                'specification'   => 'i.
Minimum 1080p (2MP), ideally 2K (4MP) or 4K (8MP)
ii.
Must support PoE (Power over Ethernet), standard IEEE
802.3af/at
iii.
Built-in microSD card slot, supports 128GB or more
iv.
Infrared (IR) LEDs, min. 10 meters range, ideally full-color
night vision
v.
Bullet or Dome type, vandal-proof casing if public areas
vi.
Motion detection, person/vehicle detection, alert
notifications
vii.
Accessible via mobile app and web browser
viii.
Use High Endurance SD Cards, e.g., SanDisk High
Endurance or Samsung PRO Endurance with 256GB
storage
ix.
Warranty: at least 3 years
x.
Installation and cabling included
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.3.5
HEALTHCARE LAB INSTALLATION',
                'brand'           => 'Dahua IPC-HDBW2449E-S-IL; item Healthcare',
                'serial_numbers'  => 'BH0B7E1PAGCB3F5, BH0B7E1PAG1C64E',
                'country_of_origin' => null,
                'unit_price'      => 1000.00,
                'total_price'     => 2000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.3.4.2',
                'category'        => 'Healthcare Lab',
                'name'            => 'Driving Simulator',
                'specification'   => 'i.
Version 3.0 or latest
ii.
Metal-based institutional driving simulator comes with
realistic vehicle steering, accelerator, auto gear level, hand
brake (left-sided), light, emergency signal, horn, signal,
wiper, key activation: 1 piece
iii. DC motor-controlled steering wheel with dynamic force haptic
feedback
iv. Actual adjustable cushion car seat with safety belt: 1 piece
v.
Auto gear transmission: 1 piece
vi. Manual gear transmission: 1 piece
vii. Simulator adaptation set including hand drive control, wiper
extension, pedal extension, accelerator extension, single-
point driving knob and 3-points driving knob: 1 piece
viii. Driving simulation software license include 30+ driving
courses scenario, vehicle type option, and reaction time
assessment
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
ix. Driving simulator main processing controller: 1 piece
x.
27 inches driving simulator monitor screen
xi. User manual
xii. Weight: 120 kg
xiii. Space dimension (L x W x H): 180 cm x 120 cm x 135 cm
xiv. Continuous usage frequency: 8-10 hours
xv. Research software development kits
xvi. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 100000.00,
                'total_price'     => 100000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.3',
                'category'        => 'Healthcare Lab',
                'name'            => 'EYAS Digital Cognitive Training System',
                'specification'   => 'i.
Digital cognitive training system
ii.
100 training games content
iii. 24 inches industrial-grade touchscreen monitor
iv. EYAS cognitive PC training software
v.
DCOG training software
vi. Main controller
vii. PC controller
viii. Eye tracker
ix. Weight: 30 kg
x.
Space dimension: 1.5 m x 1.5 m
xi. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 100000.00,
                'total_price'     => 100000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.4',
                'category'        => 'Healthcare Lab',
                'name'            => 'Portable Stadiometer',
                'specification'   => 'i.
Portable height rod
ii.
Measuring range: 14 cm to 205 cm
iii. Incline: 5.5 inch to 80.7 inch
iv. Graduation: 1 mm (1/16 in)
v.
Dimensions: Overall (W x L x H): 350 mm x 400 mm x 2430
mm
vi. Weight: 1.9 kg (carry case 0.4 kg)
vii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'CHARDER Portable Height Rod / Stadiometer HM200P; tiada nombor siri',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 600.00,
                'total_price'     => 0.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-05-14',
                'do_no'           => '13052026-01',
            ],
            [
                'item_no'         => '2.3.4.5',
                'category'        => 'Healthcare Lab',
                'name'            => 'Digital Hand Dynamometer',
                'specification'   => 'i.
A digital hand dynamometer is a precision instrument
designed to measure grip strength, providing key insights into
muscle function and overall health. It is widely used in
medical research, rehabilitation, sports science, and AI-
driven health analysis.
ii.
Maximum weight capacity: 90 kg
iii. Resolution: 0.1 kg
iv. Adjustable gripping sizes
v.
Weight: 200 gm
vi. Space dimension: 20 cm x 20 cm
vii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 200.00,
                'total_price'     => 0.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.6',
                'category'        => 'Healthcare Lab',
                'name'            => 'Digital Muscle Meter with Platform',
                'specification'   => 'i.
Pull and push direction
ii.
Maximum capacity: 300 kg
iii. Tester platform
iv. Weight: 300 gm
v.
Space dimension: 50 cm x 50 cm
vi. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 5000.00,
                'total_price'     => 5000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.7',
                'category'        => 'Healthcare Lab',
                'name'            => 'Blood Pressure and Electrocardiogram (ECG) Monitor',
                'specification'   => 'i.
Monitor: Measurement type: Upper-arm or wrist cuff blood
pressure measurement
ii.
Measurement range:
a.
Systolic pressure: 60 mmHg to 260 mmHg
b.
Diastolic pressure: 40 mmHg to 215 mmHg
c.
Pulse rate: 40 beats per minute (bpm) to 180 beats per
minute (bpm)
iii. Accuracy:
a.
Pressure: ±3 mmHg
b.
Pulse Rate: ±5% of the reading
iv. Cuff type & size: Standard: 22 cm to 42 cm (upper arm)
v. Wrist model: 13.5 cm to 21.5 cm
vi. Display type: LCD digital display for easy reading
vii. Power supply: Battery-operated (AA batteries) or AC adapter
viii. Memory storage: Stores up to 100 to 200 readings per user
(varies by model)
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
ix.
Irregular heartbeat detection: Detects arrhythmia or
abnormal pulse patterns
x.
Hypertension Indicator: Alerts if BP readings are above
normal thresholds
xi.
Bluetooth & app connectivity: Syncs with OMRON connect
app for mobile tracking
xii.
Compatible with iOS & Android devices
xiii. OMRON ECG (Electrocardiogram)
xiv. Device: Measurement type: Single-lead or 6-lead ECG
(varies by model)
xv.
Lead Type:
a.
Single-lead ECG: Provides basic heart rhythm analysis
b.
6-lead ECG: Offers more detailed heart electrical
activity monitoring
xvi. Heart rate: 30 – 250 bpm
xvii. ECG amplitude: ±2 mV
xviii. Frequency response: 0.05 – 40 Hz
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
k',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3000.00,
                'total_price'     => 3000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.8',
                'category'        => 'Healthcare Lab',
                'name'            => 'Body Composition Analyzer',
                'specification'   => 'i.
Body composition
ii.
Provide measurements body weight, body water, obesity
analysis
iii. Muscle-fat analysis, percent, body fat, Skeletal Muscle Index
(SMI), and Basal Metabolic Rate (BMR)
iv. Report printing capability
v.
USB Thumb Drive - Copy, backup or restore
vi. Weight: 13.4 kg
vii. Dimension (W x L x H): 356 mm x 796 mm x 995 mm
viii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 40000.00,
                'total_price'     => 40000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.4.9',
                'category'        => 'Healthcare Lab',
                'name'            => 'Treatment Bed',
                'specification'   => 'i.
Adjustable height: 480 - 920 mm
ii.
Adjustable back: 90 to -33 degree
iii. Adjustable foot: 41 to -24°
iv. Electrical adjustable height with foot
v.
Weight: 100 kg
vi. Dimension (L x W x H): 1980 mm x 700 mm x (range 480
mm to 920 mm)
vii. Oil, water- and fire-resistant leather cover
viii. Four movable castor wheels
ix. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 13000.00,
                'total_price'     => 13000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.3.5.1',
                'category'        => 'Healthcare Lab',
                'name'            => 'Interior, Drawing and Installation for Healthcare Lab',
                'specification'   => 'i.
Layout and design planning
ii.
Installation work - refer to attached file
iii.
Wiring work
iv.
Lighting
v.
Diffuse light panels for uniform illumination.
vi.
Separation between work areas using transparent walls for
observation without contamination.
vii. White, reflective surfaces for a sterile and clean appearance.
viii. High-speed data networks and IoT integration for lab
automation.
ix.
Ambient blue LED lighting accents for a high-tech look and
possibly to maintain circadian rhythm or specific lighting
conditions.
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
x.
Seamless workbenches and storage, likely made from
chemical-resistant materials (e.g., epoxy resin countertops,
stainless steel).
xi.
Defect Period: At least 12 months
xii. Fitted white furniture with LED lightings (Custom fitted
storage rack, custom display rack, healthcare themed 4
matching chairs and 2 tables)
xiii. Door with locking security system
xiv. CCTV security smart camera with app control
xv. Minimum specification for laptop:
a.
Processor: Intel® Core™ i5-13420H
b.
Memory: 16GB DDR5 RAM
c.
Storage: 512GB PCIe NVMe Gen4 SSD
d.
Display: 15.6" Full HD (1920 x 1080), 16:9 IPS, 144Hz
e.
Operating System: Windows 11 Home or latest
xvi. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual',
                'brand'           => '| Harga dipadankan secara fuzzy: INTERIOR RENOVATION FOR HEALTHCARE LAB (HEALTHCAR 359031',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3000.00,
                'total_price'     => 3000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.1.1',
                'category'        => 'Field Robotics Lab',
                'name'            => 'High Performance Laptop for Robot Workstation',
                'specification'   => 'i.
Processor: Core i9-13900HX
ii.
Graphics: Minimum GeForce RTX 4080
iii. Display: 16" WQXGA (2560×1600), IPS, should be at least
165Hz
iv. Memory: Should be more than 32GB DDR5 RAM (5600MHz),
dual channel supported
v.
Storage: Minimum 1 TB, M.2, PCIe Gen4 TLC
vi. Operating System: Windows 11 Pro or higher
vii. Keyboard: Backlit keyboard
viii. Cooling system: Dual fans and vapor chamber
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
ix. Ports: USB-C, USB-A, HDMI 2.1, RJ-45, Audio Jack,
Charging Port
x.
Connectivity: Wi-Fi 6E, Bluetooth 5.3
xi. Battery: 80Wh or higher, rapid charge support
xii. Weight: Approximate 2.5 – 2.7 kg
xiii. Accessories: Wireless 2.4G and Bluetooth Mouse, SSD M.2
compatible enclosure with minimum 500GB memory M.2
SSD with read and write speed minimum 5200MBps,
Wireless 2.4G and Bluetooth Headset,15.6” Laptop
Backpack, Leather Laptop Harness
xiv. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'DELL PC16250; Smart Manufacturing, 11 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 6000.00,
                'total_price'     => 12000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.4.1.2',
                'category'        => 'Field Robotics Lab',
                'name'            => 'High End PC for Robot Workstation',
                'specification'   => 'i.
i9 processor 14900K Unlocked for OC 32GB RAM
ii.
PROO B760M-A Wi-Fi DDR5 LGA1700 Motherboard
iii. Minimum of RTX 4060TI 8GB GDDR6
iv. M.2 NVMe SSD 500GB SSD or higher with read and write
speed of 5200MBps or higher
v.
M.2 NVMe SSD 2TB SSD or higher with read and write
speed of 5200MBps or higher
vi. Microsoft Windows 11 Pro or higher
vii. Accessories: Minimum 27” 2K resolution LCD Display Monitor
with connectivity of HDMI and DP, Wireless 2.4G and
Bluetooth Mouse, SSD M.2 compatible enclosure with
minimum 500GB memory M.2 SSD with read and write speed
minimum 5200MBps, Wireless 2.4G and Bluetooth
Headset,15.6” Laptop Backpack
viii. Warranty: at least 3 years
3 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.4.2
ROBOTIC EQUIPMENTS FOR FIELD ROBOTICS LAB',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 20000.00,
                'total_price'     => 60000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 3,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.2.1',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Legged Robot',
                'specification'   => 'i.
Robot Info: 1 piece
a. Standing dimensions: 70 cm × 43 cm × 50 cm
b. Weight (with battery): about 18 kg
c. Movement speed: 0~2.5 m/s
d. Tire: 7-inch pneumatic tire
e. Maximum climbing height: about 70 cm
f. Joint motors: 16
g. Battery type: long battery life (15,000 mAh), battery life 2-
4h
h. An expansion dock (Orin NX) with 100 TOPS
i. Equipped with RealSense D435i depth camera
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
ii.   OEM Batteries - Long Range: 2 pieces
a. Product size: 120 mm × 80 mm × 182 mm
b. Rated voltage: DC 28.8V
c. Charging limiting voltage: DC 33.6V
d. Charging current: 9A
e. Rated capacity: 15000 mAh, 432 W/h
f. Standard: IS 16046 (Part 2)/IEC 62133-2
g. Self-developed Battery Management System (BMS)
h. Functions: power display, battery storage self-discharge
protection, balance charging protection, overcharge
protection, overcharge protection, over-discharge
protection, short-circuit protection, battery load detection
protection
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
iii.     Robot Control Tablet and Setup Services: 1 piece
a. 8GB RAM
b. 128GB Storage
c. WLAN Wi-Fi 802.11 a/b/g/n/a/6e, dual-band, Wi-Fi Direct
d. Chipset Snapdragon 8+ Gen 1 (4 nm)
e. Size 11.0 inches
f. Android/Apple play store
g. Blueto',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 450000.00,
                'total_price'     => 450000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.2.2',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Mini Legged Robot',
                'specification'   => 'i.
Mini quadruped robot Dog 4-Legged intelligent programming
ii.
Total weight: Approx. 720 gm
iii. Dimension (H x W x L): 190 mm x 149 mm x 226 mm
iv. Body material: Hard aluminium alloy
v.
Camera resolution: 480 pixels
vi. Degrees of freedom: 8 degrees of freedom
vii. Power supply: 7.4V 2200 mAh lithium battery
viii. Control board: Raspberry Pi 4B (4G)
ix. Servo parameters: HPS-0618SG
x.
Controller: PS2 wireless
xi. Warranty: at least 1 year
10 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 10000.00,
                'total_price'     => 100000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.2.3',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Mobile Robot (Rugged) – RHINO',
                'specification'   => 'i.
Total weight: 100 kg
ii.
Maximum payload: 150 kg
iii. Dimension (H x W x L): 1300 mm x 800 mm x 1000 mm
iv. Power supply: DC 48V
v.
Battery life: 5 hours
vi. Maximum speed: 0.5 m/s
vii. Camera: Front-facing HD camera and PTZ Camera
viii. Wheel drive: Two independent motor
ix. Climbing ability: <30°
x.
Communication: Standard CAN 2.0
xi. Sensor: LiDAR integration
xii. Control box: Screen 13-inch LCD display and digital joystick
xiii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 250000.00,
                'total_price'     => 250000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.2.4',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Mini Mobile Robot for Training',
                'specification'   => 'i.
Mini outdoor 4-Wheel drive autonomous vehicle unmanned
ground vehicle
ii.
Total weight: ±13.3 kg
iii. Loading capacity: 20 kg
iv. Climbing capacity: 35°
v.
Dimension (H x W x L): 189 mm x 378 mm x 484 mm
vi. Body material: Metal
vii. Battery capacity: 24V/10AH 7H working time
viii. Speed: Max 2.0 m/s
ix. Controller: Aircraft remote control
x.
Wheel: Tyre type
xi. Warranty: at least 3 years
10 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 10000.00,
                'total_price'     => 100000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.2.5',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Underwater Robot',
                'specification'   => 'i.
Industrial-grade underwater ROV
ii.
Size: 608 mm x 294 mm x 196 mm
iii.
Weight: ±8 kg range
iv.
Maximum depth: 200 m
v.
Maximum battery life: more than 4h
vi.
Robot battery: 302.4Wh or higher
vii.
Robot charging time: 2.5H
viii. Operating temperature: -10°C - 45°C
ix.
CMOS: 1/2.3 inch
x.
Aperture: f/2.8
xi.
Focal length: 0.3m
xii.
ISO range: 100-6400
xiii. Field of View: 150°
xiv. Minimum image resolution: 12 MP
xv.
Video minimum stream: Approx. 60 MBps
xvi. IMU sensor
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xvii. Depth sensor: <±0.25m
xviii. Temperature sensor: <±0.2°C
xix. Brightness LED light: 2 x 4000lm
xx.
Color temperature: 5000K - 5500K
xxi. CRI: 85
xxii. Dimming: Three adjustable settings
xxiii. E-Reel:
a.
Size: 296 mm x 189 mm x 279 mm
b.
Weight: 3.5 kg
c.
Battery capacity: 4800 mAh
d.
Number of cycles: >300 times
e.
Runtime: 30 times
f.
Wiping speed: 3 gears
g.
Take-up speed: Fast speed 220s
xxiv. Shorebase power supply system: 20 meters
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xxv.
Remote controller:
a.
Size: 160 mm x 155 mm x 125 mm
b.
Weight: 685 gm
c.
Battery capacity: 2500 mAh or higher
d.
Charging time: 2H
e.
Remote controller with Wi-Fi, HDMI and USB support
xxvi. Sealing cover
xxvii. Thruster shield
xxviii',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 100000.00,
                'total_price'     => 100000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.2.6',
                'category'        => 'Field Robotics Lab',
                'name'            => 'TurtleBot 4 Standard',
                'specification'   => 'i.
Dimensions (H x W x L): Approx. 341 mm x 339 mm x 351
mm
ii.
Operating system: Ubuntu 20.04 with ROS 2
iii.
Onboard computer: Raspberry Pi 4B (4 GB RAM)
iv.
Weight: Approx. 4 kg
v.
Wheel diameter: 72 mm
vi.
Ground clearance: Approx. 4.5 mm
vii.
Maximum speed:
a. Safe: 0.31 m/s
b. Normal: 0.46 m/s
viii.
Maximum payload: 9 kg
ix.
Operating time: 2.5 – 4 hours
x.
Charging time: Approx. 2.5 hours
xi.
Docking station: Included
xii.
LiDAR: RPLIDAR A1M8
360° scanning, 0.15–12 m range, Approx. 1° resolution
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xiii.
Depth Camera: Luxonis OAK D Pro (IR projector + depth)
xiv.
Internal Sensors:
a. IMU
b. Optical floor tracker
c. Wheel encoders
d. Infrared cliff/edge detectors
e. Bump sensors
f. Slip sensors
xv.
OLED screen (128 mm × 64 mm)
xvi.
Battery: 14.4 V Li-ion, 26 W/h nominal
xvii.
Warranty: at least 1 year
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.4.3
SENSORS AND BOARD FOR FIELD ROBOTICS LAB',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 12700.00,
                'total_price'     => 63500.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.1',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Intelligent System TOF 3D LiDAR',
                'specification'   => 'i.
Distance Range: 130 m @ 70% intensity or 60 m @ 10%
intensity
ii.
Horizontal Field of view (FOV): 360°
iii. Vertical Field of view (FOV): -55°~ 14.9°
iv. Scan rate: 10 Hz
v.
Accuracy: +/-3 cm
vi. Pulse repetition rate: 600,000 points/s
vii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 10000.00,
                'total_price'     => 10000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.10',
                'category'        => 'Field Robotics Lab',
                'name'            => 'AI Developer Kit 40 TOPS 8GB Module Robot Edging',
                'specification'   => 'Compute AI Development Board - 4GB External Storage
i.
GPU: AmpereTM architecture with - 1024 × CUDA cores,
32 × Tensor cores
ii.
CPU: 6-core Arm Cortex -A78AE v8.2 64-bit CPU 1.5 MB
L2 + 4 MB L3
iii.
Memory: 8GB, 128-bit, LPDDR5, 102GB/s
iv.
Storage: External through microSD slot, External NVMe
SSD through M.2 Key-M, 4GB
v.
Power: 7W to 25W
vi.
Camera: 2 × MIPI CSI-2 22-pin Camera Connectors
vii.
M.2 Key-M: × 4 PCIe Gen3, 1 × 2 PCIe Gen3
viii.
M.2 Key-E: PCIe (× 1), USB 2.0, UART, I2S, and I2C
ix.
Pre-installed with Wireless Card, IEEE 802.11ac and
Bluetooth 5.0
x.
USB: 4 × Type A USB 3.2 Gen2, 1 × Type C (for Debug
and Device Mode only, not for power)
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xi.
Networking: 1 × Gigabit Ethernet RJ45 Connector
xii.
Multimedia Output: Display Port 1.2 (+MST)
xiii.
microSD slot: UHS-1 cards up to SDR104 mode
xiv. Others: 40-pin Expansion Header (UART, SPI, I2S, I2C,
GPIO)
xv.
12-pin button header, 4-pin fan header, DC power jack,
Compatible with Jetson Orin NX module
xvi. Dimensions: 100 mm × 79 mm × 21 mm (Height includes
feet, carrier board, module, and thermal solution)
xvii. Warranty: at least 1 year
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 2700.00,
                'total_price'     => 13500.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.11',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Minicomputer Pro Kit',
                'specification'   => 'i.
12TH Gen i5-1240P Processor and Graphic: Built with 12th
Generation i5-1240P, Iris xe Graphics, 12 Processor Cores
(4P+8E), 16 threads, 12MB Smart Cache, 35W TDP, P-
Cores: 4.4GHz Turbo; E-Cores: 3.3GHz Turbo.
ii.
Thunderbolt Technology & Other Feature : 2×  Thunderbolt
4 ports (incl. DP 1.4a and USB 4), 2× front and 1× rear USB
3.2 Gen 2 type A ports，1× rear type A and 2× internal
USB 2.0 headers, 2× HDMI 2.1 TMDS Compatible
(4K@60Hz), with built-in CEC per port, 3.5mm front stereo
headset jack, Up to 7.1 multichannel (or 8-channel) digital
audio on HDMI and DP type C ports
iii.
Connect up to one 8K or four 4K displays and Wi-Fi : i225 -
v 10/100/1000/2500 Mbps RJ45 Ethernet | Discrete TPM
2.0, Qualified for 24/7 operation. Wi-Fi 6E A× 211 (Gig+) on
M.2 slot, Bluetooth 5.2
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
iv.
Memory & Storage: Dual-channel DDR4-3200 SODIMMs,
1.2V, 64GB max. M.2 22× 80 key M slot for PCIe × 4 Gen 4
NVMe SSD, M.2 22× 42 key B slot for PCIe × 1 Gen 3, USB
3.2 Gen 2 and SATA SSD expandability
v.
Warranty: at least 1 year
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3700.00,
                'total_price'     => 18500.00,
                'sodo_bayaran'    => 'B36201',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.12',
                'category'        => 'Field Robotics Lab',
                'name'            => 'High-speed Spectral Scanning Camera',
                'specification'   => 'i.
Spectroscopic method: Transmission grating
ii.
Spectral range: 900–1700 nm
iii.
Spectral band: 1024
iv.
Spectral resolution (FWHM): 6.5 nm
v.
Slit width: 25 µm
vi.
Transmission efficiency: >60%
vii.
Stray light: <0.5%
viii. Number of spatial pixels: 1280
ix.
Pixel size: 5 µm
x.
Imaging speed: Full band 70 Hz, maximum 1800 Hz
xi.
Probe: InGaAs (Indium gallium arsenide)
xii.
Signal-to-Noise Ratio: 600:1
xiii. Warranty: at least 1 year
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.4.4
COMMUNICATION FOR FIELD ROBOTICS LAB',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 379000.00,
                'total_price'     => 379000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.2',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Ultra-wide View LiDAR Sensor',
                'specification'   => 'i.
Ordering Part Number OS0-071-64U-AX -BR (Not Physical P/N)
ii.
Wavelength 865 nm
iii.
Laser class 1 eye-safe per IEC/EN 60825-1: 2014
iv.
No of beams/channels: 64
v.
With dual return mode and embedded IMU
vi.
Vertical FOV: +45° to -45°
vii. Horizontal FOV: 360°
viii. Protection level: IP68 & IP69K
ix.
Dimensions:
a. Diameter: 87 mm
b. Height without cap: 58.35 mm
x.
Weight: Approx. 500 gm
xi.
Included: 910-105297-03 straight-angle connector
xii. 3m ss-ibox -kit Cable (USD400)
xiii. Included: 12/24V AC/DC power supply and ethernet cable
xiv. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 2000.00,
                'total_price'     => 2000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.3',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Mid-range LiDAR Sensor',
                'specification'   => 'i.
High performance 3D LiDAR
ii.
OS1-071-64U-AX -BR
iii.
Wavelength: 865 nm
iv.
Laser class 1 eye-safe per IEC/EN 60825-1: 2014
v.
No of beams/channels: 64
vi.
With dual return mode and embedded IMU
vii.
Vertical FOV: +22.5° to -22.5°
viii.
Horizontal FOV: 360°
ix.
Protection level: IP68 & IP69K
x.
Dimensions:
a.
Diameter: 87 mm
b.
Height with Cap: 74.50 mm
xi.
Weight: Approx. 500 gm
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xii.
Included: 910-105297-03 straight-angle connector
xiii.
3m_ss_ibox _kit Cable (USD400)
xiv. Included: 12/24V AC/DC power supply and ethernet cable
xv.
Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 7700.00,
                'total_price'     => 7700.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.4',
                'category'        => 'Field Robotics Lab',
                'name'            => 'A2M12 LiDAR 360 Degree A2 Range Scanner',
                'specification'   => 'i.
Measuring range: 0.2 m -12 m
ii.
Triangular ranging 12 m Scanner
iii.
Sampling frequency: 16 KHz
iv.
Rotational speed: 10 Hz (5 Hz -15 Hz)
v.
Angular resolution: 0.225°
vi.
Scan field flatness: ±1.5°
vii.
Proof level: Class 1
viii. System voltage: 5V
ix.
System current: 450 mA – 600 mA
x.
Power consumption: 2.25W-3W
xi.
Output: UART serial (256000bps)
xii.
Temperature range: 0℃-40℃
xiii. Angular range: 360°
10 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xiv. Range resolution: ≤1% of the range(≤12m) ≤2% of the
range (12m ～ 16m)
xv.
Accuracy: 1% of the range (≤3 m) 2% of the range (3-5 m)
2.5% of the range (5-25m) kit sensor measurement tool
xvi. Warranty: at least 1 year
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 1700.00,
                'total_price'     => 17000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 10,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.5',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Indoor Depth-camera',
                'specification'   => 'i.
Sensor type: Stereo depth camera with RGB and infrared
sensors
ii.
Depth range: 0.1 m to 10 m (ideal for indoor
environments)
iii.
Field of View (FOV):
a.
Horizontal: 87.2°
b.
Vertical: 58°
c.
Diagonal: 94-102°
iv.
Resolution:
a.
Depth: 1280 × 720 pixels (30 FPS)
b.
RGB: 1920 × 1080 pixels (30 FPS)
c.
Infrared: 1280 × 720 pixels (60 FPS)
v.
Frame rate: Up to 90 FPS (for depth and infrared)
vi.
Camera interface: USB 3.0 Type-C
vii.
Depth technology: Active stereo with a depth sensor and IR
projector
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
viii. Sensors: RGB camera, depth sensor, 2 infrared sensors,
IMU (6DoF)
ix.
IMU (Inertial Measurement Unit): Yes, for motion tracking
and spatial orientation
x.
Operating Temperature: 0°C to 40°C
xi.
Power supply: 5V DC via USB connection (USB 3.0)
xii.
Weight: Approx. 200 gm
xiii. Application: Robotics, drones, 3D scanning, augmented
reality, gesture control
xiv. Warranty: at least 1 year
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3700.00,
                'total_price'     => 18500.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.6',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Outdoor Depth-camera',
                'specification'   => 'i.
Field of View (FOV):
a.
Horizontal: 87.2°
b.
Vertical: 58°
c.
Diagonal: 102°
ii.
Resolution:
a.
Depth: 1280 × 720 pixels (30 FPS)
b.
RGB: 1280 × 800 pixels (60 FPS, global shutter)
c.
Infrared: 1280 × 720 pixels (60 FPS)
iii. Frame Rate: Up to 90 FPS (for depth and infrared)
iv. Camera Interface: GMSL2 via FAKRA connector (USB Type-
C for debugging only)
v.
Depth technology: Active stereo with depth sensor and
infrared projector
vi. Sensors: RGB camera (global shutter), depth sensor, 2
infrared sensors, IMU (6DoF)
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
vii. IMU (Inertial Measurement Unit): Yes, for motion tracking
and spatial orientation
viii. Operating temperature: –20°C to 55°C
ix. Power Supply: 12V DC via GMSL2 interface
x.
Weight: Approx. 150 gm (IP65-rated rugged enclosure)
xi. Application: Robotics, industrial applications, autonomous
vehicles, 3D mapping, outdoor vision system
xii. Warranty: at least 1 year
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3700.00,
                'total_price'     => 18500.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.7',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Nano C100 AI Developer Kit Module',
                'specification'   => 'i.
GPU 128-core Maxwell
ii. CPU Quad-core ARM A57 @ 1.43 GHz
iii. Memory 4 GB 64-bit LPDDR4 25.6 GB/s
iv. Storage 16 GB eMMC and microSD
v. Video Encoder:
a.
4K @ 30fps
b.
4× 1080 pixels @ 30fps|
c.
9× 720 pixels @ 30fps (H.264/H.265)
vi. Video Decoder:
a.
4K @ 60fps
b.
2× 4K @ 30fps
c.
8× 1080 pixels @ 30fps
d.
18× 720 pixels @ 30fps
vii. (H.264/H.265)
viii. Camera 2× MIPI CSI-2 DPHY lanes
ix. Connectivity Gigabit Ethernet, M.2 Key E
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
x. Display HDMI 2.0 and eDP 1.4
xi. USB:
a.
USB 3.0
b.
USB 2.0 Micro-B
c.
Others GPIO, IC2, I 2 S, SPI, UART
xii. Dimensions (H x W x L): 100 m × 80 m × 29 m
xiii. Warranty: at least 1 year
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 8500.00,
                'total_price'     => 0.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.8',
                'category'        => 'Field Robotics Lab',
                'name'            => 'AI Developer Kit Super Version with 117/157 TOPS',
                'specification'   => 'i.
1024-core ampere architecture GPU with 32 tensor cores
ii. GPU frequency 1173MHz (Max)
iii. CPU 6-core Arm® Cortex ®-A78AE v8.2 64-bit CPU, 2MB L2
+ 4MB L3
iv. CPU frequency 2GHz (Max)
v. DL accelerator 1× N-VDLA v2
vi. DLA frequency 1230MHz (Max)
vii. Vision accelerator 1× PVA v2
viii. Memory 8GB 128-bit LPDDR5, 102.4GB/s
ix. Support external NVMe
x. Power Consumption: 10W | 15W | 25W | 40W
xi. USB:
a.
USB 3.2 2.0 (GBps)
b.
USB 2.0
xii. Support up to 4 cameras (8 via virtual channels), 8 lanes
MIPI CSI-2, D-PHY 1.2 (up to 20 GBps)
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xiii. Video Encode:
a.
minimum 1× 4K 60fps (H.265)
b.
3× 4K 30fps (H.265)
c.
3× 4K 30fps (H.265)
xiv. Video Decode:
a.
minimum 1× 8K 30fps (H.265) |
b.
2× 4K60 (H.265) |
c.
4× 4K30 (H.265)
d.
9× 1080p60 (H.265)
e.
18× 1080p30 (H.265)
xv. 1× 8K 30fps multi-mode DP 1.4a (+MST)/eDP 1.4a/HDMI 2.1
xvi. 3× UART, 2× SPI, 2× I2S, 4× I2C, 1× CAN, DMIC & DSPK,
PWM, GPIO
xvii. Networking 1× GbE
xviii. Dimension: 69.6 × 45 mm | 260-pin SO-DIMM connector
xix. Warranty: at least 1 year
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 9700.00,
                'total_price'     => 48500.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.3.9',
                'category'        => 'Field Robotics Lab',
                'name'            => 'AI Developer Kit 40 TOPS 8GB Module Robot Edging',
                'specification'   => 'Compute AI Development Board - 8GB External Storage
i.
GPU: AmpereTM architecture with -1024 × CUDA Cores,
32 × Tensor Cores
ii.
CPU: 6-core Arm Cortex -A78AE v8.2 64-bit CPU 1.5 MB
L2 + 4 MB L3
iii.
Memory: 8GB, 128-bit, LPDDR5, 102GB/s or 68GB/s
(Depending on the power mode)
iv.
Storage: External through microSD slot, External NVMe
SSD through M.2 Key-M, 8GB
v.
Power: 7W to 25W
vi.
Camera: 2 × MIPI CSI-2 22-pin Camera Connectors
vii.
M.2 Key-M: × 4 PCIe Gen3, × 2 PCIe Gen3
viii.
M.2 Key-E: PCIe (× 1), USB 2.0, UART, I2S, and I2C
ix.
Pre-installed with Wireless Card, IEEE 802.11ac and
Bluetooth 5.0
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
x.
USB: 4 × Type A USB 3.2 Gen2, 1 × Type C (for Debug
and Device Mode only, not for power)
xi.
Networking: 1 × Gigabit Ethernet RJ45 Connector
xii.
Multimedia Output: Display Port 1.2 (+MST)
xiii.
microSD slot: UHS-1 cards up to SDR104 mode
xiv. Others: 40-pin Expansion Header (UART, SPI, I2S, I2C,
GPIO)
xv.
12-pin button header, 4-pin fan header, DC power jack,
Compatible with Jetson Orin NX module
xvi. Dimensions: 100 mm × 79 mm × 21 mm (Height includes
feet, carrier board, module, and thermal solution)
xvii. Warranty: at least 1 year
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 2700.00,
                'total_price'     => 13500.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.4.4.1',
                'category'        => 'Field Robotics Lab',
                'name'            => 'Rajant Kinetic Mesh Node Network',
                'specification'   => 'i.
Communication network for mobile connectivity machine to-
machine (M2M) BreadCrumb ES1-2450CS System kinetic
mesh node
ii.
Two transceiver MIMO (2.4 GHz and 4.9/5 GHz)
iii.
Lifetime firmware and software updates for ES family
BreadCrumbs
iv.
Antenna, omnidirectional, 2.4-2.5 GHz, 5 dBi, Type N
v.
(male) connector, includes BAM1013 clamp mount
vi.
Antenna, omnidirectional, 5.7-5.9 GHz, 6 dBi, Type N
vii.
(male) connector, includes BAM1013 clamp mount
viii. AC/DC POE injector
ix.
ES1 cable, USB Type A and zeroize switch to M8
x.
Warranty: at least 3 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.5
SMART MANUFACTURING LABORATORY
2.5.1
ICT EQUIPMENT FOR SMART MANUFACTURING LAB',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 50000.00,
                'total_price'     => 100000.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('SNS Network (M) Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.1',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'GPU Server Generative AI & Omniverse (Digital Twin',
                'specification'   => 'Applications)
The specification should be at least:
i.
QuantaGrid D54U-3U 3U chassis, 3.5"
ii.
10 x 2.5" front hot-plug NVMe/SATA/SAS bays
iii. 4 x PCIe 5.0 x16 slots for dual-width GPU cards
iv. 2 x M.2 2230 PCIe slots for OS SSD
v.
Processor: 2 x Intel® Xeon® Silver 4514Y (16C/32T, 30.00MB
Cache, 2.00 GHz, D5 4400, TB, HT, 150 W)
vi. Memory: 4 x 32 GB RDIMM, DDR5-5600, 1.1 V
vii. GPU: 2 x NVIDIA® RTX PRO 6000 Blackwell workstation
edition, 96 GB
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
viii.
GDDR7, PCIe® 5.0 x16, du-lot, active cooling, 600 W
ix.
PSU: 4 x 2000W 80 plus titanium hot-plug PSU
x.
HDD for OS: 1 x Boot-up PCIe adapter card with 2 x 480 GB
Neube
xi.
EPR3750 0.6 DWPD M.2 NVMe SSD RAID1
xii.
HDD: 1 x 2.5" 960 GB TLC NVMe SSD (PM9A3, PCIe 4.0
x4, approx. 1 DWPD, 7mm)
xiii.
NIC SFP: 1 x Intel® E810 25 Gbps SFP28 Dual Port OCP
3.0 Card
xiv.
NIC BaseT: Ethernet adapter Intel® I350-T4 PCIe® 2.0 x4
Quad Port 1 GbE RJ45
xv.
Power cord: 1.8m C13/C14 power cord
xvi.
(Parts & Labor only) inclusive of AHR for components except
for GPU card
xvii. Server quoted with 2 GPU card but able to support 4 cards
xviii. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 408275.00,
                'total_price'     => 408275.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.10',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Laptop',
                'specification'   => 'The specification should be at least:
i.
Processor: Ultra 7 155H
ii.
OS: Windows 11, Professional
iii. Memory: 16 GB DDR5-5600MHz
iv. Storage: 1TB NVMe M.2 SSD
v.
Display: 16" IPS and anti-glare display
vi. Connectivity: Wi-Fi 7, Bluetooth v5.3, HDMI 2.1, RJ-45, 2x
USB-C, 2x USB-A
vii. Accessories: Wireless mouse, Bag
viii. Model Type: Business model
ix. Warranty: at least 3 years
11 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'DELL PC16250; Smart Manufacturing, 11 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 6177.00,
                'total_price'     => 67947.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 11,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.5.1.11',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'All-In-One Video Conferencing System for Meeting Room',
                'specification'   => 'The specification should be at least:
i.
Camera: Features a motorized camera with a resolution of up
to 4K and offers a 120-degree diagonal field of view, with
motorized pan capabilities of ±25° and tilt of ±15°, along with
a 4x HD digital zoom.
ii.
Audio: Beamforming microphones that provide a pickup
range of up to 23 feet (7 meters) and cover a frequency range
of 90Hz – 16kHz with high-quality speakers
iii. Connectivity: HDMI outputs for up to 1080p display
resolutions, HDMI input, and USB ports, including Type-A
and Type-C. Features a 10/100/1G Ethernet port, Wi-Fi
iv. 802.11 a/b/g/n/ac, and a proprietary external microphone
input for additional mic pods
v.
Complete with Installation
vi. Warranty: at least 3 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 24706.00,
                'total_price'     => 49412.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.12',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Touch Controller',
                'specification'   => 'The specification should be at least:
i.
Display: 10.1-inch touch controller with resolution of 1280
pixels x 800 pixels and is designed with internal cable
retention
ii.
Connectivity:  Power over Ethernet for power and data
transmission, a 10/100 Mbps Ethernet port, and Wi-Fi
802.11a/b/g/n/ac.
iii. Complete with Installation
iv. Warranty: at least 3 years
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 24706.00,
                'total_price'     => 49412.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.13',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Outdoor LED Panel',
                'specification'   => 'The specification should be at least:
i.
Screen Size: 140 inches diagonal (approx. 3.2 m x 1.8 m
depending on aspect ratio)
ii.
Aspect ratio: 16:9 or customizable
iii. Pixel pitch: P3.91 / P4.81 / P6
iv. Viewing angle: 140° horizontal / 120° vertical
v.
IP rating: IP65 front / IP54 rear (weatherproof for rain and
dust)
vi. Mounting: Wall-mount
vii. Power consumption: Max: 800–1000W/m²
Average: 300–500W/m²
viii. Operating temperature: -20°C to +50°C
ix. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
2.5.2
ROBOTIC EQUIPMENTS FOR SMART MANUFACTURING LAB',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 83600.00,
                'total_price'     => 83600.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.2',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Server for Warehouse Management System (WMS)',
                'specification'   => 'The specification should be at least:
i.
QuantaGrid D54X-1U 1U Chassis, 2.5"
ii.
Max 12 x 2.5" NVMe/SATA/SAS Hot-plug disks
iii.
32 x DDR5 RDIMM slots
iv.
3 x PCIe 5.0 x16 HHHL slots
v.
2 x PCIe 5.0 x16 OCP 3.0 slots
vi.
2 x M.2 2230 PCIe slots for OS SSD
vii. Processor: 2 x Xeon Silver 4509Y (8C/16T, 22.50MB Cache,
viii. 2.60 GHz, D5 4400, TB, HT, 125 W)
ix.
Memory: 4 x 32 GB RDIMM, DDR5-5600, 1.1V
x.
PSU: 2 x 1600W 80 plus titanium hot-plug PSU
xi.
HDD for OS: 1 x Boot-up PCIe adapter card with 2 x 480 GB
Neube
xii. EPR3750 0.6 DWPD M.2 NVMe SSD RAID1
xiii. HDD: 1 x 2.5" 960 GB TLC NVMe SSD, PM9A3, PCIe 4.0 x4,
approx. 1 DWPD, 7mm)
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
xiv.
NIC SFP: 1 x Intel® E810 25Gbps SFP28 Dual Port OCP
3.0 card
xv.
NIC BaseT: Ethernet adapter Intel® I350-T4 PCIe® 2.0 x4
quad port 1GbE RJ45
xvi.
Power Cord: 1.8m C13/C14 Power Cord
xvii. Parts and Labor:  inclusive of AHR for component
xviii. Warranty: at least 3 years
xix. SCADA system:
a. License type: perpetual license
b. Protocol: TCP/IP, MQTT module
c. Details:
d. Ignition SCADA (next generation industrial control platform
e. Brand: Inductive Automation
f. open standard & web-based technologies
g. HMI / SCADA / MES / IIoT on single unified platform
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 297854.00,
                'total_price'     => 297854.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.3',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Server Rack',
                'specification'   => 'The specification should be at least:
i.
Rack SR-V081220ST
a.
42U x 800W x 1200D, Front - 1 piece
b.
9U x 800W x 1200D, Front - 1 piece
c.
Front single & Rear split Mesh Door with side Panels
d.
RAL7021 Black
2 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => '42U Vertiv VE Rack SR-V081220SF dan 9U Maxxpro WMS66P',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 16204.00,
                'total_price'     => 32408.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 2,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.5.1.4',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Keyboard, Video, Mouse (KVM) Switch and Monitor',
                'specification'   => 'The specification should be at least:
i.
Switch and monitor (F0262792 CLRA19KMM8A)
ii.
18.5" LCD Tray
iii.
8 Ports Analog KVM
iv.
8 × VGA/USB target cables, USB KB‑US INTL, with jumper
cord
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'Vertiv CLRA19KMM Local Rack Access Console IP-KVM',
                'serial_numbers'  => '2.102330051224402e+19',
                'country_of_origin' => null,
                'unit_price'      => 40618.00,
                'total_price'     => 40618.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-12',
                'do_no'           => 'UTMCARD/2025/005',
            ],
            [
                'item_no'         => '2.5.1.5',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Network Attached Storage (NAS)',
                'specification'   => 'The specification should be at least:
i.
Xeon D-1541 8core/16Threads
ii.
30TB total storage
iii.
16GB DDR4 ECC RDIMM
iv.
24 drive bays
v.
Redundant power supply
vi.
4 x RJ-45 1GbE LAN Port
vii. 2 x RJ-45 10GbE LAN Port
viii. 2U form factor
ix.
Over 350,500/129,400 ISCI 4K random read/write IOPS
x.
10 x SAT5221-3840GB Disk
xi.
Rail RKS-02
xii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 84989.00,
                'total_price'     => 84989.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.6',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Uninterrupted Power Supply (UPS)',
                'specification'   => 'The specification should be at least:
i.
20 kVA
ii.
Type: Online double-conversion
iii.
Power capacity: 20 kVA
iv.
Input: 208/120V or 220/127V (4-wire) Voltage Range, 40-72Hz
frequency range
v.
Output: 208/120V or 220/127V (4-wire), 40-72Hz frequency
range
vi.
Overload Capacity:
a.
150% for 40 ms
b.
125% for 30 second
c.
110% for 10 minutes
vii. Efficiency: Up to 98% efficiency in energy-saving mode
viii. Battery: Internal batteries provide up to 21 minutes of runtime
ix. Complete with installation
x.
Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 23389.00,
                'total_price'     => 23389.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.7',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'TV for Lab Display',
                'specification'   => 'The Specification should be at least:
i.
65" 4K UHD AI TV UA73 HDR10
ii.
TV Dimensions without Stand (WxHxD, mm): 1446 x 834 x
59.9
iii. Refresh Rate: 60Hz Native
iv. With installation c/w brackets
v.
Warranty: at least 3 years
1 unit',
                'brand'           => '| Harga dipadankan secara fuzzy: TV FORLAB DISPLAY (INCLUDE INSTALLATION) (SMART MAN',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 3423.00,
                'total_price'     => 3423.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.8',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Patch Panel',
                'specification'   => 'The specification should be at least:
i.
UTP and Fibre Optic
ii.
UTP CAT 6A - From server room to smart lab (come with
faceplate)
iii.
48 ports
iv. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 15052.00,
                'total_price'     => 15052.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.1.9',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Desktop PC',
                'specification'   => 'The specification should be at least:
i.
Processor: i7-12700H
ii.
OS: Windows 11 Professional
iii. Memory: 16GB of DDR5-5200MHz
iv. Storage: 512GB NVMe M.2 SSD
v.
Warranty: at least 3 years
vi. Connectivity:
a. Wi-Fi 6e
b. Bluetooth v5.3
c. HDMI 2.1 Port
d. DisplayPort
e. RJ-45 ports
f. USB-C port
g. 8x USB-A port
5 units
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
vii. Peripherals:
a. Wireless Mouse
b. Wireless Keyboard
c. Wireless Headphone
d. 100 Hz 24 in. IPS Monitor
e. HDMI to HDMI cable
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => 'DELL QCM1250; Smart Manufacturing, 5/6 unit seperti DO',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 7039.00,
                'total_price'     => 35195.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 5,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
            [
                'item_no'         => '2.5.2.1',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Vision COBOT Picking System',
                'specification'   => 'The specification should be at least:
i.
3D bin picking COBOT AI solution c/w camera mounting
ii.
3D smart camera:  M-1200C-GL
iii. Authorized 3D vision software
iv. Authorized intelligent robot programming software
v.
Calibration board
vi. Industrial PC c/w 22" LCD monitor
vii. Industrial Personal Computer (IPC)
a. Specification: i5 / 16G RAM / 512Gb SS or higher
b. GPU: GTX1660S
c. Monitor: 24" monitor
d. Keyboard & mouse wireless
x.
Complete with installation and training
xi. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 226109.00,
                'total_price'     => 226109.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.2.2',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Conveyor System',
                'specification'   => 'The Specification should be at least:
i.
Interroll MCP conveyor system
ii. Type: EC5000BI roller type conveyor c/w MCP play control
module
iii. Straight Conveyor with the following dimensions:
a. At least 3600 mm length
b. No L-turns and no rounded sections.
iv. Roller diameter: min. 50 mm
v. Roller pitch: min. 90 mm
vi. Frame material: powder coated mild steel frame
vii. Roller material: nickel coated roller
viii. Interroll roller conveyor BF420, L720 mm
ix. Interroll multicontrol card with bus interface estimation
x. Warranty: at least 3 years
1 unit',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 169445.00,
                'total_price'     => 169445.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.2.3',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Automatic Sorter Machine',
                'specification'   => 'i.
Auto sorter machine main module c/w inbound scanning conveyor
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
ii.
Elevator module
iii. Sorting cart module
iv. Barcode scanner (fixed mounted, top scanning)
v.
Sorting output: 12 bins (horizontal 3 x vertical 4)
vi. Bin size (L x W x H): Approx. 495 mm x 340 mm x 120 mm, plastic
bin
vii. Sorting speed: Approx. 1500 UPH, subject to operator loading
speed
viii. Main controller: embedded PC controller with Warehouse Control
System (WCS)
ix. Product infeed: 1 fixed scanner camera, conveyor station
x.
c/w LCD display monitor for machine status
xi. Max product size (L x W x H): 200 mm x 300 mm x 50 mm
xii. Min product size (L x W x H): 10 mm x 30 mm x 3 mm
xiii. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 305363.00,
                'total_price'     => 305363.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.2.4',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'COBOT Gripper',
                'specification'   => 'i.
Gripper Type: Electric parallel gripper, compact design, no
external compressor required
ii. Drive: Built-in servo/electric actuator
iii. Stroke: Adjustable (e.g., 0 mm–20 mm depending on model)
iv. Grip Force: Approx. 20 N –50 N (suitable for light–medium
payloads
v. Gripper to Robot Adaptor Mount: Aluminium adaptor mount
with gripper holder ,1 piece (customizable to robot flange
standard ISO 9409-1)
vi. Power & Control: Operates on 24 V DC (from cobot I/O)
vii. Communication: Digital I/O / Modbus / EtherCAT
viii. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 64603.00,
                'total_price'     => 64603.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.2.5',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Mechanical Handling',
                'specification'   => 'i.
COBOT picking bin positioner c/w structure & mounting, c/w
mounting adaptor onto conveyor
ii.
Bin holder surface (W x D): at least 600 mm × 400 mm
iii. Structure height: at least 750 mm to 1000 mm (adjustable)
iv. COBOT mounting base c/w mechanical structure (aluminium
profile): 1 unit
v.
at least 40 mm × 40 mm or 45×90 mm extrusion
vi. Warranty: at least 3 years
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 32306.00,
                'total_price'     => 32306.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.2.6',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => 'Embedded System and I/O Peripheral',
                'specification'   => 'i. Single Board Computer (SBC):
a. Processor: Broadcom BCM2712
b.
2.4GHz quad-core, 64-bit arm cortex-A76 CPU, with
512KB L2 caches and a 2MB shared L3 cache
c.
Video Core VII GPU, supporting OpenGL ES 3.1, Vulkan
1.2
d. 4Kp60 HEVC decoder
e. LPDDR4X-4267 SDRAM
f.
Dual-band 802.11ac Wi-Fi
g. Bluetooth 5.0 / Bluetooth Low Energy (BLE)
h.
Micro SD card slot, with support for high-speed SDR104
mode
i.
2 × USB 3.0 ports, supporting simultaneous
j.
2 × USB 2.0 ports
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
k. Gigabit Ethernet, with PoE+ support (requires PoE+ HAT)
l.
2 × 4 lane MIPI camera/display transceivers
m. PCIe 2.0 x 1 interface for fast peripherals
ii. DHT 11 Sensors:
a. Type: Digital Temperature & Humidity Sensor
b. Interface: Single-bus digital (1-wire protocol)
c. Supply Voltage (Vcc): 3.3V – 5.5V
d. Operating Current: 0.5 mA (measuring), 2.5 mA
(standby)
e. Sampling Rate: 1 Hz (1 reading per second)
f. Physical Size (sensor module): ~15.5mm × 12mm ×
5.5mm (depends on module board)
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
iii. Tower Light:
a. Type: LED tower light, 3-tier
b. Colours: Red, Amber, Green
c. Voltage: 24V DC / 220V AC (selectable)
d. Lighting: Steady or Flashing
e. Sound: Optional buzzer (90 dB)
f. Diameter: 50–70 mm',
                'brand'           => null,
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 2577.00,
                'total_price'     => 2577.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'belum_diterima',
                'received_date'   => null,
                'do_no'           => null,
            ],
            [
                'item_no'         => '2.5.3.1',
                'category'        => 'Smart Manufacturing Lab',
                'name'            => '7 Drawer Tool Trolley',
                'specification'   => 'The specification should be at least:
i.
The trolley is equipped with 5-inch heavy-duty wheels
featuring brakes for stability and quiet movement.
ii.
Each drawer is fitted with a heavy-duty latch
iii. 45 mm rollers, supporting a maximum load of up to 35 kg per
drawer.
iv. List of tools provided:
a. 1/4" drive socket set (Metric)
b. 3/8" drive socket set (Metric) - 66 pieces
c. 1/2" drive socket set (Metric) - 33 pieces
d. Double box-end wrench set (Metric) - 27 pieces
e. Combination wrench and hex key set (Metric) -10 pieces
f. Combination wrench set (Metric) - 30 pieces
1 unit
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
g. Adjustable wrench and pliers set - 8 pieces
h. Adjustable wrench and pliers set - 5 pieces
i. File set - 8 pieces
j. Snap ring pliers set - 4 pieces
k. Pliers set- 4 pieces
l. Screwdriver set -13 pieces
m. 8 mm hex shank bits set - 42 pieces
n. Dial calliper (0-150 mm)
o. Adjustable hacksaw (10"/12")
p. Measuring tape (7.5 m x 25 mm)
q. Magnetic pick-up tool
r. Circuit tester (145 mm) - 39 pieces
s. 8 m/26\' x 25 mm/1" legacy tape measure
t. 4-piece tool set
v. Warranty: at least 3 years
TENDER FOR SUPPLY, DELIVERY, INSTALLATION, INTEGRATION, TESTING AND COMMISSIONING OF ROBOTICS EQUIPMENT AND ICT
GOODS FOR PROCUREMENT PROJECT UNDER NEW BUILDING H4, CENTRE FOR ARTIFICIAL INTELLIGENCE & ROBOTICS (CAIRO), UTM
KUALA LUMPUR
_________________________
_________________________
_________________________
keterangan berhubung dengan pengenalan petender dijadual ini/katalog/risalah teknikal.
NEGARA
ASAL
3.0
STANDARD ACCESSORIES
4.0
SPECIFICATION
4.1
Tenderer shall include a written response to',
                'brand'           => 'SATA 95107-P15A-BL; 1 unit',
                'serial_numbers'  => null,
                'country_of_origin' => null,
                'unit_price'      => 6867.00,
                'total_price'     => 6867.00,
                'sodo_bayaran'    => 'B35903',
                'supplier_id'     => $sid('DOTCOM Telecommunications Sdn. Bhd.'),
                'quantity'        => 1,
                'status'          => 'diterima',
                'received_date'   => '2026-03-04',
                'do_no'           => 'UTMCARD/2025/003',
            ],
        ];

        foreach ($items as $item) {
            MasterItem::create($item);
        }

        $this->command->info('Seeded ' . count($items) . ' master items.');
    }
}
