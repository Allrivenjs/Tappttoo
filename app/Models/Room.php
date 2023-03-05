<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];
    protected $with = ['users'];
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

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->messages()->orderByDesc('created_at');
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants');
    }
    public function quotations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function lastQuotation(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->quotations()->orderByDesc('created_at');
    }
}
