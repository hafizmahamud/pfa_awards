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

class DatabaseSeeder extends Seeder
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

        \App\Models\User::factory(20)->create();

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

        \App\Models\Tag::factory(10)->create();

        \App\Models\Post::factory(30)
            ->create()
            ->each(function ($post, $key) {
                $jurisdictions = Jurisdiction::all()->random(rand(1, 3));
                $post->jurisdictions()->saveMany($jurisdictions);

                $tags = Tag::all()->random(rand(1, 4));
                $post->tags()->saveMany($tags);
            });

        \App\Models\DocumentCollection::factory(30)
            ->create()
            ->each(function ($documentCollection, $key) {
                $jurisdictions = Jurisdiction::all()->random(rand(1, 3));
                $documentCollection->jurisdictions()->saveMany($jurisdictions);

                $tags = Tag::all()->random(rand(1, 4));
                $documentCollection->tags()->saveMany($tags);
            });

        \App\Models\Project::factory(30)
            ->create()
            ->each(function ($project, $key) {
                $jurisdictions = Jurisdiction::all()->random(rand(1, 3));
                $project->jurisdictions()->saveMany($jurisdictions);

                $tags = Tag::all()->random(rand(1, 4));
                $project->tags()->saveMany($tags);

                $users = User::all()->random(rand(5, 7));
                $project->members()->saveMany($users);

                \App\Models\ProjectPost::factory(rand(5, 10))
                    ->create([
                        'project_id' => $project->id
                    ])
                    ->each(function ($project_post, $key) {
                        $tags = Tag::all()->random(rand(1, 4));
                        $project_post->tags()->saveMany($tags);
                    });
            });

        $clauses = [
            'GENERAL' => [
                'No Further Claims',
                'Definitions',
                'Commitment to Professional and Ethical Conduct',
                'Anti-Discrimination',
                'Inspection of Award',
                'Existing Privileges',
                'Salary Packaging Arrangements, Including Salary
                Sacrifice to Superannuation',
                'Deduction of Police Association of New South Wales
                Membership Fees',
                'Travelling Allowances',
                'Provision of Quarters',
                'Remote Area - Living Allowances',
                'Part Time Employment',
                'Local Arrangements'
            ],
            'LEAVE' => [
                'Leave Generally',
                'Applications for Leave',
                'Annual Leave',
                'Purchased Leave',
                'Extended Leave',
                'Sick Leave',
                'Sick Leave to Care for a Family Member',
                'Maternity Leave',
                'Parental Leave',
                'Adoption Leave',
                'Right to Request Additional Maternity, Parental or
                Adoption Leave',
                'Communication During Maternity, Parental or
                Adoption Leave',
                'Accrual of Leave while on Maternity, Parental or
                Adoption Leave',
                'Incremental Progression while on Maternity Leave,
                Adoption Leave or Parental Leave',
                'Family and Community Service Leave',
                'Leave Without Pay',
                'Military Leave',
                'Special Leave',
                'Leave for Matters Arising from Domestic Violence'
            ],
            'ASSOCIATION ACTIVITIES' => [
                'Association Activities regarded as Special Leave',
                'Association Activities Regarded as On Duty',
                'Association Training Courses'
            ],
            'NON-COMMISSIONED OFFICERS' => [
                'Salaries (Other than Detectives and Police
                Prosecutors)',
                'Salaries (Detectives)',
                'Salaries (Police Prosecutors)',
                'Loading',
                'Leading Senior Constables',
                'Special Duties Allowance',
                'Forensic Services Group Expert Allowance',
                'Regional Target Action Group (TAG)/Anti-Theft Unit',
                'Allowance',
                'Tactical Operations Unit Allowance',
                'Professional/Academic Qualification Allowance',
                'Special Operations Allowance',
                'On Call Allowances',
                'Hours of Duty',
                'Shift Allowance',
                'Meals',
                'Overtime',
                'Recall to Duty',
                'Court Attendance Between Shifts',
                'Lockup Keepers’ or Sole Detective’s Recall',
                'On Call Detectives Recall',
                'On Call Telephone Recall (Other than Detectives)',
                'Penalty Provisions Not Cumulative',
                'Travelling Time',
                'Time in Lieu of Payment of Travelling Time and',
                'Overtime',
                'Relieving Duty',
                'Allowance for Officers Relieving into a Detectives',
                'Position at Rank',
                'Public Holidays',
                'Competency Based Incremental Progression',
                'Provision of Uniform',
                'Air Travel',
                'Lockers',
                'Work of a Menial Nature'
            ],
            'COMMISSIONED OFFICERS' => [
                'Salaries',
                'Hours of Duty',
                'Fixed Term Appointment',
                'Non-Renewal Benefit',
                'Competency Based Incremental Progression',
                'Relieving Duty',
                'Travelling Time'
            ],
            'DISPUTES/GRIEVANCE SETTLEMENT PROCEDURE' => [
                'Disputes/Grievance Settlement Procedure'
            ],
            'TRANSFERRED OFFICERS ENTITLEMENTS & COMPENSATION' => [
                'Definitions',
                'Eligibility for Entitlements under this Section',
                'Officers Appointed under Section 66A & 67 of the',
                'Police Act',
                'Special Remote Locations',
                'Notice of Transfer',
                'Transfer Leave',
                'Cost of Temporary Accommodation',
                'Excess Rent Assistance',
                'Removal Costs',
                'Storage of Furniture',
                'Cost of Personal Transport',
                'Compensation for Depreciation and Disturbance',
                'Education of Children',
                'Conveyancing and Other Costs',
                'Refund of Stamp Duty, Registration of Transfer and',
                'Mortgage Fees',
                'Incidental Costs Upon Change of Residence',
                'Relocation on Retirement',
                'Existing Benefits'
            ],
            'AREA, INCIDENCE AND DURATION' => [
                'Area, Incidence and Duration'
            ]
        ];

        collect($clauses)->each(function ($subclauses, $name) {
            $clause = \App\Models\Clause::create(['title' => $name]);
            collect($subclauses)->each(function ($name) use ($clause) {
                \App\Models\Subclause::create([
                    'clause_id' => $clause->id,
                    'title' => $name
                ]);
            });
        });

        // \App\Models\Clause::factory(8)
        //     ->create()
        //     ->each(function ($clause, $key) {
        //         \App\Models\Subclause::factory(rand(2, 3))
        //             ->create([
        //                 'clause_id' => $clause->id
        //             ]);
        //     });

        Jurisdiction::all()->each(function ($jurisdiction, $key) {
            \App\Models\Award::factory(rand(2, 3))
                ->create([
                    'jurisdiction_id' => $jurisdiction->id
                ])->each(function ($award, $key) {
                    \App\Models\Clause::all()
                        ->each(function ($clause, $key) use ($award) {
                            $clause->subclauses->each(function ($subclause, $key) use ($award) {
                                \App\Models\AwardContent::factory(1)
                                    ->create([
                                        'award_id' => $award->id,
                                        'subclause_id' => $subclause->id
                                    ]);
                            });
                        });
                });
        });

        $this->call(BasicAdminPermissionSeeder::class);

        \App\Models\AwardContent::all()->each(function ($award_content, $key) {
            $tags = Tag::all()->random(rand(1, 4));
            $award_content->tags()->saveMany($tags);
        });

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

        \App\Models\ConditionCategory::all()->each(function ($category, $key) {
            $subcategories = \App\Models\ConditionSubcategory::factory(rand(5, 7))
                ->create([
                    'condition_category_id' => $category->id
                ])
                ->each(function ($subcategory, $key) {
                    $topics = \App\Models\ConditionTopic::factory(rand(2, 4))
                        ->create([
                            'condition_subcategory_id' => $subcategory->id
                        ])
                        ->each(function ($topic, $key) {
                            $tags = Tag::all()->random(rand(1, 4));
                            $topic->tags()->saveMany($tags);
                        });
                    // $subcategory->topics()->saveMany($topics);
                });

            // $category->subcategories()->saveMany($subcategories);
        });

        \App\Models\Jurisdiction::all()->each(function ($jurisdiction, $key) {
            \App\Models\ConditionTopic::all()->each(function ($topic, $key) use ($jurisdiction) {
                \App\Models\Condition::factory(1)->create([
                    'jurisdiction_id' => $jurisdiction->id,
                    'condition_topic_id' => $topic->id,
                ]);
            });
        });

        \App\Models\DashboardPost::factory(10)->create();
    }
}
