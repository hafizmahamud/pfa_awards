<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'user_id',
        'category_id'
    ];

    protected $appends = [
        'edit_url'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function jurisdictions()
    {
        return $this->belongsToMany(Jurisdiction::class);
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d/m/Y');
    }

    public function getEditUrlAttribute()
    {
        return route('document-collections.edit', ['document_collection' => $this->id]);
    }

    public function documents()
    {
        return $this->hasMany(DocumentUpload::class, 'document_collection_id');
    }
}
