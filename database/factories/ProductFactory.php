<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->sentence();
        $slug = Str::slug($name);
        $admin = Admin::first();

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => $slug,
            'price' => fake()->randomFloat(2, 100, 1000),
            'created_by' => $admin->name,
        ];
    }
}
