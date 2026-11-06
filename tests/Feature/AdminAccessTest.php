<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        $u = User::factory()->create();
        $u->forceFill(['role' => $role])->save();
        return $u;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurrencySeeder::class);
    }

    public function test_guest_and_regular_user_cannot_access_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->actingAs($this->user('user'))->get('/admin')->assertForbidden();
        $this->actingAs($this->user('user'))->get('/admin/r/countries')->assertForbidden();
    }

    public function test_editor_can_manage_content_but_not_system(): void
    {
        $editor = $this->user('editor');
        $this->actingAs($editor)->get('/admin')->assertOk();
        $this->get('/admin/r/countries')->assertOk();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/currencies')->assertForbidden();
        $this->get('/admin/settings')->assertForbidden();
    }

    public function test_editor_cannot_delete_but_admin_can(): void
    {
        $country = Country::create(['slug' => 'x', 'iso2' => 'XX', 'name' => ['uz' => 'X'], 'continent' => 'asia']);
        $this->actingAs($this->user('editor'))->delete("/admin/r/countries/{$country->id}")->assertForbidden();
        $this->assertDatabaseHas('countries', ['id' => $country->id]);
        $this->actingAs($this->user('admin'))->delete("/admin/r/countries/{$country->id}")->assertRedirect();
        $this->assertDatabaseMissing('countries', ['id' => $country->id]);
    }

    public function test_country_crud_with_translations(): void
    {
        $this->actingAs($this->user('admin'));
        $this->post('/admin/r/countries', [
            'slug' => 'test-land', 'iso2' => 'TL', 'continent' => 'europe',
            'name' => ['uz' => 'Sinov yurti', 'ru' => 'Страна', 'en' => 'Test land'],
            'best_season' => ['spring', 'autumn'], 'travel_types' => ['family'],
            'local_apps' => 'Uber, Bolt', 'emergency_numbers' => "police=112\nambulance=103",
            'is_published' => 1, 'popularity' => 5,
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.resource.index', 'countries'));
        $c = Country::where('slug', 'test-land')->firstOrFail();
        $this->assertSame('Sinov yurti', $c->tr('name'));
        $this->assertSame('spring,autumn', $c->best_season);
        $this->assertSame(['Uber', 'Bolt'], $c->local_apps);
        $this->assertSame('112', $c->emergency_numbers['police']);

        $this->put("/admin/r/countries/{$c->id}", ['slug' => 'test-land', 'iso2' => 'TL', 'continent' => 'europe', 'name' => ['uz' => 'Yangilangan'], 'popularity' => '', 'is_published' => 1])->assertSessionHasNoErrors()->assertRedirect(route('admin.resource.index', 'countries'));
        $this->assertSame('Yangilangan', $c->fresh()->tr('name'));
    }

    public function test_country_requires_uzbek_name_and_unique_slug(): void
    {
        $this->actingAs($this->user('admin'));
        Country::create(['slug' => 'dup', 'iso2' => 'DD', 'name' => ['uz' => 'D'], 'continent' => 'asia']);
        $this->post('/admin/r/countries', ['slug' => 'dup', 'iso2' => 'DE', 'continent' => 'asia', 'name' => ['ru' => 'Нет узбекского']])
            ->assertSessionHasErrors(['name.uz', 'slug']);
    }

    public function test_admin_cannot_assign_super_admin_or_touch_one(): void
    {
        $admin = $this->user('admin');
        $target = $this->user('user');
        $super = $this->user('super_admin');
        $this->actingAs($admin)->put("/admin/users/{$target->id}", ['role' => 'super_admin'])->assertForbidden();
        $this->put("/admin/users/{$super->id}", ['role' => 'user'])->assertForbidden();
        $this->delete("/admin/users/{$super->id}")->assertForbidden();
        $this->put("/admin/users/{$target->id}", ['role' => 'editor', 'is_suspended' => 1])->assertSessionHasNoErrors();
        $this->assertSame(['editor', true], [$target->fresh()->role, $target->fresh()->is_suspended]);
    }

    public function test_admin_cannot_modify_self(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->put("/admin/users/{$admin->id}", ['role' => 'user'])->assertStatus(422);
        $this->delete("/admin/users/{$admin->id}")->assertStatus(422);
    }

    public function test_super_admin_seeder_uses_environment_and_never_hardcodes(): void
    {
        config(['travel.admin' => ['name' => 'Boss', 'email' => 'boss@example.test', 'password' => 'Sup3r-Secret-Pass']]);
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);
        $u = User::where('email', 'boss@example.test')->firstOrFail();
        $this->assertSame('super_admin', $u->role);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Sup3r-Secret-Pass', $u->password));
    }

    public function test_super_admin_seeder_refuses_weak_password_and_missing_config(): void
    {
        config(['travel.admin' => ['name' => 'X', 'email' => 'weak@example.test', 'password' => 'short']]);
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);
        config(['travel.admin' => ['name' => 'X', 'email' => null, 'password' => null]]);
        $this->seed(\Database\Seeders\SuperAdminSeeder::class);
        $this->assertDatabaseCount('users', 0);
    }
}
