<?php

namespace App\Models;

use App\Models\Concerns\Favoritable;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasTranslations, Favoritable;

    protected $guarded = [];
    protected array $translatable = ['name', 'capital', 'summary', 'climate', 'safety', 'customs', 'local_transport'];
    public const CONTINENTS = ['asia', 'europe', 'africa', 'north_america', 'south_america', 'oceania'];
    public const TRAVEL_TYPES = ['budget', 'comfortable', 'luxury', 'family', 'adventure', 'historical', 'shopping', 'religious'];

    protected function casts(): array
    {
        return ['travel_types' => 'array', 'local_apps' => 'array', 'emergency_numbers' => 'array', 'is_published' => 'boolean', 'is_demo' => 'boolean'];
    }

    public function cities() { return $this->hasMany(City::class); }
    public function attractions() { return $this->hasMany(Attraction::class); }
    public function hotels() { return $this->hasMany(Hotel::class); }
    public function restaurants() { return $this->hasMany(Restaurant::class); }
    public function visa() { return $this->hasOne(VisaRequirement::class); }

    public function scopePublished($q) { return $q->where('is_published', true); }
    public function getRouteKeyName() { return 'slug'; }

    public function flag(): string
    {
        $iso = strtoupper($this->iso2);
        return mb_chr(0x1F1E6 + ord($iso[0]) - 65).mb_chr(0x1F1E6 + ord($iso[1]) - 65);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) return null;
        return str_starts_with($this->image, 'http') ? $this->image : asset('storage/'.$this->image);
    }

    public function dailyBudget(string $style): ?float
    {
        $map = ['budget' => 'daily_budget_budget', 'comfortable' => 'daily_budget_comfort', 'luxury' => 'daily_budget_luxury'];
        $v = $this->{$map[$style] ?? 'daily_budget_comfort'};
        return $v !== null ? (float) $v : null;
    }
}
