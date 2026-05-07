<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone',
        'employee_id', 'territory_id', 'fcm_token', 'avatar',
        'is_active', 'last_seen_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_seen_at'      => 'datetime',
        ];
    }

    // --- Role helpers ---
    public function isAdmin(): bool        { return in_array($this->role, ['superadmin', 'manager']); }
    public function isSalesperson(): bool  { return $this->role === 'salesperson'; }
    public function isSuperAdmin(): bool   { return $this->role === 'superadmin'; }
    public function isAnalyst(): bool      { return $this->role === 'analyst'; }

    // --- Relationships ---
    public function territory()  { return $this->belongsTo(Territory::class); }
    public function visits()     { return $this->hasMany(Visit::class, 'salesperson_id'); }
    public function locations()  { return $this->hasMany(Location::class); }
    public function targets()    { return $this->hasMany(Target::class); }
    public function schedules()  { return $this->hasMany(Schedule::class, 'salesperson_id'); }
    public function feedbacks()  { return $this->hasMany(Feedback::class, 'salesperson_id'); }
    public function clients()    { return $this->hasMany(Client::class, 'assigned_to'); }

    public function latestLocation()
    {
        return $this->hasOne(Location::class)->latestOfMany('recorded_at');
    }

    public function todayVisits()
    {
        return $this->visits()->whereDate('checkin_at', today());
    }

    public function todayTarget()
    {
        return $this->targets()
            ->where('type', 'daily')
            ->whereDate('period_date', today())
            ->first();
    }
}
