<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Condition;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Jurisdiction;
use App\Models\ConditionTopic;
use App\Models\ConditionCategory;
use Illuminate\Http\UploadedFile;
use App\Models\ConditionSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Condition>
 */
class ConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Condition::class;

    public function definition()
    {

        $file = UploadedFile::fake()->create('test-' . date('Y-m-d') . '-' . date('H-i-s') . '.pdf')->store('conditions', 'public');
        return [
            'content' => fake()->paragraphs(3, true),
            'user_id' => User::all()->random()->id,
            'attachment' => $file
        ];
    }
}
