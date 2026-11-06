<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_direct' => 'boolean', 'is_sample' => 'boolean'];
    }

    public function airline() { return $this->belongsTo(Airline::class); }
    public function toCity() { return $this->belongsTo(City::class, 'to_city_id'); }

    public function durationLabel(): string
    {
        return $this->duration_min ? intdiv($this->duration_min, 60).'h '.($this->duration_min % 60).'m' : '—';
    }
}
