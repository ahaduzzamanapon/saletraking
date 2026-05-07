<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackFormTemplate extends Model
{
    protected $fillable = ['name', 'description', 'fields', 'is_default', 'is_active', 'created_by'];
    protected $casts    = ['fields' => 'array', 'is_default' => 'boolean', 'is_active' => 'boolean'];

    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function feedbacks() { return $this->hasMany(Feedback::class, 'template_id'); }
}
