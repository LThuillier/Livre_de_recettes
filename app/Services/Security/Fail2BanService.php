<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class Fail2BanService
{
    public function registerFailedAttempt(string $ip): void
    {
        if (! $this->enabled()) {
            return;
        }

        if ($this->isIpBanned($ip)) {
            return;
        }

        $attemptKey = $this->attemptKey($ip);
        RateLimiter::hit($attemptKey, $this->findTimeSeconds());

        if (RateLimiter::attempts($attemptKey) < $this->maxAttempts()) {
            return;
        }

        $bannedUntil = now()->addSeconds($this->banSeconds())->timestamp;
        Cache::put($this->banKey($ip), $bannedUntil, now()->addSeconds($this->banSeconds()));
        RateLimiter::clear($attemptKey);

        Log::warning('Fail2Ban: IP bannie apres trop d\'echecs de connexion.', [
            'ip' => $ip,
            'banned_until' => $bannedUntil,
        ]);
    }

    public function clearIp(string $ip): void
    {
        RateLimiter::clear($this->attemptKey($ip));
        Cache::forget($this->banKey($ip));
    }

    public function isIpBanned(string $ip): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        $bannedUntil = Cache::get($this->banKey($ip));

        return is_int($bannedUntil) && $bannedUntil > now()->timestamp;
    }

    public function banRemainingSeconds(string $ip): int
    {
        if (! $this->isIpBanned($ip)) {
            return 0;
        }

        $bannedUntil = (int) Cache::get($this->banKey($ip), 0);

        return max(0, $bannedUntil - now()->timestamp);
    }

    private function enabled(): bool
    {
        return (bool) config('fail2ban.enabled', true);
    }

    private function maxAttempts(): int
    {
        return max(1, (int) config('fail2ban.max_attempts', 5));
    }

    private function findTimeSeconds(): int
    {
        return max(60, (int) config('fail2ban.find_time_minutes', 10) * 60);
    }

    private function banSeconds(): int
    {
        return max(60, (int) config('fail2ban.ban_minutes', 30) * 60);
    }

    private function attemptKey(string $ip): string
    {
        return 'fail2ban:attempts:'.$ip;
    }

    private function banKey(string $ip): string
    {
        return 'fail2ban:ban:'.$ip;
    }
}

