<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subclause extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'clause_id'
    ];

    public function clause()
    {
        return $this->belongsTo(Clause::class);
    }

    public function award_contents()
    {
        return $this->hasMany(AwardContent::class);
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
