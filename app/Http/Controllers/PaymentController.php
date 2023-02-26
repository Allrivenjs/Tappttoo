<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    /**
     * @throws \Throwable
     */
    public function webhook(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {

        $user= User::query()->with(['tattoo_artist'])->first();
        return view('payment', [
            'user' => $user,
            'avatar'=> env('APP_URL') === 'https://tappttoo.shop' ? $this->getImage('public', $user->profile_photo_path) : $user->profile_photo_path
        ]);
    }
}
