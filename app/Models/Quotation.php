<?php

namespace App\Models;

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
        'status' => 'required|in:pending,accepted,rejected',
        'user_id' => 'required|exists:users,id',
        'room_id' => 'required|exists:rooms,id',
        'price' => 'nullable|numeric',
    ];

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

    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class);
    }

}
