<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'address', 'lat', 'lng', 'contact_name',
        'contact_phone', 'contact_email', 'geofence_radius',
        'category', 'territory_id', 'assigned_to', 'is_active', 'notes',
    ];

    protected $casts = ['is_active' => 'boolean', 'lat' => 'float', 'lng' => 'float'];

    public function territory()   { return $this->belongsTo(Territory::class); }
    public function assignedTo()  { return $this->belongsTo(User::class, 'assigned_to'); }
    public function visits()      { return $this->hasMany(Visit::class); }
    public function schedules()   { return $this->hasMany(Schedule::class); }

    public function hasCoordinates(): bool
    {
        return $this->lat !== null && $this->lng !== null;
    }
}
