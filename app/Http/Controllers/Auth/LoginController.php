<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Fail2BanMiddleware;
use App\Services\Security\Fail2BanService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        sendLoginResponse as protected traitSendLoginResponse;
        sendFailedLoginResponse as protected traitSendFailedLoginResponse;
    }

    protected int $maxAttempts;

    protected int $decayMinutes;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxAttempts = max(1, (int) config('fail2ban.max_attempts', 5));
        $this->decayMinutes = max(1, (int) config('fail2ban.find_time_minutes', 10));

        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
        $this->middleware(Fail2BanMiddleware::class)->only('login');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        app(Fail2BanService::class)->registerFailedAttempt((string) $request->ip());

        return $this->traitSendFailedLoginResponse($request);
    }

    protected function sendLoginResponse(Request $request)
    {
        app(Fail2BanService::class)->clearIp((string) $request->ip());

        return $this->traitSendLoginResponse($request);
    }
}
