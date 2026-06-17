<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'user_id',
        'tracking_date',
        'progress_value',
        'notes',
        'metrics',
        'status',
        'recorded_by',
    ];

    protected $casts = [
        'metrics' => 'array',
        'tracking_date' => 'date',
    ];

    // Relationships
    public function goal()
    {
        return $this->belongsTo(PerformanceGoal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
