#!/usr/bin/env python3
"""Analyse discrepancies between master list and source documents."""

import openpyxl, re, json

wb = openpyxl.load_workbook(
    '/mnt/c/Hakimi/Internship/System/cairo-inventory/item_documents/Senarai_Peralatan_CAIRO_Lengkap_Subtotal. 16062026 1149xlsx (1) (1).xlsx',
    data_only=True
)
ws = wb['Senarai Peralatan']

# Parse seeder fully
with open('/mnt/c/Hakimi/Internship/System/cairo-inventory/database/seeders/MasterItemSeeder.php', 'r',
          encoding='utf-8', errors='replace') as f:
    content = f.read()

# Extract all items from seeder with full data
seeder_items = {}
# Find each item block
pattern = r"'item_no'\s*=>\s*'([\d.]+)'"
matches = list(re.finditer(pattern, content))

for i, match in enumerate(matches):
    item_no = match.group(1)
    start = match.start()
    if i + 1 < len(matches):
        end = matches[i+1].start()
    else:
        end = content.rfind('];')

    block = content[start:end]
    item = {'item_no': item_no}

    # Extract fields
    name_m = re.search(r"'name'\s*=>\s*'([^']*)'", block)
    item['name'] = name_m.group(1) if name_m else ''

    brand_m = re.search(r"'brand'\s*=>\s*'([^']*)'", block)
    item['brand'] = brand_m.group(1) if brand_m else None

    up_m = re.search(r"'unit_price'\s*=>\s*([\d.]+)", block)
    item['unit_price'] = float(up_m.group(1)) if up_m else 0

    tp_m = re.search(r"'total_price'\s*=>\s*([\d.]+)", block)
    item['total_price'] = float(tp_m.group(1)) if tp_m else 0

    qty_m = re.search(r"'quantity'\s*=>\s*(\d+)", block)
    item['qty'] = int(qty_m.group(1)) if qty_m else 0

    st_m = re.search(r"'status'\s*=>\s*'([^']+)'", block)
    item['status'] = st_m.group(1) if st_m else ''

    sn_m = re.search(r"'serial_numbers'\s*=>\s*'([^']*)'", block)
    item['serial_numbers'] = sn_m.group(1) if sn_m else None

    cat_m = re.search(r"'category'\s*=>\s*'([^']+)'", block)
    item['category'] = cat_m.group(1) if cat_m else ''

    sup_m = re.search(r"'supplier_id'\s*=>\s*\$sid\(['\"]([^'\"]+)['\"]\)", block)
    item['supplier'] = sup_m.group(1) if sup_m else ''

    sodo_m = re.search(r"'sodo_bayaran'\s*=>\s*'([^']+)'", block)
    item['sodo'] = sodo_m.group(1) if sodo_m else ''

    seeder_items[item_no] = item

# Also get Excel data for comparison
excel_items = {}
for row in ws.iter_rows(min_row=2, max_row=157, values_only=True):
    item_no = str(row[2] or '').strip()
    if item_no:
        excel_items[item_no] = {
            'kategori': str(row[1] or '').strip(),
            'nama': str(row[3] or '').strip(),
            'qty': row[4],
            'jenama': str(row[6] or '').strip(),
            'no_siri': str(row[7] or '').strip(),
            'negara': str(row[8] or '').strip(),
            'harga_seunit': row[9],
            'jumlah_harga': row[10],
            'sodo': str(row[11] or '').strip(),
            'supplier': str(row[12] or '').strip(),
            'status': str(row[13] or '').strip(),
            'tarikh': str(row[14] or '').strip(),
            'do_no': str(row[15] or '').strip(),
        }

print('=' * 90)
print('SECTION 1: PRICING ANOMALIES (qty × unit_price ≠ total_price)')
print('=' * 90)
print()

count = 0
for item_no in sorted(seeder_items.keys()):
    d = seeder_items[item_no]
    expected = d['qty'] * d['unit_price']
    if abs(expected - d['total_price']) > 0.01 and d['total_price'] > 0:
        count += 1
        diff = d['total_price'] - expected
        print(f'{item_no:<15} {d["category"]:<20} {d["name"][:45]:<47}')
        print(f'  Qty={d["qty"]:<3} × Unit RM {d["unit_price"]:>10,.2f} = RM {expected:>12,.2f}')
        print(f'  Actual Total:          RM {d["total_price"]:>12,.2f}  (diff: RM {diff:>+10,.2f})')
        # Check what Excel says
        if item_no in excel_items:
            e = excel_items[item_no]
            print(f'  Excel: qty={e["qty"]}, unit={e["harga_seunit"]}, total={e["jumlah_harga"]}')
        print()

print(f'Total pricing anomalies: {count}')
print()

print('=' * 90)
print('SECTION 2: ITEMS WITH INCOMPLETE/MISSING SERIAL NUMBERS')
print('=' * 90)
print()

# Items with S/N count mismatch
print('--- Items where S/N count ≠ quantity (Diterima items) ---')
for item_no in sorted(seeder_items.keys()):
    d = seeder_items[item_no]
    if d['status'] != 'diterima':
        continue
    if d['serial_numbers']:
        sn_list = [s.strip() for s in d['serial_numbers'].split(',') if s.strip()]
        if d['qty'] > 1 and len(sn_list) != d['qty']:
            flag = '⚠️  S/N COUNT MISMATCH'
            print(f'{item_no:<15} {d["name"][:40]:<42} qty={d["qty"]:<3} S/N count={len(sn_list):<3} {flag}')
            print(f'  {"":>15} S/Ns: {d["serial_numbers"][:120]}')
        elif d['qty'] == 1 and len(sn_list) == 1:
            pass  # OK
    else:
        print(f'{item_no:<15} {d["name"][:40]:<42} qty={d["qty"]:<3} NO SERIAL NUMBERS')

print()
print('--- Items with DUPLICATE serial numbers ---')
# Check if any S/Ns appear in multiple items
all_sns = {}
for item_no in sorted(seeder_items.keys()):
    d = seeder_items[item_no]
    if d['serial_numbers']:
        sn_list = [s.strip() for s in d['serial_numbers'].split(',') if s.strip()]
        for sn in sn_list:
            if sn not in all_sns:
                all_sns[sn] = []
            all_sns[sn].append(item_no)

for sn, items_list in sorted(all_sns.items()):
    if len(items_list) > 1:
        names = [f'{x} ({seeder_items[x]["name"][:30]})' for x in items_list]
        print(f'  S/N {sn:<20} appears in: {", ".join(names)}')

print()

print('=' * 90)
print('SECTION 3: QUANTITY COMPARISON (Excel/Seeder vs ACK document)')
print('=' * 90)
print()
# The ACK shows items with different quantities for some items.
# Key finding: ACK mentions "4TB External SSD 4" units but master list
# 2.1.1.6 External Storage has only qty=1
d = seeder_items.get('2.1.1.6', {})
if d:
    print(f'Item 2.1.1.6: {d["name"]}')
    print(f'  Master list qty: {d["qty"]} (unit_price=RM{d["unit_price"]:.2f})')
    print(f'  ACK doc: "1.1.1.7(ii) 4TB External SSD" — qty=4, ADATA S820')
    print(f'  ⚠️  Different item name, brand (ADATA vs UGREEN+Samsung), qty (4 vs 1), and capacity (4TB vs 1TB)')
    print()

# Items with qty=0 or None
for item_no in sorted(seeder_items.keys()):
    d = seeder_items[item_no]
    if d['qty'] == 0:
        e = excel_items.get(item_no, {})
        print(f'Item {item_no}: {d["name"]} — qty=0 in seeder, Excel qty={e.get("qty")}')
        print()

print()
print('=' * 90)
print('SECTION 4: ITEM-FIELD MISMATCHES (brand in wrong field, etc.)')
print('=' * 90)
print()
# Check brand field — some items have 'X unit' or descriptions in brand field
for item_no in sorted(seeder_items.keys()):
    d = seeder_items[item_no]
    if d['brand'] and (d['brand'].startswith('10 unit') or 
                        'unit' in d['brand'].lower() or
                        '; ' in d['brand'] or
                        'tiada' in d['brand'].lower()):
        print(f'{item_no:<15} {d["name"][:45]:<47}')
        print(f'  Brand field: "{d["brand"]}" ← might be quantity/notes, not brand')
        print()

# Items where brand contains quantity info
print('--- Items whose brand field contains quantity info ---')
for item_no in sorted(seeder_items.keys()):
    d = seeder_items[item_no]
    e = excel_items.get(item_no, {})
    if d['brand'] and re.match(r'^\d+\s*unit', d['brand'], re.IGNORECASE):
        print(f'{item_no:<15} {d["name"][:40]:<42} brand="{d["brand"]:<20}" excel_brand="{e.get("jenama", "")[:30]}" → qty={d["qty"]}')

# Check items with null brand
null_brand_diterima = [(n, d) for n, d in seeder_items.items() if not d['brand'] and d['status'] == 'diterima']
if null_brand_diterima:
    print()
    print(f'--- Delivered items with no brand: {len(null_brand_diterima)} ---')
    for item_no, d in null_brand_diterima:
        e = excel_items.get(item_no, {})
        print(f'{item_no:<15} {d["name"][:45]:<47} Excel brand: "{e.get("jenama", "")[:30]}"')

print()
print('=' * 90)
print('SECTION 5: SODO BAYARAN ANOMALIES')
print('=' * 90)
print()
sodos = {}
for item_no, d in sorted(seeder_items.items()):
    s = d.get('sodo', '')
    if s not in sodos:
        sodos[s] = []
    sodos[s].append(item_no)

for sodo, items in sorted(sodos.items()):
    print(f'SODO {sodo:<10}: {len(items)} items')
    # Check first few
    for item_no in items[:5]:
        d = seeder_items[item_no]
        print(f'  {item_no:<15} {d["name"][:40]:<42} RM {d["total_price"]:>10,.2f}')
    if len(items) > 5:
        print(f'  ... and {len(items)-5} more')
    total = sum(seeder_items[n]['total_price'] for n in items)
    print(f'  Total: RM {total:>12,.2f}')
    print()

wb.close()
print('Done.')
