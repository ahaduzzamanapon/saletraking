<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = [
        'user_id', 'type', 'period_date',
        'visits_target', 'sales_target',
        'visits_achieved', 'sales_achieved',
    ];

    protected $casts = ['period_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }

    public function visitsProgress(): int
    {
        if ($this->visits_target == 0) return 0;
        return min(100, (int) (($this->visits_achieved / $this->visits_target) * 100));
    }

    public function salesProgress(): int
    {
        if ($this->sales_target == 0) return 0;
        return min(100, (int) (($this->sales_achieved / $this->sales_target) * 100));
    }
}
