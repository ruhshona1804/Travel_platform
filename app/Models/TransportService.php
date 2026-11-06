<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class TransportService extends Model
{
    use HasTranslations;

    protected $guarded = [];
    protected array $translatable = ['description'];
    public const TYPES = ['taxi', 'airport_transfer', 'metro', 'bus', 'train', 'tram', 'car_rental', 'intercity'];

    protected function casts(): array
    {
        return ['price_verified' => 'boolean', 'is_demo' => 'boolean'];
    }

    public function city() { return $this->belongsTo(City::class); }
}
