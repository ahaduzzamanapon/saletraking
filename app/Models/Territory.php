<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Territory extends Model
{
    protected $fillable = ['name', 'description', 'polygon', 'manager_id'];
    protected $casts    = ['polygon' => 'array'];

    public function manager()   { return $this->belongsTo(User::class, 'manager_id'); }
    public function users()     { return $this->hasMany(User::class); }
    public function clients()   { return $this->hasMany(Client::class); }
}
