<?php

namespace Database\Factories;

use App\Models\ConditionTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConditionTopic>
 */
class ConditionTopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = ConditionTopic::class;

    public function definition()
    {
        return [
            'name' => fake()->word()
        ];
    }
}
