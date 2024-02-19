<?php

namespace App\Console\Commands;

use App\Models\Condition;
use App\Models\Jurisdiction;
use App\Models\ConditionTopic;
use Illuminate\Console\Command;
use App\Models\ConditionCategory;
use Illuminate\Support\Facades\DB;
use App\Models\ConditionSubcategory;
use Illuminate\Support\Facades\Storage;


class ConditionImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'condition:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import conditions from exported files';

    /**
     * Create a new command instance.
     * php artisan condition:import 'Condition full export.csv'
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $file  = $this->argument('file');
        $start = microtime(true);

        $this->info('Importing conditions from file: ' . basename($file));

        //$open = Storage::disk('local')->get('test.csv');
        $open = fopen(storage_path() . "/app/public/" . $file, "r");

        if (!$open) {
            $this->error('File not exists');
            return;
        }

        $count = 0;

        $this->info('Populating...');

        while (($data = fgetcsv($open)) !== FALSE) {

            if ($count == 0) {
                $count++;

                continue;
            }

            $count++;

            $this->error($count);

            $categoryName = $data[0];
            $subCategoryName  = $data[1];
            $topicName    = $data[2];
            $state        = $data[3];
            $content      = $data[4];

            if ($topicName == 'Provision') continue;

            $this->info($data[0] . " - " . $data[1] . " - " . $data[2] . " - " . $data[3] . " - " . $data[4]);

            // $condition = ConditionCategory::where('name', $category)->first();
            $condition = ConditionCategory::firstOrCreate(
                ['name'  => $categoryName],
                ['image' => strtolower($categoryName).'.svg']
            );

            $this->error('Condition ID = ' . $condition->id);

            $subCategory = ConditionSubcategory::firstOrCreate([
                'name' => $subCategoryName,
                'condition_category_id' => $condition->id,
            ]);

            $this->error('subcategory ID = ' . $subCategory->id);

            $topic = ConditionTopic::firstOrCreate([
                'name' => $topicName,
                'condition_subcategory_id' => $subCategory->id,
            ]);

            $jurisdiction = Jurisdiction::where('name', $state)->first();

            $this->error('state ID = ' . $state);
            $this->error('jurisdiction ID = ' . $jurisdiction->id);

            if ($content) {
                Condition::firstOrCreate([
                    'jurisdiction_id' => $jurisdiction->id,
                    'condition_topic_id' => $topic->id,
                    'user_id' => 1,
                    'content' => $content,
                ]);
            }

            // $this->error('jurisdictions ID = ' . $jurisdictions[$data[6]]);
        }

        $this->info('Done populating data');
        return 0;
    }
}
