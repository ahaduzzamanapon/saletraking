<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'lat', 'lng', 'accuracy', 'speed', 'battery_level', 'recorded_at'];
    protected $casts    = ['recorded_at' => 'datetime', 'lat' => 'float', 'lng' => 'float'];

    public function user() { return $this->belongsTo(User::class); }
}
