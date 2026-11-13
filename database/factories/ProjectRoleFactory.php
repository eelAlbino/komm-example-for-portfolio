<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ProjectRole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectRole>
 */
class ProjectRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        do {
            $name = $this->faker->unique()->word;
            $slug = Str::slug($name, '-');
        } while(ProjectRole::where('name', $name)->orWhere('slug', $slug)->exists());

        return [
            'name' => $name,
            'slug' => $slug,
            'permissions' => [],
            'weight' => $this->faker->numberBetween(1, 5)
        ];
    }
}
