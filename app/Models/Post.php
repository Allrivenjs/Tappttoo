<?php

namespace App\Models;

use Carbon\Carbon;
use Conner\Likeable\Likeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;
    use Likeable;

    protected $fillable = ['slug', 'body', 'user_id'];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::addGlobalScope('hiddenPost', fn (Builder $builder) =>
        $builder->whereDoesntHave('hiddenByUsers', fn (Builder $builder) =>
          $builder->where('user_id', Auth::guard('api')->id()
        )));
    }

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

    public function hiddenByUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hidden_post_by_user');
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

    public function images(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function taggableUsers(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(User::class, 'taggable');
    }
}
