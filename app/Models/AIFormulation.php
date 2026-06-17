<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIFormulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'formulation',
        'type',
        'confidence_score',
        'metrics',
        'suggestions',
        'created_by',
    ];

    protected $casts = [
        'metrics' => 'array',
        'suggestions' => 'array',
        'confidence_score' => 'float',
    ];

    // Relationships
    public function goal()
    {
        return $this->belongsTo(PerformanceGoal::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
