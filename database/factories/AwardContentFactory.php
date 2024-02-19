<?php

namespace Database\Factories;

use App\Models\AwardContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AwardContent>
 */
class AwardContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = AwardContent::class;

    public function definition()
    {
        $timestamp = getRandomTimestamps(-365);
        $define = [
            'content' => fake()->paragraphs(1, true)
        ];
        return array_merge($timestamp, $define);
    }
}
