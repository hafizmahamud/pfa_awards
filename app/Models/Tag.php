<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'order'
    ];

    // this relationship will only return one level of child items
    public function tags()
    {
        return $this->hasMany(Tag::class, 'parent_id');
    }

    // This is method where we implement recursive relationship
    public function childTags()
    {
        return $this->hasMany(Tag::class, 'parent_id')->with('tags');
    }
}
