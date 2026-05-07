<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'salesperson_id', 'client_id', 'checkin_at', 'checkout_at',
        'checkin_lat', 'checkin_lng', 'checkin_method', 'status',
        'outcome', 'rating', 'order_value', 'notes', 'contact_met', 'feedback_submitted',
    ];

    protected $casts = [
        'checkin_at'         => 'datetime',
        'checkout_at'        => 'datetime',
        'feedback_submitted' => 'boolean',
        'order_value'        => 'float',
    ];

    public function salesperson() { return $this->belongsTo(User::class, 'salesperson_id'); }
    public function client()      { return $this->belongsTo(Client::class); }
    public function feedback()    { return $this->hasOne(Feedback::class); }

    public function durationMinutes(): ?int
    {
        if ($this->checkin_at && $this->checkout_at) {
            return (int) $this->checkin_at->diffInMinutes($this->checkout_at);
        }
        return null;
    }
}
