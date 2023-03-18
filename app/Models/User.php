<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\{Builder, Factories\HasFactory, SoftDeletes};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Overtrue\LaravelFollow\Traits\{Followable, Follower};
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    use Follower;
    use Followable;

    protected $with = ['roles', 'city', 'tattoo_artist', 'preferences', 'socialAccounts'];

    protected static function boot()
    {
        parent::boot();
        static::created(fn ($user) => $user->assignRole('Normal'));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'locate_maps',
        'city_id',
        'address',
        'biography',
        'phone',
        'email',
        'profile_photo_path',
        'password',
        'nickname',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('expires_at', '>', Carbon::now())
            ->exists() && !$this->getPlanSubscriptions();
    }

    public function getPaymentsPending($plan): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->payments()->where('status', Payment::STATUS_PENDING)
            ->where('payment_amount',Plan::query()->find($plan)->price * 100)
            ->first();
    }

    public function getPaymentOrCreate($plan): \Illuminate\Database\Eloquent\Model|Payment|\Illuminate\Database\Eloquent\Relations\HasMany
    {
           return $this->getPaymentsPending($plan)
            ??
            $this->createPayment(Plan::query()->find($plan));
    }

    public function getPlanSubscriptions(): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->payments()->where('status', Payment::STATUS_SUCCESS)->first();
    }

    public function createPayment($plan): Payment
    {
        $payment = new Payment();
        $payment->user_id = $this->id;
        $payment->status = Payment::STATUS_PENDING;
        $payment->payment_method = 'webhook';
        $payment->payment_currency = 'COP';
        $payment->payment_amount = $plan->price * 100;
        $payment->payment_reference = Str::uuid();
        $payment->save();
        return $payment;
    }

    public function attachPayment($payment, $plan, $payload, $transaction_id): void
    {
        $payment->status = Payment::STATUS_SUCCESS;
        $payment->payload = $payload;
        $payment->payment_id = $transaction_id;
        $payment->save();
        $payment->plan()->attach([
            $plan->id => [
                'expires_at' => Carbon::now()->addDays($plan->duration_in_days),
                'user_id' => $this->id,
            ],
        ]);
    }

    public function getSubscriptionActive(): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->subscriptions()->where('expires_at', '>', Carbon::now())->select('name')->first();
    }
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

    public function getNameForDate(): string
    {
        return Str::uuid(). $this->name .'-'. $this->lastname . \Illuminate\Support\Carbon::today()->toString();
    }

    public function getRoleAttribute(): \Illuminate\Support\Collection
    {
        return $this->roles()->pluck('name');
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function rooms(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'participants');
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class, 'own_id');
    }

    public function socialAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialProfile::class);
    }

    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function tattoo_artist(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Tattoo_artist::class);
    }

    public function preferences(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Topic::class, 'topicables');
    }

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'users_plans')->withPivot(['expires_at']);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
