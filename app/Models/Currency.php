<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_manual' => 'boolean', 'rate_updated_at' => 'datetime', 'rate_per_usd' => 'float'];
    }
}
