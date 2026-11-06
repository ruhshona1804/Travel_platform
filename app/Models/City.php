<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasTranslations;

    protected $guarded = [];
    protected array $translatable = ['name', 'summary'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'is_demo' => 'boolean'];
    }

    public function country() { return $this->belongsTo(Country::class); }
    public function attractions() { return $this->hasMany(Attraction::class); }
    public function hotels() { return $this->hasMany(Hotel::class); }
    public function restaurants() { return $this->hasMany(Restaurant::class); }
    public function airports() { return $this->hasMany(Airport::class); }
    public function transportServices() { return $this->hasMany(TransportService::class); }
    public function scopePublished($q) { return $q->where('is_published', true); }
    public function getRouteKeyName() { return 'slug'; }

    public function imageUrl(): ?string
    {
        if (! $this->image) return null;
        return str_starts_with($this->image, 'http') ? $this->image : asset('storage/'.$this->image);
    }
}
