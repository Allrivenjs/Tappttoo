<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{

    /**
     * @throws \Throwable
     */
    public function webhook(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
        ]);
        $this->verifySubscription($user = User::query()->with(['tattoo_artist'])->find($request->get('user_id')));
        $avatar = $user->profile_photo_path;
        $avatar = !$avatar  && $user->socialAccounts->count() > 0 ?
                    $user->socialAccounts->first()->avatar
                    :  $this->getImage('public', $avatar);
        return view('payment', [
            'user' => $user,
            'payment' => $user->getPaymentOrCreate($request->get('plan_id')),
            'plan' => Plan::query()->find($request->get('plan_id')),
            'avatar'=> env('APP_URL') === 'https://tappttoo.shop' ?
               $avatar ?? "https://images.pexels.com/photos/2379005/pexels-photo-2379005.jpeg?auto=compress&amp;cs=tinysrgb&amp;dpr=2&amp;w=500"
                : $user->profile_photo_path
        ]);
    }

    protected function verifySubscription($user): void
    {
        abort_if($user->hasActiveSubscription(), Response::HTTP_UNAUTHORIZED, 'Ya tienes una suscripción activa');
    }

    public function paymentSuccess(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('payment-success');
    }

    public function paymentConfirm(Request $request)
    {
        $user = User::query()->find($request->get('user_id'));
        $payment = Payment::query()->where('payment_reference', $request->get('reference'))->first();
        $plan = Plan::query()->find($request->get('plan_id'));
        $user->attachPayment($payment, $plan, $request->get('transaction'), $request->get('transaction_id'));
        return response([
            'message' => 'Pago confirmado',
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    public function getPlans(Request $request): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        return response( Plan::all() );
    }
}
