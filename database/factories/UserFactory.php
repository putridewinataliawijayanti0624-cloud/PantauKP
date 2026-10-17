<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    public function dosen(): static
    {
        return $this->state(fn () => ['role' => 'dosen']);
    }

    public function mahasiswa(): static
    {
        return $this->state(fn () => ['role' => 'mahasiswa']);
    }
}
