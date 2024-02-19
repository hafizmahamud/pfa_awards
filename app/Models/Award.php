<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Award extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'jurisdiction_id',
        'start_date',
        'end_date',
        'user_id',
        'pdf'
    ];

    protected $appends = [
        // 'content'
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }

    public function award_contents()
    {
        return $this->hasMany(AwardContent::class);
    }

    public function award_contents_with_subclause()
    {
        return $this->award_contents()->with('subclause');
    }

    public function getContentAttribute()
    {
        // build content
        $award_contents = $this->award_contents_with_subclause()->get();

        $content = [];

        foreach ($award_contents as $award_content) {
            $clause = $award_content->subclause->clause;
            if (!isset($content[$clause->id])) {
                $content[$clause->id] = [
                    'id' => $clause->id,
                    'title' => $clause->title
                ];
            }

            $content[$clause->id]['subclauses'][$award_content->subclause->id] = [
                'id' => $award_content->subclause->id,
                'title' => $award_content->subclause->title,
                'content' => $award_content->content
            ];
        }

        return $content;
    }

    public function award_contents_filtered($filter = [])
    {
        $award_content = $this->hasMany(AwardContent::class);
        if (count($filter)) {
            foreach ($filter as $key => $value) {
                switch ($key) {
                    case 'tag':
                        $award_content->whereHas('tags', function ($award_content) use ($value) {
                            $award_content->whereId($value);
                        });
                        break;

                    case 'clause':
                        $clause = Clause::find($value);
                        $subclauses = Subclause::whereBelongsTo($clause)->get();
                        $award_content->whereBelongsTo($subclauses);
                        break;

                    default:
                        # code...
                        break;
                }
            }
        }

        return $award_content->get();
    }

    public function getContent($filter = [])
    {
        $award_contents = $this->award_contents_filtered($filter);
        $content = [];

        foreach ($award_contents as $award_content) {
            $clause = $award_content->subclause->clause;
            if (!isset($content[$clause->id])) {
                $content[$clause->id] = [
                    'id' => $clause->id,
                    'title' => $clause->title
                ];
            }

            $content[$clause->id]['subclauses'][$award_content->subclause->id] = [
                'id' => $award_content->subclause->id,
                'title' => $award_content->subclause->title,
                'content' => $award_content->content
            ];
        }

        return $content;
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
