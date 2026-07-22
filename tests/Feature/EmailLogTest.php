<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EmailLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_writes_verification_email_to_log(): void
    {
        // Override to use log driver for this test
        Config::set('mail.default', 'log');

        // Clear log before test
        file_put_contents(storage_path('logs/laravel.log'), '');

        $this->post('/register', [
            'name'                  => 'Log Test User',
            'email'                 => 'log-test@cairo.test',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'log-test@cairo.test']);

        // Read the log file
        $log = file_get_contents(storage_path('logs/laravel.log'));

        $this->assertStringContainsString('log-test@cairo.test', $log,
            'Verification email should be written to log');

        // Extract verification URL
        preg_match('/https?:\/\/[^\s\"]+\/verify-email\/[^\s\"]+/', $log, $matches);
        $this->assertNotEmpty($matches, 'Verification URL should be present in log');
        echo "\n\n=== VERIFICATION URL ===\n{$matches[0]}\n========================\n\n";
    }
}
