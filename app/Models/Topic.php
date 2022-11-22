<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $visible = [
        'name',
        'id',
        'created_at',
        'updated_at',
    ];

    public function posts(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Post::class, 'topicables');
    }

    public function tattooArtists(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Tattoo_artist::class, 'topicables');
    }
}
