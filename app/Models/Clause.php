<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clause extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'award_id'
    ];

    public function award()
    {
        return $this->belongsTo(Award::class);
    }

    public function subclauses()
    {
        return $this->hasMany(Subclause::class);
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
}
