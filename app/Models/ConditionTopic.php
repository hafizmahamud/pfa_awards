<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConditionTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'condition_subcategory_id'
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function conditions()
    {
        return $this->hasMany(Condition::class);
    }

    public function conditions_by_jurisdictions(Request $request)
    {
        return $this->hasMany(Condition::class)
            ->when($request->input('jurisdictions'), function ($query) use ($request) {
                $query->whereIn('jurisdiction_id', $request->input('jurisdictions'));
            });
    }

    public function conditionSubcategory()
    {
        return $this->belongsTo(ConditionSubcategory::class);
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
