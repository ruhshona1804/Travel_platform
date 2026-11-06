<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class TravelGuide extends Model
{
    use HasTranslations;

    protected $guarded = [];
    protected array $translatable = ['title', 'excerpt', 'body'];
    protected function casts(): array { return ['is_published' => 'boolean']; }
    public function getRouteKeyName() { return 'slug'; }
    public function scopePublished($q) { return $q->where('is_published', true); }
}
