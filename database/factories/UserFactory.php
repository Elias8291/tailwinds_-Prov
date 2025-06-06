<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'correo' => fake()->unique()->safeEmail(),
            'rfc' => strtoupper(Str::random(13)),
            'password' => bcrypt('password'),
            'estado' => 'pendiente',
            'verification_token' => Str::random(60),
            'fecha_verificacion_correo' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_verificacion_correo' => now(),
            'estado' => 'activo',
            'verification_token' => null,
        ]);
    }
}
