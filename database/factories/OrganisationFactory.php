<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class OrganisationFactory extends Factory
{

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->sentence,
            'created_by' => User::factory(),
            'storage_size' => 0
        ];
    }
}
