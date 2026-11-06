<?php

namespace App\Models\Concerns;

use App\Models\Favorite;
use App\Models\Review;

trait Favoritable
{
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable')->where('is_approved', true);
    }

    public function isFavoritedBy(?\App\Models\User $user): bool
    {
        return $user && $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function kind(): string
    {
        return strtolower(class_basename($this));
    }
}
