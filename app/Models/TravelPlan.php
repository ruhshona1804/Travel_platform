<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelPlan extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['input' => 'array', 'summary' => 'array', 'tips' => 'array', 'start_date' => 'date', 'end_date' => 'date'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function country() { return $this->belongsTo(Country::class); }
    public function city() { return $this->belongsTo(City::class); }
    public function days() { return $this->hasMany(TravelPlanDay::class)->orderBy('day_number'); }
}
