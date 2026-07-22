<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailFullFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_verification_flow_works(): void
    {
        // Register a user
        $response = $this->post('/register', [
            'name'                  => 'Full Flow Test',
            'email'                 => 'full-flow@cairo.test',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'full-flow@cairo.test')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        // Generate the same signed URL the notification would
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        echo "\nVerification URL: {$url}\n";

        // Request the URL as the authenticated user
        $verifyResponse = $this->actingAs($user)->get($url);
        // Redirects to /dashboard?verified=1 (markEmailAsVerified sets ?verified=1)
        $verifyResponse->assertRedirect('/dashboard?verified=1');

        // Email should now be verified
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_verification_fails_with_wrong_user(): void
    {
        $user  = User::factory()->unverified()->create(['email' => 'alice@test.com']);
        $other = User::factory()->unverified()->create(['email' => 'bob@test.com']);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        // Bob tries to verify Alice's link — should get 403
        $this->actingAs($other)
            ->get($url)
            ->assertStatus(403);
    }

    public function test_verification_fails_without_auth(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'guest@test.com']);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        // Guest (not logged in) — should redirect to login
        $this->get($url)->assertRedirect('/login');
    }

    public function test_verification_fails_with_wrong_hash(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'wronghash@test.com']);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->getKey(),
                'hash' => sha1('wrong-email@test.com'), // hash doesn't match user's email
            ]
        );

        $this->actingAs($user)
            ->get($url)
            ->assertStatus(403);
    }
}
