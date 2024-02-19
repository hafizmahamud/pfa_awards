<?php

namespace App\Jobs\Documents;

use App\Models\DocumentCollection;
use App\Models\DocumentUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FileHasUploaded implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public DocumentCollection $documentCollection,
        public string $uniqueFileName,
        public string $filePath,
        public array $file
    )
    {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DocumentUpload::create([
            'document_collection_id' => $this->documentCollection->id,
            'unique_file_name' => $this->uniqueFileName,
            'file_name' => $this->file['name'],
            'type' => $this->file['type'],
            'path' => $this->filePath,
            'size' => $this->file['size'],
        ]);
    }
}
