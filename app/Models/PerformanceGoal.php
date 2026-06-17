<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PerformanceGoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'target_value',
        'current_value',
        'progress_percentage',
        'start_date',
        'end_date',
        'actual_completion_date',
        'ai_formulation',
        'metrics',
        'assigned_by',
    ];

    protected $casts = [
        'metrics' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_completion_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function tracking()
    {
        return $this->hasMany(PerformanceTracking::class, 'goal_id');
    }

    public function aiFormulations()
    {
        return $this->hasMany(AIFormulation::class, 'goal_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Methods
    public function updateProgress($value)
    {
        $this->current_value = $value;
        $this->progress_percentage = $this->calculateProgress();
        $this->save();
    }

    public function calculateProgress()
    {
        if ($this->target_value == 0) return 0;
        return min(100, round(($this->current_value / $this->target_value) * 100));
    }
}
