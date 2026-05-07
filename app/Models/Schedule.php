<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'salesperson_id', 'client_id', 'scheduled_at',
        'status', 'sort_order', 'notes', 'visit_id',
    ];

    protected $casts = ['scheduled_at' => 'datetime'];

    public function salesperson() { return $this->belongsTo(User::class, 'salesperson_id'); }
    public function client()      { return $this->belongsTo(Client::class); }
    public function visit()       { return $this->belongsTo(Visit::class); }
}
