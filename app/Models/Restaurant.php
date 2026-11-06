<?php

namespace App\Models;

class Restaurant extends Place
{
    public const CATEGORIES = ['uzbek', 'halal', 'local', 'international', 'fastfood', 'cafe', 'budget', 'premium'];

    protected function casts(): array
    {
        return parent::casts() + ['is_uzbek' => 'boolean', 'is_halal' => 'boolean'];
    }

    public function mapPoint(): ?array
    {
        $p = parent::mapPoint();
        if ($p && $this->is_uzbek) $p['type'] = 'uzbek';
        return $p;
    }
}
