<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'visit_id', 'salesperson_id', 'template_id', 'data',
        'competitor_activity', 'order_value', 'satisfaction_rating',
        'photo_paths', 'voice_note_path',
    ];

    protected $casts = [
        'data'        => 'array',
        'photo_paths' => 'array',
        'order_value' => 'float',
    ];

    public function visit()       { return $this->belongsTo(Visit::class); }
    public function salesperson() { return $this->belongsTo(User::class, 'salesperson_id'); }
    public function template()    { return $this->belongsTo(FeedbackFormTemplate::class, 'template_id'); }
}
