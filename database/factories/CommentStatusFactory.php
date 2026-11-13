<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommentStatus>
 */
class CommentStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->unique()->word;
        $slug = Str::slug($name, '-');
        return [
            'name' => $name,
            'slug' => $slug,
            'color' => $this->faker->unique()->safeHexColor()
        ];
    }
}
