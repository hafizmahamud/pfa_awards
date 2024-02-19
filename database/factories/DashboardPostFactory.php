<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DashboardPost>
 */
class DashboardPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $type = ['news', 'announcement'];
        $status = ['publish', 'draft'];
        $title = fake()->sentence();
        $timestamp = getRandomTimestamps(-365);
        $define = [
            'title' => $title,
            'content' => fake()->paragraphs(3, true),
            'status' => $status[rand(0, 1)],
            'type' => $type[rand(0, 1)],
            'user_id' => User::all()->random()->id
        ];
        return array_merge($timestamp, $define);
    }
}
