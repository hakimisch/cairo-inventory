# 2026-10-18 — Tong purge + PA-1 delete (findings and plan)

Reported from the client machine (post `git pull` + `npm run build`): verification
works, delete works on `/assets`, but delete does not work on `/receivings`, and
nothing can be removed permanently from a Tong — not even as `user@gmail.com`
(superadmin).

## Findings (code facts, not a stale build)

1. **`/receivings` Hapus only exists for `pending`**
   - `resources/js/Pages/Assets/ReceivingIndex.jsx:529` opens
     `{item.status === 'pending' && (`; the Hapus button (`:597`) and the
     `handleDelete` handler (`:349`) live inside it.
   - `AssetController::destroyReceiving()` (`app/Http/Controllers/AssetController.php:187`)
     refuses anything that is not `pending`.
   - Every PA-1 created by Verify is written `status = 'accepted'`
     (`VerificationController::verify`), so those rows have no delete affordance
     anywhere. `rejected` rows — which create nothing — are also undeletable.
   - The route gate is `permission:crud_assets` (`routes/web.php:234`), which
     superadmin holds: the blocker is state, not role.
2. **`/assets` Tong is restore-only, and no permanent delete exists for assets**
   - Tong rows render only `Pulihkan` (`Assets/Index.jsx:356–378`).
   - The only `forceDelete` in the app is `VerificationController::forceDelete`
     (`:490`, route `routes/web.php:329`). `routes/web.php` has 15 `restore`
     routes and 1 `force` route, i.e. 14 modules have a one-way bin.
   - `AssetController::index()` sends no permission props at all, so the page
     gates on `['admin','superadmin']` and offers nothing more to a superadmin.
3. **End-to-end dead end after Verify** — pipeline Hapus refuses while a PA-1 or
   asset exists, the PA-1 is `accepted` (no Hapus), and its asset can be binned
   but never purged. A wrongly verified record is permanent from the UI.
4. **Missing link**: `assets` has `master_item_id` but no `receiving_id`, and
   `Receiving` has no `assets()` relation, so the app cannot tell whether a
   given PA-1 produced an asset. `destroyReceiving()` could not guard on it even
   if the status gate were relaxed.

## Decisions

- A PA-1 may be deleted in any status **as long as it has not produced an
  asset**. Accepted rows get the button only when no asset is linked; the
  backend refuses with the asset tag(s) so the operator knows the next step.
  Rationale: the PA-1 is the acquisition evidence for that asset; deleting it
  under a live asset leaves an unexplained asset in the register.
- `assets.receiving_id` is added (nullable, indexed) so the guard is real.
  `acceptReceiving()` and `verify()` set it; existing verify-created assets are
  backfilled by `master_item_id`. Historical manual PA-1A rows stay unlinked —
  nothing reliable can reconstruct them.
- Permanent delete is `permission:force_delete` (superadmin only), exposed as a
  shared `can.force_delete` Inertia prop, with a typed `PADAM` confirmation and
  an activity entry with event `force_deleted` + snapshot (audit label already
  exists in `AuditLog/Index.jsx`).
- **Purge refuses while dependent records exist** (blocking behaviour, not
  cascading): an asset with placements/inspections/maintenances/transfers/
  disposals/loss reports/damage reports/upgrades keeps its history; a supplier
  still referenced by master items keeps its link; a disposal sale with items
  keeps them. The refusal names the counts, so it is informative rather than a
  dead button.
- P3 covers the four remaining **top-level** registers (Receivings, Suppliers,
  Committee Appointments, Disposal Sales). The eight `Kewpa*` child registers
  stay restore-only by design: they are audit entries under an asset, and the
  asset purge is the place that decides their fate.

## P1 — PA-1 (receivings) delete

- [ ] migration `add_receiving_id_to_assets` (nullable + index, backfill by
      `master_item_id`).
- [ ] `Receiving::assets()` hasMany; `Asset` fillable + `receiving()`.
- [ ] `verify()` and `acceptReceiving()` stamp `receiving_id` on created assets.
- [ ] `destroyReceiving()`: refuse when linked assets exist (message names the
      tags), allow pending/rejected/accepted-without-asset, audit `deleted`.
- [ ] `receivingIndex()` adds `has_asset` per row plus `can_delete` /
      `can_force_delete` props.
- [ ] `ReceivingIndex.jsx`: Hapus gated on `can_delete && !item.has_asset`
      (moved out of the `pending` block), Tong gains `Padam Kekal`.
- Verify: `php artisan test --filter=ReceivingDelete`

## P2 — asset permanent delete

- [ ] route `DELETE /assets/{id}/force` behind `permission:force_delete`.
- [ ] `AssetController::forceDelete()`: refuse when child records exist, else
      `forceDelete()` + activity event `force_deleted` with snapshot.
- [ ] `index()` sends `can_delete` / `can_force_delete`.
- [ ] `Assets/Index.jsx`: `Padam Kekal` in the Tong row (typed `PADAM`).
- Verify: `php artisan test --filter=AssetPurge`

## P3 — purge for the remaining top-level Tongs

- [ ] shared `can.force_delete` in `HandleInertiaRequests::share()`.
- [ ] routes + controller methods: `receivings.force-delete`,
      `suppliers.force-delete`, `committee-appointments.force-delete`,
      `disposal-sales.force-delete`.
- [ ] `Padam Kekal` buttons on those four Tong panels.
- Verify: `php artisan test --filter=TongPurge`

## Deploy note (client machine)

```
git pull
php artisan migrate          # NEW — assets.receiving_id
npm run build
php artisan optimize:clear
```

## Execution log

2026-10-18 — P1, P2, P3 done in one pass.

- P1: migration `2026_10_18_000001_add_receiving_id_to_assets` (nullable + index,
  backfilled by `master_item_id`); `Receiving::assets()`; `Asset::receiving()`;
  `verify()` and `acceptReceiving()` stamp `receiving_id`;
  `destroyReceiving()` now allows any status while no linked asset exists and
  refuses with the asset tag(s) otherwise; `receivingIndex()` sends `has_asset`
  plus `can_delete` / `can_force_delete`; `ReceivingIndex.jsx` Hapus moved out of
  the `pending` block, Tong gained `Padam Kekal`.
- P2: `DELETE /assets/{id}/force` (`assets.force-delete`),
  `AssetController::forceDelete()` (Tong-only, refuses disposals / loss reports /
  damage reports / vehicle-disposal, cascades routine history, audit snapshot),
  `can_delete` / `can_force_delete` props, `Padam Kekal` in the Tong.
- P3: shared `can.force_delete` / `can.crud_assets` in `HandleInertiaRequests`;
  purge routes + controllers for receivings, suppliers, committee appointments and
  disposal sales; `Padam Kekal` on those four Tong panels. The eight `Kewpa*`
  child registers stay restore-only (decision recorded above). Hardcoded
  `['admin','superadmin']` gates on the five touched pages replaced by `can_*`.
- Tests: new `tests/Feature/TongPurgeAndReceivingDeleteTest.php` (16 tests, 46
  assertions). Full suite: **143 passed / 639 assertions / 0 failed** (236s).
  `php -l` clean on every edited PHP file; esbuild clean on the five edited JSX
  files; real `npm run build` succeeded.
- UI check on a scratch sqlite DB (`php artisan serve --port=8123`, login
  `user@gmail.com`): pending PA-1 → Hapus → Tong (1) → Padam Kekal → row gone from
  the DB; accepted PA-1 with an asset → no Hapus; accepted PA-1 without an asset →
  Hapus; asset Hapus → Tong tab → Padam Kekal → "Tong sampah kosong", asset gone,
  two `force_deleted` audit rows.
- Docs: README §Soft Delete, HANDOVER Technical Debt #16 (now PARTIAL), HANDOVER
  change log + new "Updating an existing deployment" block.
- Not done: nothing committed (`git` commands left to the operator).
