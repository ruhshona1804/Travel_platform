<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public const SECTIONS = ['trending', 'budget', 'hotels', 'restaurants', 'map', 'planner', 'guides'];

    public function edit()
    {
        return view('admin.settings', ['sections' => self::SECTIONS, 'apis' => [
            'AI_API_KEY' => filled(config('travel.ai.api_key')), 'EXCHANGE_PROVIDER' => config('travel.exchange.provider'),
            'GOOGLE_CLIENT_ID' => config('travel.google.enabled'), 'FLIGHT_PROVIDER' => config('travel.flights.provider') !== 'none',
        ]]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:60', 'tagline' => 'nullable|string|max:160',
            'seo_description' => 'nullable|string|max:300', 'announcement' => 'nullable|string|max:300',
            'contact_email' => 'nullable|email|max:120', 'footer_text' => 'nullable|string|max:300',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'locales' => 'nullable|array', 'locales.*' => 'in:'.implode(',', array_keys(config('travel.locales'))),
            'sections' => 'nullable|array', 'sections.*' => 'in:'.implode(',', self::SECTIONS),
        ]);
        foreach (['app_name', 'tagline', 'seo_description', 'announcement', 'contact_email', 'footer_text'] as $k) {
            Setting::put($k, $data[$k] ?? null);
        }
        if ($request->hasFile('logo')) Setting::put('logo', $request->file('logo')->store('branding', 'public'));
        $locales = array_unique(array_merge(['uz'], $data['locales'] ?? [])); // o'zbek tili doim yoqilgan
        Setting::put('enabled_locales', json_encode(array_values($locales)));
        Setting::put('hidden_sections', json_encode(array_values(array_diff(self::SECTIONS, $data['sections'] ?? []))));
        ActivityLog::record('settings.update');
        return back()->with('status', __('ui.saved'));
    }
}
