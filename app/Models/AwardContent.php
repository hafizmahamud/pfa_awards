<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'award_id',
        'subclause_id'
    ];

    public function award()
    {
        return $this->belongsTo(Award::class);
    }

    public function subclause()
    {
        return $this->belongsTo(Subclause::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
