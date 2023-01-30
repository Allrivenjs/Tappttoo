<?php

namespace App\Models;

use App\Traits\PaymentTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    use PaymentTraits;

    protected $fillable = [
        'user_id',
        'type_method',
        'type_card',
        'card_number',
        'card_name',
        'card_cvv',
        'card_exp_month',
        'card_exp_year',
        'payload',
    ];

    final public const validationRules = [
        'type_method' => 'required|string|max:255',
        'type_card' => 'required|string|max:255',
        'card_number' => 'required|string|max:255',
        'card_name' => 'required|string|max:255',
        'card_cvv' => 'required|string|max:255',
        'card_exp_month' => 'required|string|max:255',
        'card_exp_year' => 'required|string|max:255',
    ];

    final const TYPE_METHODS = [
        'TYPE_METHOD_CARD' => 'CARD',
        'TYPE_METHOD_NEQUI' => 'NEQUI',
        'TYPE_METHOD_PSE' => 'PSE',
    ];


    /**
     * @throws \Throwable
     */
    public static function card(mixed $validated, mixed $data): Payment
    {
      return self::Wompi()->init()->verifyPaymentTypeMethod(self::TYPE_METHODS['TYPE_METHOD_CARD'])
          ->createCardTokenize(
            $validated['card_number'],
            $validated['card_name'],
            $validated['card_cvv'],
            $validated['card_exp_month'],
            $validated['card_exp_year']
          )->createDataPayment(
              $data['amount'],
              $data['reference'],
              $data['email'],
          )->paymentCard();
    }


    public static function create(mixed $validated): Model|\Illuminate\Database\Eloquent\Builder
    {
        return Payment::query()->create($validated);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
