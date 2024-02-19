<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DocumentUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_collection_id',
        'unique_file_name',
        'file_name',
        'type',
        'path',
        'size',
    ];

    // public function documentCollection()
    // {
    //     return $this->belongsTo(DocumentCollection::class);
    // }
}
