<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_triggers_verification_notification(): void
    {
        Event::fake();

        $this->post('/register', [
            'name'                  => 'Verify Me',
            'email'                 => 'verify-me@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = \App\Models\User::where('email', 'verify-me@test.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        Event::assertDispatched(Registered::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    public function test_verified_user_redirects_to_dashboard(): void
    {
        $user = \App\Models\User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_unverified_user_redirected_to_verify(): void
    {
        $user = \App\Models\User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/verify-email');
    }
}
