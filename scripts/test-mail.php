<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create an unverified user
$user = App\Models\User::factory()->unverified()->create([
    'email' => 'live-test@cairo.test',
]);

echo "User created: {$user->id} / {$user->email}\n";
echo "Verified: " . ($user->email_verified_at ?? 'null') . "\n";

// Send the verification notification
$user->sendEmailVerificationNotification();
echo "Verification notification sent.\n";
