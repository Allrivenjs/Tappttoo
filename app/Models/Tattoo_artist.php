<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tattoo_artist extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_company',
        'base_price',
        'price_per_hour',
        'instagram',
        'status',
        'user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function topics(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Topic::class, 'topicables');
    }
}
