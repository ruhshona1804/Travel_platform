<?php

namespace App\Models;

use App\Models\Concerns\Favoritable;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

abstract class Place extends Model
{
    use HasTranslations, Favoritable;

    protected $guarded = [];
    protected array $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return ['gallery' => 'array', 'is_demo' => 'boolean', 'is_published' => 'boolean', 'price_verified' => 'boolean'];
    }

    public function country() { return $this->belongsTo(Country::class); }
    public function city() { return $this->belongsTo(City::class); }

    public function scopePublished($q) { return $q->where('is_published', true); }
    public function getRouteKeyName() { return 'slug'; }
    public function hasCoords(): bool { return $this->lat !== null && $this->lng !== null; }

    public function imageUrl(): ?string
    {
        if (! $this->image) return null;
        return str_starts_with($this->image, 'http') ? $this->image : asset('storage/'.$this->image);
    }

    /** Taxminiy masofa (km), Haversine. */
    public function distanceTo(float $lat, float $lng): ?float
    {
        if (! $this->hasCoords()) return null;
        return \App\Support\Geo::distanceKm((float) $this->lat, (float) $this->lng, $lat, $lng);
    }

    public function mapPoint(): ?array
    {
        if (! $this->hasCoords()) return null;
        return [
            'id' => $this->kind().'-'.$this->id,
            'type' => $this->kind(),
            'category' => $this->category ?? $this->type ?? null,
            'name' => $this->tr('name'),
            'lat' => (float) $this->lat,
            'lng' => (float) $this->lng,
            'url' => route($this->kind().'s.show', $this),
            'address' => $this->address,
        ];
    }
}
