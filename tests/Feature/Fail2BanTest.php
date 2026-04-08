<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Security\Fail2BanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Fail2BanTest extends TestCase
{
    use RefreshDatabase;

    public function test_ip_est_bannie_apres_plusieurs_echecs_de_connexion(): void
    {
        config([
            'fail2ban.enabled' => true,
            'fail2ban.max_attempts' => 3,
            'fail2ban.find_time_minutes' => 5,
            'fail2ban.ban_minutes' => 10,
        ]);

        User::factory()->create([
            'email' => 'fail2ban@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $ip = '10.10.10.10';

        for ($i = 0; $i < 3; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])->post('/login', [
                'email' => 'fail2ban@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $service = app(Fail2BanService::class);
        $this->assertTrue($service->isIpBanned($ip));

        $response = $this->from('/login')
            ->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post('/login', [
                'email' => 'fail2ban@example.com',
                'password' => 'secret123',
            ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_ban_expire_apres_la_duree_configuree(): void
    {
        config([
            'fail2ban.enabled' => true,
            'fail2ban.max_attempts' => 2,
            'fail2ban.find_time_minutes' => 5,
            'fail2ban.ban_minutes' => 1,
        ]);

        User::factory()->create([
            'email' => 'expire@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $ip = '11.11.11.11';

        for ($i = 0; $i < 2; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])->post('/login', [
                'email' => 'expire@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $service = app(Fail2BanService::class);
        $this->assertTrue($service->isIpBanned($ip));

        $this->travel(61)->seconds();

        $this->assertFalse($service->isIpBanned($ip));
    }
}

