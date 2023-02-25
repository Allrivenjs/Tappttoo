<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'body_part',
        'width',
        'height',
        'option',
        'status',
        'user_id',
        'room_id',
        'price',
    ];

    final public const validationRules = [
        'body_part' => 'required|string|max:255',
        'width' => 'required|numeric',
        'height' => 'required|numeric',
        'option' => 'required|in:black_and_white,color',
        'user_id' => 'required|exists:users,id',
        'room_id' => 'required|exists:rooms,id',
        'price' => 'nullable|numeric',
        'images' => 'nullable|array|max:4',
        'images.*' => 'nullable|image',
    ];

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function getUpdatedAtAttribute($value)
    {
        if ($value == null) {
            return;
        }

        return Carbon::parse($value)->diffForHumans();
    }

    public function getDeletedAtAttribute($value)
    {
        if ($value == null) {
            return;
        }

        return Carbon::parse($value)->diffForHumans();
    }

    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        self::creating(fn (Quotation $quotation) => $quotation->setAttribute('status', self::STATUS_PENDING));
        self::addGlobalScope('images', fn (Builder $builder) => $builder->with('images'));
    }

    final const STATUS_PENDING = 'pending';
    final const STATUS_ACCEPTED = 'accepted';
    final const STATUS_REJECTED = 'rejected';

    public static function create(mixed $validated): Model|\Illuminate\Database\Eloquent\Builder
    {
        return Quotation::query()->create($validated);
    }


    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

}
