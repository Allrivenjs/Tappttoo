<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Payment::all());
    }


    /**
     * @throws \Throwable
     */
    public function CardPayment(Request $request)
    {
        $validate = $request->validate([
            'type_method' => 'required|string|max:255',
            'type_card' => 'required|string|max:255',
            'card_number' => 'required|string|max:255',
            'card_name' => 'required|string|max:255',
            'card_cvv' => 'required|string|max:255',
            'card_exp_month' => 'required|string|max:255',
            'card_exp_year' => 'required|string|max:255',
            'amount' => 'required|string|max:255',
        ]);
        $email = $request->user()->email;
        $reference = Str::uuid();
        $payment = Payment::card($validate, [
            'amount' => $validate['amount'],
            'reference' => $reference,
            'email' => $email,
        ]);




    }

}
