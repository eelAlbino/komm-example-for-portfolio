<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectRole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectUser>
 */
class ProjectUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'created_by' => User::factory(),
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'project_role_id' => ProjectRole::factory(),
        ];
    }
}
