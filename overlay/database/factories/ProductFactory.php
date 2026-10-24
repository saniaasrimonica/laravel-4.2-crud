<?php
namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class ProductFactory extends Factory
{
    public function definition(): array {
        return ['user_id' => User::factory(), 'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-########')),
            'description' => fake()->sentence(), 'price_cents' => 1250, 'stock' => 5];
    }
}
