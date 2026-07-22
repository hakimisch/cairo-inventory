<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role'     => 'user',
        ];
    }

    /** Indicate the user is an admin. */
    public function admin(): static
    {
        return $this->state(fn(array $attrs) => [
            'role' => 'admin',
        ]);
    }

    /** Indicate the user is a super admin. */
    public function superadmin(): static
    {
        return $this->state(fn(array $attrs) => [
            'role' => 'superadmin',
        ]);
    }

    /** Indicate the user is a manager. */
    public function manager(): static
    {
        return $this->state(fn(array $attrs) => [
            'role' => 'manager',
        ]);
    }

    /** Indicate the user is an auditor. */
    public function auditor(): static
    {
        return $this->state(fn(array $attrs) => [
            'role' => 'auditor',
        ]);
    }

    /** Indicate the user is a regular staff member. */
    public function staff(): static
    {
        return $this->state(fn(array $attrs) => [
            'role' => 'user',
        ]);
    }

    /** Mark the user's email as unverified. */
    public function unverified(): static
    {
        return $this->state(fn(array $attrs) => [
            'email_verified_at' => null,
        ]);
    }
}
