<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Models\Jurisdiction;
use App\Models\DocumentUpload;
use Illuminate\Console\Command;
use App\Models\DocumentCategory;
use App\Models\DocumentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Documents\FileHasUploaded;
use App\Http\Controllers\DocumentCollectionController;


class DocumentImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import documents from exported files';

    /**
     * Create a new command instance.
     * php artisan document:import 'Document full export.csv'
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

        $this->info('Importing document from file: ' . basename($file));

        //$open = Storage::disk('local')->get('test.csv');
        $open = fopen(storage_path() . "/app/public/" . $file, "r");

        if (!$open) {
            $this->error('File not exists');
            return;
        }

        $count = 0;

        $this->info('Populating...');

        while (($data = fgetcsv($open)) !== FALSE) {

            if ($count == 0) { // skip header
                $count++;

                continue;
            }

            $count++;

            $this->error($count);

            $categoryName   = $data[0];
            $title          = $data[1];
            $publishedDate  = $data[2]; //no column in dbase
            $visibility     = $data[3]; //no column in dbase
            $fileName     = $data[5];
            $fileType       = $data[6];
            $content        = $data[7]; //description

            $this->info($data[0] . " - " . $data[1] . " - " . $data[2] . " - " . $data[3] . " - " . $data[4]);

            $category = DocumentCategory::firstOrCreate(
                ['name'  => $categoryName],
            );

            $state = 'Federal'; // set default to federal as no jurisdiction in old site

            $jurisdiction = Jurisdiction::where('name', $state)->first();

            $this->error('state ID = ' . $state);
            $this->error('jurisdiction ID = ' . $jurisdiction->id);

            $jurisdictions[] = $jurisdiction->id;

            $documentCollection = DocumentCollection::firstOrCreate([
                'title'       => $title,
                'slug'        => (new DocumentCollectionController)->makeSlugFromTitle($title),
                'content'     => $content,
                'user_id'     => 1,
                'category_id' => $category->id,
            ]);

            $documentCollection->jurisdictions()->sync($jurisdictions);

            $uniqueFileName = time() . '_' . Str::replace(':', '-', $fileName) . '.' . $fileType;

            if ($this->checkDirectory(storage_path('app/public/documents'))) {
                //$filePath = $file->storeAs('documents', $uniqueFileName);
                // $downloadPath = $attachment;
                // Storage::disk('local')->put('documents/' . $uniqueFileName, file_get_contents($downloadPath));
                Storage::copy('library_files/' . $fileName . '.' . $fileType, 'documents/' . $uniqueFileName);

                //$filePath = Storage::path('documents/' . $uniqueFileName);
                $filePath = 'documents/' . $uniqueFileName;
            }

            $fileContent['name'] = Str::replace(':', '-', $title).".$fileType";
            $fileContent['size'] = ($fileType == 'ptpx') ? 260542 : Storage::disk('local')->size("$filePath"); //error readigngptpx file size
            //$fileContent['type'] = Storage::mimeType($filePath);
            $fileContent['type'] = 'application/' . $fileType;

            dispatch(new FileHasUploaded($documentCollection, $uniqueFileName, $filePath, $fileContent));
        }

        $this->info('Done populating data');
        return 0;
    }

    /**
     * Check if directory exist or not and create if not
     *
     * @param string $path
     * @return void
     */
    private function checkDirectory($path) // copy function from DocumentCollectionController because it is private
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);

            return true;
        }

        return true;
    }

}
