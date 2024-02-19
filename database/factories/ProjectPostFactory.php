<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ProjectPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectPost>
 */
class ProjectPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = ProjectPost::class;

    public function definition()
    {
        $title = fake()->sentence();
        $timestamp = getRandomTimestamps(-365);
        $define = [
            'title' => $title,
            'content' => fake()->paragraphs(3, true),
            'user_id' => User::all()->random()->id
        ];
        return array_merge($timestamp, $define);
    }
}
