<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function webhook(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {

        $user= User::query()->with(['tattoo_artist'])->first();
        dd($user);
        return view('payment', [
            'user' => $user
        ]);
    }
}
