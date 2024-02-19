<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'user_id'
    ];

    protected $appends = [
        'edit_url'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function jurisdictions()
    {
        return $this->belongsToMany(Jurisdiction::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    public function posts()
    {
        return $this->hasMany(ProjectPost::class);
    }

    public function posts_with_author_and_tags()
    {
        return $this->posts()->with(['author', 'tags']);
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
        return route('projects.edit', ['project' => $this->id]);
    }

    // public function projectmembers()
    // {
    //     return $this->hasMany(Project::class, 'project_user')->with('projects');
    // }
}
