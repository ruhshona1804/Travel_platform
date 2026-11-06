<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const ROLES = ['super_admin', 'admin', 'editor', 'user'];

    protected $guarded = ['role', 'is_suspended', 'email_verified_at'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_suspended' => 'boolean'];
    }

    public function favorites() { return $this->hasMany(Favorite::class); }
    public function travelPlans() { return $this->hasMany(TravelPlan::class); }
    public function budgets() { return $this->hasMany(Budget::class); }

    public function hasRole(string ...$roles): bool { return in_array($this->role, $roles, true); }
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isAdmin(): bool { return $this->hasRole('super_admin', 'admin'); }
    public function canAccessAdmin(): bool { return $this->hasRole('super_admin', 'admin', 'editor') && ! $this->is_suspended; }

    public function avatarUrl(): string
    {
        return $this->avatar ? asset('storage/'.$this->avatar)
            : 'https://ui-avatars.com/api/?background=0e7490&color=fff&name='.urlencode($this->name);
    }
}
