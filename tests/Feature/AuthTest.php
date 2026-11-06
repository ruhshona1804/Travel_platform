<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\CurrencySeeder;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurrencySeeder::class);
    }

    public function test_user_can_register_and_gets_welcome_notification(): void
    {
        $res = $this->post('/register', ['name' => 'Ali', 'email' => 'ali@example.test', 'password' => 'password123', 'password_confirmation' => 'password123']);
        $res->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticated();
        $user = User::where('email', 'ali@example.test')->first();
        $this->assertSame('user', $user->role);
        $this->assertCount(1, $user->notifications);
    }

    public function test_registration_cannot_set_role(): void
    {
        $this->post('/register', ['name' => 'Evil', 'email' => 'evil@example.test', 'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 'super_admin']);
        $this->assertSame('user', User::where('email', 'evil@example.test')->value('role'));
    }

    public function test_registration_validates_input(): void
    {
        $this->post('/register', ['name' => '', 'email' => 'bad', 'password' => 'short', 'password_confirmation' => 'x'])
            ->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_login_logout_and_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'password123']);
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create(['password' => 'password123']);
        $user->forceFill(['is_suspended' => true])->save();
        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_password_reset_request_does_not_reveal_accounts(): void
    {
        $this->post('/forgot-password', ['email' => 'nobody@example.test'])->assertSessionHas('status');
    }

    public function test_user_can_update_and_delete_own_account(): void
    {
        $user = User::factory()->create(['password' => 'password123']);
        $this->actingAs($user)->put('/dashboard/profile', ['name' => 'Yangi Ism', 'email' => $user->email, 'locale' => 'ru', 'currency' => 'UZS', 'theme' => 'dark'])->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertSame(['Yangi Ism', 'ru', 'UZS', 'dark'], [$user->name, $user->locale, $user->currency, $user->theme]);
        $this->delete('/dashboard/account', ['password' => 'password123'])->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
