<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Condition;
use App\Models\Jurisdiction;
use App\Models\ConditionTopic;
use Illuminate\Database\Seeder;
use App\Models\ConditionCategory;
use Illuminate\Http\UploadedFile;
use App\Models\ConditionSubcategory;
use App\Models\ProjectPost;
use Illuminate\Support\Facades\Hash;

class ProductionDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'dev@osky.com.au',
        //     'password' => Hash::make('password')
        // ]);

        $jurisdiction = collect([
            'Federal',
            'NSW',
            'VIC',
            'QLD',
            'SA',
            'WA',
            'TAS',
            'NT',
            'NZ'
        ]);

        $jurisdiction->each(function ($item, $key) {
            \App\Models\Jurisdiction::factory()->create([
                'name' => $item
            ]);
        });

        $this->call(BasicAdminPermissionSeeder::class);

        $conditionCategories = collect([
            ['Discipline', 'discipline.svg'],
            ['Employment', 'employment.svg'],
            ['Deployment', 'deployment.svg'],
            ['Uniform and equipment', 'uniform.svg'],
            ['Termination', 'termination.svg'],
            ['OH&S', 'ohs.svg'],
            ['Superannuation', 'superannuation.svg'],
            ['COVID-19', 'covid.svg'],
        ]);

        $conditionCategories->each(function ($item, $key) {
            \App\Models\ConditionCategory::factory()->create([
                'name'  => $item[0],
                'image' => $item[1],
            ]);
        });
    }
}
