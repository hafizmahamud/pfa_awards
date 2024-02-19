<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConditionSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'condition_category_id'
    ];

    public function conditionCategory()
    {
        return $this->belongsTo(ConditionCategory::class);
    }

    public function conditionTopics()
    {
        return $this->hasMany(ConditionTopic::class);
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
