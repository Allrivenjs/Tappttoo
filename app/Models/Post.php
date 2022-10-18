<?php

namespace App\Models;

use Carbon\Carbon;
use Conner\Likeable\Likeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, Likeable;


    protected $fillable = ['slug', 'body', 'user_id'];

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function getUpdatedAtAttribute($value): string
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    public function comments_lasted(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->comments()->orderBy('created_at', 'desc')->take(3);
    }

    public function topics(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Topic::class, 'topicables');
    }

}
