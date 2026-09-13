<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_type', 'severity', 'access_card_id', 'access_log_id', 'user_id',
        'assigned_to', 'location', 'status', 'description', 'resolution_notes', 'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function card() { return $this->belongsTo(AccessCard::class, 'access_card_id'); }
    public function log() { return $this->belongsTo(AccessLog::class, 'access_log_id'); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
}
