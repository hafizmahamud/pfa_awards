<?php

namespace Database\Factories;

use App\Models\Subclause;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subclause>
 */
class SubclauseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Subclause::class;

    public function definition()
    {
        $title = fake()->sentence();
        return [
            'title' => $title
        ];
    }
}
