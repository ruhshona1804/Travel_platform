<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelPlanItem extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['cost_estimated' => 'boolean']; }
    public function day() { return $this->belongsTo(TravelPlanDay::class, 'travel_plan_day_id'); }
}
