<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackofficePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['travel.admin' => ['name' => 'Root', 'email' => 'root@example.test', 'password' => 'Sup3r-Secret-Pass']]);
        $this->seed(DatabaseSeeder::class);
    }

    private function root(): User { return User::where('email', 'root@example.test')->firstOrFail(); }

    public function test_every_admin_page_renders_for_super_admin(): void
    {
        $this->actingAs($this->root());
        foreach (['/admin', '/admin/users', '/admin/currencies', '/admin/settings'] as $u) $this->get($u)->assertOk();
        $this->get('/admin/users/'.$this->root()->id)->assertOk();
        foreach (config('admin') as $res => $cfg) {
            $this->get("/admin/r/$res")->assertOk();
            $this->get("/admin/r/$res?q=a")->assertOk();
            $this->get("/admin/r/$res/create")->assertOk();
            $row = $cfg['model']::first();
            if ($row) $this->get("/admin/r/$res/{$row->getKey()}/edit")->assertOk();
        }
    }

    public function test_admin_pages_render_in_every_language(): void
    {
        $this->actingAs($this->root());
        foreach (array_keys(config('travel.locales')) as $l) {
            $this->post("/locale/$l");
            $this->get('/admin')->assertOk();
            $this->get('/admin/r/hotels')->assertOk();
        }
    }

    public function test_every_user_dashboard_page_renders(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u);
        foreach (['index', 'favorites', 'plans', 'budgets', 'notifications', 'profile'] as $p) $this->get("/dashboard".($p === 'index' ? '' : "/$p"))->assertOk();
        $this->post('/dashboard/notifications/read')->assertRedirect();
    }

    public function test_image_upload_is_validated_and_stored(): void
    {
        Storage::fake('public');
        $this->actingAs($this->root());
        $country = \App\Models\Country::first();
        $base = ['slug' => $country->slug, 'iso2' => $country->iso2, 'continent' => 'asia', 'name' => ['uz' => 'Turkiya'], 'is_published' => 1];
        $this->put("/admin/r/countries/{$country->id}", $base + ['image' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php')])->assertSessionHasErrors('image');
        $this->put("/admin/r/countries/{$country->id}", $base + ['image' => UploadedFile::fake()->createWithContent('flag.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='))])->assertSessionHasNoErrors();
        $path = $country->fresh()->image;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_settings_update_and_language_toggle(): void
    {
        $this->actingAs($this->root());
        $this->put('/admin/settings', ['app_name' => 'Safar', 'announcement' => 'Yangi mavsum!', 'locales' => ['ru'], 'sections' => ['map']])->assertSessionHasNoErrors();
        $this->assertSame('Safar', Setting::get('app_name'));
        $this->get('/')->assertSee('Safar')->assertSee('Yangi mavsum!');
        $this->assertArrayNotHasKey('kk', enabled_locales());
        $this->assertArrayHasKey('uz', enabled_locales());
        $this->post('/locale/kk')->assertNotFound();
        $this->assertFalse(section_visible('trending'));
    }

    public function test_edit_guide_and_visa_verification(): void
    {
        $this->actingAs($this->root());
        $visa = \App\Models\VisaRequirement::first();
        $this->put("/admin/r/visas/{$visa->id}", [
            'country_id' => $visa->country_id, 'status' => 'visa_free', 'verification_status' => 'verified', 'last_verified_at' => '2026-11-01',
            'max_stay_days' => 30, 'official_url' => 'https://example.gov', 'documents' => ['uz' => 'Pasport'],
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->get('/countries/'.$visa->country->slug)->assertSee(__('ui.vs_visa_free'))->assertSee('01.11.2026')->assertSee(__('ui.visa_warning'));
    }

    public function test_error_pages_render(): void
    {
        $this->get('/nope')->assertNotFound()->assertSee(__('ui.err_title_404'));
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden()->assertSee(__('ui.err_title_403'));
    }
}
