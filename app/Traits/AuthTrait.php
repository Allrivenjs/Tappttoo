<?php

namespace App\Traits;

use App\Models\SocialProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rules;

trait AuthTrait
{
    private $token;
    protected function authWeb(): \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
    {
        return auth()->guard('web');
    }

    protected function authApi(): \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
    {
        return auth()->guard('api');
    }

    public function redirectToProvider($driver = null, Request $request): \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        dd($request->all(), $driver);
        return Socialite::driver($driver ?? 'apple')->redirect();
    }

    public function redirectToCallbackSocialProvider($driver, $other = false, $token = null)
    {
        abort_unless(array_key_exists($driver, Config::get('services')), Response::HTTP_NOT_FOUND, 'Driver not found');
        $method = 'handle'.ucfirst($driver);
        $other ? $method .= 'OtherCallback' : $method .= 'Callback';
        $this->token = $token;
        if (!method_exists($this, $method)) {
            $this->handleMissingCallbackMethod();
        }
        return $this->{$method}();
    }

    private function handleFacebookOtherCallback(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response($this->handleSocialiteMethodLogin('facebook', true));
    }

    private function handleGoogleOtherCallback(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response($this->handleSocialiteMethodLogin('google', true));
    }

    private function handleAppleOtherCallback(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response($this->handleSocialiteMethodLogin('apple', true));
    }

    private function handleTwitterOtherCallback(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response($this->handleSocialiteMethodLogin('twitter', true));
    }


    private function handleFacebookCallback(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response($this->handleSocialiteMethodLogin('facebook'));
    }

    private function handleGoogleCallback(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response($this->handleSocialiteMethodLogin('google'));
    }

    private function handleAppleCallback(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response($this->handleSocialiteMethodLogin('apple'));
    }
    private function handleTwitterCallback(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response($this->handleSocialiteMethodLogin('twitter'));
    }

    private function handleSocialiteMethodLogin($provider, $other = false): array
    {
        $socialite = Socialite::driver($provider);
        $socialUser = !$other ? $socialite->stateless()->user() : $socialite->userFromToken($this->token);
        list($user, $created) = $this->createUserProvider($socialUser, $provider);
        return $this->loginMethod($user);
    }

    #[NoReturn] public function findOrCreateUser($socialUser)
    {
        dd($socialUser, $socialUser->getName(), $socialUser->getEmail(), $socialUser->getId());
        return User::query()->whereHas(
            'socialAccounts',
            fn (Builder $q) => $q->where('social_id', $socialUser->getId())
        )->orWhere('email', $socialUser->getEmail())
            ->firstOr(fn () => $this->createUser([
            'full_name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
        ]));
    }

    public function createUserProvider($socialUser, string $provider): array
    {
        return [
            $user = $this->findOrCreateUser($socialUser),
            SocialProfile::query()->with('user')
                ->where('social_id', $socialUser->getId())
                ->firstOr(fn () => SocialProfile::query()->create([
                    'social_id' => $socialUser->getId(),
                    'nickname' => $socialUser->getName(),
                    'avatar' => $socialUser->getAvatar(),
                    'driver' => $provider,
                    'data' => json_encode($socialUser),
                    'user_id' => $user->id
                ])),
        ];
    }

    public function createUser($data): \Illuminate\Database\Eloquent\Model|Builder
    {
        $names = explode(' ', $data['full_name']);
        return User::query()->create([
            'name' => $names[0] ?? $data['name'] ?? null,
            'lastname' => $names[1] ?? $data['lastname'] ?? null,
            'email' => $data['email'],
        ]);
    }

    private function handleMissingCallbackMethod(): void
    {
        abort(Response::HTTP_NOT_FOUND, 'Method not found');
    }

    public function handleLoginMethod(Request $request): array
    {
        $request->validate($this->rulesLogin());
        abort_unless(
            $this->authWeb()->attempt($request->only('email', 'password')),
            Response::HTTP_FORBIDDEN,
            'Invalid credentials'
        );
        $tokenResult = $this->authWeb()->user()->createToken('authToken');
        $token = $tokenResult->token;
        $this->remember_me($token, $request);
        return $this->returnDataUser($this->authWeb()->user(), $tokenResult);
    }

    public function handleRegisterMethod(Request $request): array
    {
        $validatedData = $request->validate($this->rulesRegister());
        $validatedData['password'] = Hash::make($request->input('password'));
        try {
            $user = User::query()->create($validatedData);
        } catch (QueryException $e) {
            abort($e->getCode(), $e->getMessage());
        }
        $this->loginMethod($user);
        $tokenResult = $user->createToken('authToken');
        return $this->returnDataUser($user, $tokenResult);
    }

    public function loginMethod(User | Model $user): array
    {
        $this->authWeb()->login($user);
        $token = $user->createToken('authToken');
        return $this->returnDataUser($user, $token);
    }

    private function returnDataUser($user, $token): array
    {
        return [
            'user' => $user,
            'roles' => $user->role,
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->token->expires_at)->toDateTimeString(),
        ];
    }

    public function handleLogoutMethod(Request $request): string
    {
        $request->user()->token()->revoke();
        return 'Logout successfully';
    }

    protected function remember_me($token, Request $request): void
    {
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
        }
    }

    protected function rulesLogin(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    protected function rulesRegister(): array
    {
        return [
            'email' => 'required|email|unique:users',
            'password' => ['required', Rules\Password::defaults()],
        ];
    }

    protected function handleSendEmailToResetPasswordMethod(Request $request): array
    {
        $request->validate($this->rulesResetPassword());
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? ['status' => __($status)]
            : ['email' => __($status)];
    }

    protected function handleResetPasswordMethod(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
    public function rulesResetPassword(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function handleUpdatePasswordMethod(Request $request): string
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', Rules\Password::defaults()],
        ]);
        $user = $request->user();
        abort_unless(Hash::check($request->input('current_password'), $user->password), Response::HTTP_FORBIDDEN, 'Invalid credentials');
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return 'Password updated successfully';
    }
}
