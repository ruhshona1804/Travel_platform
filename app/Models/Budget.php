<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $guarded = [];
    public const CATEGORIES = ['flights', 'accommodation', 'food', 'taxi', 'public_transport', 'attractions', 'shopping', 'insurance', 'other'];
    public function user() { return $this->belongsTo(User::class); }
    public function country() { return $this->belongsTo(Country::class); }
    public function items() { return $this->hasMany(BudgetItem::class); }
    public function totalUsd(): float { return (float) $this->items->sum('amount_usd'); }
}
