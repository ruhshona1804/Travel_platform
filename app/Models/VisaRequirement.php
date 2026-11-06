<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class VisaRequirement extends Model
{
    use HasTranslations;

    protected $guarded = [];
    protected array $translatable = ['documents', 'notes'];
    public const STATUSES = ['visa_free', 'visa_required', 'on_arrival', 'e_visa', 'check_official'];

    protected function casts(): array
    {
        return ['last_verified_at' => 'date', 'insurance_required' => 'boolean'];
    }

    public function country() { return $this->belongsTo(Country::class); }
}
