<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'jurisdiction_id',
        'condition_topic_id',
        'topic_id',
        'content',
        'attachment',
        'user_id'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function conditionTopic()
    {
        return $this->belongsTo(ConditionTopic::class);
    }

    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d/m/Y');
    }

    public function attachmentUrl()
    {
        return $this->attachment ? Storage::url($this->attachment) : null;
    }
}
