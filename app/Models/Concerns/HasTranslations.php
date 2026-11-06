<?php

namespace App\Models\Concerns;

/**
 * JSON ustunlardagi tarjimalar ({"uz": "...", "ru": "..."}).
 * Tarjima topilmasa, o'zbek tiliga qaytadi.
 */
trait HasTranslations
{
    public function initializeHasTranslations(): void
    {
        foreach ($this->translatable as $field) {
            $this->casts[$field] = 'array';
        }
    }

    protected function asJson($value, $flags = 0)
    {
        return parent::asJson($value, $flags | JSON_UNESCAPED_UNICODE);
    }

    /** Barcha tillardagi tarjima ichidan qidirish (JSON ustun bo'yicha). */
    public function scopeSearchName($q, string $term, string $column = 'name')
    {
        return $q->where($column, 'like', '%'.addcslashes($term, '%_\\').'%');
    }

    public function tr(string $field, ?string $locale = null): ?string
    {
        $values = $this->getAttribute($field);
        if (! is_array($values)) {
            return $values;
        }
        $locale ??= app()->getLocale();
        foreach ([$locale, config('app.fallback_locale', 'uz')] as $l) {
            if (! empty($values[$l])) {
                return $values[$l];
            }
        }
        return collect($values)->filter()->first();
    }

    public function isTranslatedTo(string $field, string $locale): bool
    {
        return ! empty(($this->getAttribute($field) ?? [])[$locale]);
    }
}
