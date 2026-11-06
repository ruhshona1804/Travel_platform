<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    use HasTranslations;

    protected $guarded = [];
    protected array $translatable = ['description'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'is_demo' => 'boolean'];
    }

    public function flights() { return $this->hasMany(Flight::class); }
    public function getRouteKeyName() { return 'slug'; }
}
