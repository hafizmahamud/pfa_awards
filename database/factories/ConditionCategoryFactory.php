<?php

namespace Database\Factories;

use App\Models\ConditionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConditionCategory>
 */
class ConditionCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = ConditionCategory::class;

    public function definition()
    {
        return [
            'name' => fake()->word()
        ];
    }
}
