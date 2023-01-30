<?php

namespace App\Traits;

use App\Models\Payment;
use Bancolombia\Wompi;

trait PaymentTraits
{
    private mixed $paymentMethod;
    private mixed $wompi;

    private mixed $token;
    private mixed $payment;
    private mixed $acceptanceToken;
    private mixed $tokenId;
    private array $dataPayment;

    /**
     * @throws \Throwable
     */
    public function verifyPaymentTypeMethod(string $typeMethod): static
    {
        throw_if(
            !in_array($typeMethod, Payment::TYPE_METHODS),
            new \Exception('The type method is not valid')
        );
        return $this->setPaymentMethod($typeMethod);
    }

    /**
     * @param mixed $paymentMethod
     * @return PaymentTraits
     */
    public function setPaymentMethod(mixed $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod(): mixed
    {
        return $this->paymentMethod;
    }

    public static function Wompi(): static
    {
        return new static();
    }

    public function init(): static
    {
        Wompi::initialize([
            'publicKey' => env('WOMPI_PUBLIC_KEY'),
            'privateKey' => env('WOMPI_PRIVATE_KEY'),
        ]);
        Wompi::getTokens();
        $this->acceptanceToken = Wompi::acceptance_token();
        return $this;
    }
    public function createToken(string $type, ...$args): mixed
    {
        return Wompi::{$type}($args);
    }

    public function createCardTokenize(
        string $cardNumber,
        string $cardName,
        string $cardCvv,
        string $cardExpMonth,
        string $cardExpYear
    ): static {
        $this->token = $this->createToken('createCard', [
            'card_number' => $cardNumber,
            'card_holder_name' => $cardName,
            'cvv' => $cardCvv,
            'exp_month' => $cardExpMonth,
            'exp_year' => $cardExpYear,
        ]);
        $this->tokenId = $this->token->id;
        return $this;
    }

    public function createDataPayment(
        string $amount,
        string $reference,
        string $email,
    ): static {
        $this->dataPayment = [
            'amount_in_cents' => $amount,
            'currency' => "COP",
            'reference' => $reference,
            'customer_email' => $email,
        ];
        return $this;
    }

    public function paymentCard(int $numberInstallments = 1): static
    {
        $this->payment = $this->createToken('card',
            $this->acceptanceToken,
            $this->tokenId,
            $numberInstallments,
            $this->dataPayment
        );
        return $this;
    }

    public function paymentBancolombia(string $payment_description = ''): static
    {
        $this->payment = $this->createToken('bancolombia',
            $this->acceptanceToken,
            $payment_description,
            $this->dataPayment
        );
        return $this;
    }

    public function paymentNequi(int $phone = null): static
    {
        $this->payment = $this->createToken('nequi',
            $this->acceptanceToken,
            $phone,
            $this->dataPayment
        );
        return $this;
    }

    public function paymentPSE(
        int $person_type = 0,
        string $document_type = 'CC',
        string $document = '',
        string $financial_institution_code = '',
        string $payment_description = ''
    ): static
    {
        $this->payment = $this->createToken('pse',
            $this->acceptanceToken,
            $person_type,
            $document_type,
            $document,
            $financial_institution_code,
            $payment_description,
            $this->dataPayment
        );
        return $this;
    }




}
