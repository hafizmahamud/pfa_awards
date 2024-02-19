<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    use HasFactory;

    protected $table = 'document_collection_categories';

    protected $fillable = [
        'name',
    ];

    public function documents()
    {
        return $this->hasMany(DocumentCollection::class);
    }
}
