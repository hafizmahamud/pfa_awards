<?php

namespace Database\Factories;

use App\Models\ConditionSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConditionSubcategory>
 */
class ConditionSubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = ConditionSubcategory::class;

    public function definition()
    {
        return [
            'name' => fake()->word()
        ];
    }
}
