<?php

namespace App\Models;

class Hotel extends Place
{
    public const TYPES = ['budget', 'luxury', 'hostel', 'apartment', 'family', 'guesthouse', 'resort'];

    protected function casts(): array
    {
        return parent::casts() + ['facilities' => 'array'];
    }
}
