<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'review_period',
        'review_type',
        'overall_rating',
        'strengths',
        'areas_for_improvement',
        'comments',
        'competencies',
        'status',
        'review_date',
    ];

    protected $casts = [
        'competencies' => 'array',
        'review_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
