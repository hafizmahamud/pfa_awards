<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Award;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Award>
 */
class AwardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Award::class;

    public function definition()
    {
        $title = fake()->sentence();
        $file = UploadedFile::fake()->create('test-' . date('Y-m-d') . '-' . date('H-i-s') . '.pdf')->store('pdfs', 'public');
        $start_date = fake()->dateTimeBetween('-5 years', '-1 year');
        $end_date = Carbon::create($start_date)->addYear()->format('Y-m-d H:i:s');

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'user_id' => User::all()->random()->id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'pdf' => $file
        ];
    }
}
