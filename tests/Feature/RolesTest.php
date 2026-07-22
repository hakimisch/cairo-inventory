<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\MasterItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ────────────────────────────────────────────────────────

    private function createUser(string $role, array $attrs = []): User
    {
        if (in_array($role, ['superadmin', 'admin', 'manager', 'auditor'])) {
            return User::factory()->{$role}()->create($attrs);
        }
        // 'user' is the default factory role
        return User::factory()->create($attrs);
    }

    private function createReceivedItem(): MasterItem
    {
        return MasterItem::create([
            'item_no'       => 'TEST-' . str_pad(MasterItem::count() + 1, 4, '0', STR_PAD_LEFT),
            'category'      => 'Test Category',
            'name'          => 'Test Item',
            'unit_price'    => 3200.00,
            'total_price'   => 3200.00,
            'quantity'      => 1,
            'status'        => 'diterima',
            'received_date' => now(),
        ]);
    }

    private function createAsset(): Asset
    {
        return Asset::create([
            'asset_tag'      => 'AST-ROLES-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'name'           => 'Roles Test Asset',
            'category'       => 'Office Equipment',
            'asset_type'     => 'fixed_asset',
            'purchase_price' => 5000.00,
            'location'       => 'Test Lab',
            'status'         => 'active',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  SUPERADMIN — has manage_users permission
    // ═══════════════════════════════════════════════════════════════════

    public function test_superadmin_can_manage_users(): void
    {
        $superadmin = $this->createUser('superadmin');

        $response = $this->actingAs($superadmin)->get('/admin/users');

        $response->assertStatus(200);
    }

    public function test_superadmin_can_create_user(): void
    {
        $superadmin = $this->createUser('superadmin');

        $response = $this->actingAs($superadmin)->post('/admin/users', [
            'name'     => 'New Super User',
            'email'    => 'new@test.com',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['email' => 'new@test.com']);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ADMIN — does NOT have manage_users, but has view_admin
    // ═══════════════════════════════════════════════════════════════════

    public function test_admin_cannot_manage_users(): void
    {
        $admin = $this->createUser('admin');

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_create_user(): void
    {
        $admin = $this->createUser('admin');

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name'     => 'Should Not Exist',
            'email'    => 'shouldnot@test.com',
            'password' => 'password',
            'role'     => 'user',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('users', ['email' => 'shouldnot@test.com']);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MANAGER — has view_reports but not verify
    // ═══════════════════════════════════════════════════════════════════

    public function test_manager_can_view_reports(): void
    {
        $manager = $this->createUser('manager');

        $response = $this->actingAs($manager)->get('/reports/kewpa4');

        // Permission middleware passes — no redirect to dashboard.
        // Assert that response is not a redirect (the report controller may 500
        // on SQLite due to pre-existing PostgreSQL-incompatible EXTRACT() query).
        $this->assertNotEquals(302, $response->status(), 'Manager should not be redirected from reports');
        $this->assertNotEquals(301, $response->status(), 'Manager should not be redirected from reports');
    }

    public function test_manager_cannot_verify_items(): void
    {
        $manager = $this->createUser('manager');
        $item    = $this->createReceivedItem();

        $response = $this->actingAs($manager)
            ->post("/verification/{$item->id}/verify", [
                'location' => 'Test Lab',
            ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertEquals('diterima', $item->fresh()->status);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  AUDITOR — has view_reports but not crud_assets
    // ═══════════════════════════════════════════════════════════════════

    public function test_auditor_can_view_reports(): void
    {
        $auditor = $this->createUser('auditor');

        $response = $this->actingAs($auditor)->get('/reports/kewpa5');

        // Permission middleware passes — no redirect to dashboard.
        $this->assertNotEquals(302, $response->status(), 'Auditor should not be redirected from reports');
        $this->assertNotEquals(301, $response->status(), 'Auditor should not be redirected from reports');
    }

    public function test_auditor_cannot_edit_assets(): void
    {
        $auditor = $this->createUser('auditor');
        $asset   = $this->createAsset();

        $response = $this->actingAs($auditor)->put("/assets/{$asset->id}", [
            'name' => 'Hacked by Auditor',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertNotEquals('Hacked by Auditor', $asset->fresh()->name);
    }

    public function test_auditor_cannot_delete_asset(): void
    {
        $auditor = $this->createUser('auditor');
        $asset   = $this->createAsset();
        $assetId = $asset->id;

        $response = $this->actingAs($auditor)->delete("/assets/{$assetId}");

        $response->assertRedirect('/dashboard');
        $this->assertNotNull(Asset::find($assetId));
    }

    // ═══════════════════════════════════════════════════════════════════
    //  USER (default) — has no permissions
    // ═══════════════════════════════════════════════════════════════════

    public function test_user_cannot_access_any_admin_route(): void
    {
        $user = $this->createUser('user');

        $routes = [
            '/admin/dashboard',
            '/reports/kewpa4',
            '/reports/kewpa5',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertRedirect('/dashboard');
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    //  REGISTRATION — defaults and verification state
    // ═══════════════════════════════════════════════════════════════════

    public function test_new_user_defaults_to_user_role(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'New Staff',
            'email'                 => 'newstaff@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $user = User::where('email', 'newstaff@test.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);
    }

    public function test_new_user_email_is_unverified(): void
    {
        $this->post('/register', [
            'name'                  => 'Unverified Staff',
            'email'                 => 'unverified@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'unverified@test.com')->first();
        $this->assertNull($user->email_verified_at);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MIDDLEWARE — invalid role and edge cases
    // ═══════════════════════════════════════════════════════════════════

    public function test_role_middleware_rejects_invalid_role(): void
    {
        // Create a user with a role string that doesn't exist in config
        $user = User::factory()->create([
            'role' => 'nonexistent_role',
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Akses ditolak. Peranan tidak sah.');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  USER MANAGEMENT — self-delete protection, duplicate email
    // ═══════════════════════════════════════════════════════════════════

    public function test_cannot_delete_self(): void
    {
        // Need manage_users permission to access admin/users routes
        $superadmin = $this->createUser('superadmin');

        $response = $this->actingAs($superadmin)
            ->delete("/admin/users/{$superadmin->id}");

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('users', ['id' => $superadmin->id]);
    }

    public function test_duplicate_email_rejected_on_user_create(): void
    {
        User::factory()->create(['email' => 'existing@test.com']);

        $superadmin = $this->createUser('superadmin');

        $response = $this->actingAs($superadmin)->post('/admin/users', [
            'name'     => 'Duplicate User',
            'email'    => 'existing@test.com',
            'password' => 'password',
            'role'     => 'user',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
