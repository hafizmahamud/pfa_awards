<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConditionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image'
    ];

    public function conditionSubcategories()
    {
        return $this->hasMany(ConditionSubcategory::class);
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
