<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\DocumentCollection;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentCollection>
 */
class DocumentCollectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = DocumentCollection::class;

    public function definition()
    {
        $title = fake()->sentence();
        $timestamp = getRandomTimestamps(-365);
        $define = [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'user_id' => User::all()->random()->id,
        ];
        return array_merge($timestamp, $define);
    }
}
