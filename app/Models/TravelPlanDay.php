<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelPlanDay extends Model
{
    protected $guarded = [];
    public function plan() { return $this->belongsTo(TravelPlan::class, 'travel_plan_id'); }
    public function items() { return $this->hasMany(TravelPlanItem::class)->orderBy('position'); }
}
