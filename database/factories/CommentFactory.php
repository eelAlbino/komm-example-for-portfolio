<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Project;
use App\Models\CommentStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $createdBy = User::factory();
        return [
            'project_id' => Project::factory([
                'created_by' => $createdBy
            ]),
            'created_by' => $createdBy,
            'parent_id' => null,
            'status_id' => CommentStatus::factory(),
            'mentioned_users' => [], 
            'number' => $this->faker->randomNumber() .'-'. $this->faker->randomNumber(),
            'text' => $this->faker->sentence,
            'url' => $this->faker->url,
            'title' => $this->faker->sentence,
            'type' => $this->faker->randomElement(['text', 'media']),
            'selector_start' => (string) $this->faker->randomNumber(),
            'selector_end' => (string) $this->faker->randomNumber(),
            'fragment' => $this->faker->sentence,
            'index_start' => (string) $this->faker->randomNumber(),
            'index_end' => (string) $this->faker->randomNumber(),
            'screen' => $this->faker->sentence,
            'viewport' => $this->faker->sentence,
            'ua' => $this->faker->sentence,
            'os' => $this->faker->word,
            'browser' => $this->faker->word,
            'version' => $this->faker->regexify('^[1-9]\d*\.\d+\.\d+$'),
            'device' => $this->faker->randomElement(['desktop', 'tablet', 'mobile']),
            'is_private' => $this->faker->boolean(),
        ];
    }
}
