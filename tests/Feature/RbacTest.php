<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\MasterItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ────────────────────────────────────────────────────────

    private function createStaff(array $attrs = []): User
    {
        return User::factory()->staff()->create($attrs);
    }

    private function createAdmin(array $attrs = []): User
    {
        return User::factory()->admin()->create($attrs);
    }

    private function createUnverifiedUser(): User
    {
        return User::factory()->unverified()->staff()->create();
    }

    private function createAsset(): Asset
    {
        return Asset::create([
            'asset_tag'      => 'AST-TEST-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'name'           => 'Test Asset for RBAC',
            'category'       => 'Office Equipment',
            'asset_type'     => 'fixed_asset',
            'purchase_price' => 5000.00,
            'location'       => 'Test Lab',
            'status'         => 'active',
        ]);
    }

    private function createUnreceivedItem(): MasterItem
    {
        return MasterItem::create([
            'item_no'    => 'TEST-' . str_pad(MasterItem::count() + 1, 4, '0', STR_PAD_LEFT),
            'category'   => 'Test Category',
            'name'       => 'Test Item for Receive',
            'unit_price' => 100.00,
            'total_price'=> 100.00,
            'quantity'   => 1,
            'status'     => 'belum_diterima',
        ]);
    }

    private function createReceivedItem(): MasterItem
    {
        return MasterItem::create([
            'item_no'    => 'TEST-' . str_pad(MasterItem::count() + 1, 4, '0', STR_PAD_LEFT),
            'category'   => 'Test Category',
            'name'       => 'Test Item for Verify',
            'unit_price' => 3200.00,
            'total_price'=> 3200.00,
            'quantity'   => 1,
            'status'     => 'diterima',
            'received_date' => now(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  STAFF — should be able to view, receive, scan, import
    // ═══════════════════════════════════════════════════════════════════

    public function test_staff_can_view_verification_page(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get('/verification');

        $response->assertStatus(200);
    }

    public function test_staff_can_receive_items(): void
    {
        $staff = $this->createStaff();
        $item  = $this->createUnreceivedItem();

        $response = $this->actingAs($staff)
            ->post("/verification/{$item->id}/receive", [
                'do_no' => 'DO-TEST-001',
            ]);

        // Receive succeeds — staff enters data
        $response->assertSessionHas('success');
        $this->assertEquals('diterima', $item->fresh()->status);
    }

    public function test_staff_can_access_scanner_lookup(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)
            ->post('/scanner/lookup', ['serial_number' => 'TEST-SN-001']);

        $response->assertStatus(200);
    }

    public function test_staff_can_view_import_page(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get('/pdf-import');

        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  STAFF — CANNOT verify (admin gate)
    // ═══════════════════════════════════════════════════════════════════

    public function test_staff_cannot_verify_items(): void
    {
        $staff = $this->createStaff();
        $item  = $this->createReceivedItem();

        $response = $this->actingAs($staff)
            ->post("/verification/{$item->id}/verify");

        // AdminMiddleware redirects non-admin to dashboard with error
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertEquals('diterima', $item->fresh()->status);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ADMIN — can do everything including verify
    // ═══════════════════════════════════════════════════════════════════

    public function test_admin_can_view_verification_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/verification');

        $response->assertStatus(200);
    }

    public function test_admin_can_receive_items(): void
    {
        $admin = $this->createAdmin();
        $item  = $this->createUnreceivedItem();

        $response = $this->actingAs($admin)
            ->post("/verification/{$item->id}/receive", [
                'do_no' => 'DO-ADMIN-001',
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('diterima', $item->fresh()->status);
    }

    public function test_admin_can_verify_items(): void
    {
        $admin = $this->createAdmin();
        $item  = $this->createReceivedItem();

        $response = $this->actingAs($admin)
            ->post("/verification/{$item->id}/verify", [
                'location'       => 'Test Lab',
                'custodian_name' => 'Test Custodian',
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('verified', $item->fresh()->status);
        $this->assertNotNull($item->fresh()->asset);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MIDDLEWARE — direct unit test of AdminMiddleware redirect
    // ═══════════════════════════════════════════════════════════════════

    public function test_admin_middleware_redirects_staff_to_dashboard(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get('/admin/dashboard');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Akses ditolak. Anda tidak mempunyai kebenaran.');
    }

    public function test_admin_middleware_allows_admin(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  STAFF — CANNOT create/update/delete assets (ADMIN GATE)
    //  Routes now have ->middleware('admin') on POST/PUT/DELETE /assets
    // ═══════════════════════════════════════════════════════════════════

    public function test_staff_cannot_create_asset(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->post('/assets', [
            'asset_tag'      => 'AST-NEW-001',
            'name'           => 'Staff-Created Asset',
            'category'       => 'Office',
            'asset_type'     => 'inventory',
            'purchase_price' => 100.00,
            'location'       => 'Lab',
            'status'         => 'active',
        ]);

        // AdminMiddleware redirects non-admin to dashboard with error
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertEquals(0, Asset::where('name', 'Staff-Created Asset')->count());
    }

    public function test_staff_cannot_update_asset(): void
    {
        $staff = $this->createStaff();
        $asset = $this->createAsset();

        $this->actingAs($staff)->put("/assets/{$asset->id}", [
            'asset_tag'      => $asset->asset_tag,
            'name'           => 'Hacked Name',
            'category'       => $asset->category,
            'asset_type'     => $asset->asset_type,
            'campus'         => $asset->campus ?? 'utm_jb',
            'purchase_price' => $asset->purchase_price,
            'location'       => $asset->location,
            'status'         => $asset->status,
        ]);

        // AdminMiddleware redirects, name unchanged
        $this->assertNotEquals('Hacked Name', $asset->fresh()->name);
    }

    public function test_staff_cannot_delete_asset(): void
    {
        $staff = $this->createStaff();
        $asset = $this->createAsset();
        $assetId = $asset->id;

        $this->actingAs($staff)->delete("/assets/{$assetId}");

        // AdminMiddleware redirects, asset still exists
        $this->assertNotNull(Asset::find($assetId));
    }

    // ═══════════════════════════════════════════════════════════════════
    //  STAFF — CANNOT access admin-only report pages
    // ═══════════════════════════════════════════════════════════════════

    public function test_staff_cannot_access_kewpa4_report(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get('/reports/kewpa4');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_staff_cannot_access_kewpa5_report(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get('/reports/kewpa5');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  UNVERIFIED EMAIL — now enforced via MustVerifyEmail
    //  The User model implements MustVerifyEmail, so the 'verified'
    //  middleware now redirects unverified users to /email/verify.
    // ═══════════════════════════════════════════════════════════════════

    public function test_unverified_user_cannot_access_dashboard(): void
    {
        $user = $this->createUnverifiedUser();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/verify-email');
    }

    public function test_unverified_user_cannot_access_verification_page(): void
    {
        $user = $this->createUnverifiedUser();

        $response = $this->actingAs($user)->get('/verification');

        $response->assertRedirect('/verify-email');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  GUEST — not logged in, redirected to login
    // ═══════════════════════════════════════════════════════════════════

    public function test_guest_redirected_to_login_for_assets(): void
    {
        $response = $this->get('/assets');

        $response->assertRedirect('/login');
    }

    public function test_guest_redirected_to_login_for_verification(): void
    {
        $response = $this->get('/verification');

        $response->assertRedirect('/login');
    }

    public function test_guest_redirected_to_login_for_scanner(): void
    {
        $response = $this->post('/scanner/lookup', ['serial_number' => 'TEST']);

        $response->assertRedirect('/login');
    }
}
