<?php

namespace App\Http\Middleware;

use App\Services\Security\Fail2BanService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Fail2BanMiddleware
{
    public function __construct(private readonly Fail2BanService $fail2BanService)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        if ($ip && $this->fail2BanService->isIpBanned($ip)) {
            $seconds = $this->fail2BanService->banRemainingSeconds($ip);

            throw ValidationException::withMessages([
                'email' => [trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => max(1, (int) ceil($seconds / 60)),
                ])],
            ])->status(Response::HTTP_TOO_MANY_REQUESTS);
        }

        return $next($request);
    }
}

