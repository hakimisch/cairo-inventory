#!/usr/bin/env python3
"""
validate_seeder_prices.py — Validates pricing in MasterItemSeeder.php

Checks:
  1. qty × unit_price == total_price
  2. Serial number count matches quantity (for diterima items with S/Ns)
  3. No duplicate serial numbers across items

Usage:
  python3 scripts/validate_seeder_prices.py
"""

import re
import sys

SEEDER_PATH = 'database/seeders/MasterItemSeeder.php'


def parse_seeder(path: str) -> list[dict]:
    """Parse all items from the MasterItemSeeder.php file."""
    with open(path, 'r', encoding='utf-8', errors='replace') as f:
        content = f.read()

    items = []
    pattern = r"'item_no'\s*=>\s*'([\d.]+)'"
    matches = list(re.finditer(pattern, content))

    for i, match in enumerate(matches):
        item_no = match.group(1)
        start = match.start()
        end = matches[i + 1].start() if i + 1 < len(matches) else content.rfind('];')
        block = content[start:end]

        def _extract(p: str) -> str | None:
            m = re.search(p, block)
            return m.group(1) if m else None

        name = _extract(r"'name'\s*=>\s*'([^']*)'") or ''
        unit_price = float(_extract(r"'unit_price'\s*=>\s*([\d.]+)") or 0)
        total_price = float(_extract(r"'total_price'\s*=>\s*([\d.]+)") or 0)
        qty = int(_extract(r"'quantity'\s*=>\s*(\d+)") or 0)
        status = _extract(r"'status'\s*=>\s*'([^']+)'") or ''
        sn_raw = _extract(r"'serial_numbers'\s*=>\s*'([^']*)'")
        serial_numbers = [s.strip() for s in sn_raw.split(',') if s.strip()] if sn_raw else []
        category = _extract(r"'category'\s*=>\s*'([^']+)'") or ''

        items.append({
            'item_no': item_no,
            'name': name,
            'category': category,
            'unit_price': unit_price,
            'total_price': total_price,
            'qty': qty,
            'status': status,
            'serial_numbers': serial_numbers,
        })

    return items


def main():
    items = parse_seeder(SEEDER_PATH)
    errors = 0
    warnings = 0

    print(f"Validating {len(items)} items in {SEEDER_PATH}...\n")

    # ── Check 1: Pricing anomalies ──
    print("=" * 70)
    print("1. PRICING ANOMALIES (qty × unit_price ≠ total_price)")
    print("=" * 70)
    for item in items:
        if item['qty'] == 0:
            continue
        expected = item['qty'] * item['unit_price']
        if abs(expected - item['total_price']) > 0.01 and item['total_price'] > 0:
            errors += 1
            diff = item['total_price'] - expected
            print(f"  ❌ {item['item_no']:<15} {item['name'][:40]:<42}")
            print(f"     {item['qty']} × {item['unit_price']:>10,.2f} = {expected:>12,.2f} ≠ {item['total_price']:>12,.2f} (diff: {diff:>+10,.2f})")

    if errors == 0:
        print("  ✅ All prices correct\n")

    # ── Check 2: S/N count vs quantity ──
    print("\n" + "=" * 70)
    print("2. SERIAL NUMBER COUNT vs QUANTITY (diterima items)")
    print("=" * 70)
    for item in items:
        if item['status'] != 'diterima':
            continue
        sn_count = len(item['serial_numbers'])
        if sn_count > 0 and sn_count != item['qty'] and item['qty'] > 1:
            warnings += 1
            print(f"  ⚠️  {item['item_no']:<15} {item['name'][:40]:<42} qty={item['qty']:<3} S/Ns={sn_count:<3}")
        elif sn_count == 0 and item['qty'] > 1 and item['category'] not in ['Agriculture Lab']:
            # Many agri items naturally lack S/Ns (sensors, etc.)
            pass

    if warnings == 0:
        print("  ✅ All S/N counts match\n")

    # ── Check 3: Duplicate serial numbers ──
    print("\n" + "=" * 70)
    print("3. DUPLICATE SERIAL NUMBERS ACROSS ITEMS")
    print("=" * 70)
    sn_map = {}
    for item in items:
        for sn in item['serial_numbers']:
            if sn not in sn_map:
                sn_map[sn] = []
            sn_map[sn].append(f"{item['item_no']} ({item['name'][:30]})")

    dup_count = 0
    for sn, owners in sorted(sn_map.items()):
        if len(owners) > 1:
            dup_count += 1
            print(f"  ❌ {sn:<20} appears in: {', '.join(owners)}")

    if dup_count == 0:
        print("  ✅ No duplicate serial numbers\n")

    # ── Summary ──
    print("\n" + "=" * 70)
    print("SUMMARY")
    print("=" * 70)
    print(f"  Total items: {len(items)}")
    print(f"  Pricing errors: {errors}")
    print(f"  S/N count warnings: {warnings}")
    print(f"  Duplicate S/Ns: {dup_count}")

    return 1 if errors > 0 else 0


if __name__ == '__main__':
    sys.exit(main())
