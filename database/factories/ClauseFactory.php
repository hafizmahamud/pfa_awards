<?php

namespace Database\Factories;

use App\Models\Clause;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clause>
 */
class ClauseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Clause::class;

    public function definition()
    {
        $title = fake()->sentence();
        return [
            'title' => $title
        ];
    }
}
