<?php

namespace Tests\Feature;

use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const EMAIL = 'arthur.white@example.net';

    private const PASSWORD = 'Test_123';

    /**
     * @return array<string, string>
     */
    private function spaHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Origin' => 'http://localhost:3000',
            'Referer' => 'http://localhost:3000/login',
        ];
    }

    private function createAdministrator(): Administrator
    {
        return Administrator::query()->create([
            'email' => self::EMAIL,
            'password' => Hash::make(self::PASSWORD),
        ]);
    }

    public function test_spa_can_initialize_csrf_protection(): void
    {
        $this->get('/sanctum/csrf-cookie')->assertNoContent();
    }

    public function test_administrator_can_login_fetch_their_record_and_logout(): void
    {
        $administrator = $this->createAdministrator();

        $this->withHeaders($this->spaHeaders())
            ->postJson('/api/v1/auth/login', [
                'email' => self::EMAIL,
                'password' => self::PASSWORD,
            ])
            ->assertOk()
            ->assertJsonPath('data.id', $administrator->id)
            ->assertJsonPath('data.email', self::EMAIL)
            ->assertJsonMissingPath('data.password');

        $this->assertAuthenticatedAs($administrator, 'web');

        $this->withHeaders($this->spaHeaders())
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $administrator->id)
            ->assertJsonPath('data.email', self::EMAIL);

        $this->withHeaders($this->spaHeaders())
            ->postJson('/api/v1/auth/logout')
            ->assertNoContent();

        $this->assertGuest('web');
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $this->createAdministrator();

        $this->withHeaders($this->spaHeaders())
            ->postJson('/api/v1/auth/login', [
                'email' => self::EMAIL,
                'password' => 'incorrect-password',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);

        $this->assertGuest('web');
    }

    public function test_unauthenticated_requests_cannot_access_me_or_logout(): void
    {
        $this->withHeaders($this->spaHeaders())
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();

        $this->withHeaders($this->spaHeaders())
            ->postJson('/api/v1/auth/logout')
            ->assertUnauthorized();
    }
}
