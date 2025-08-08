<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolStatistics extends Model
{
    protected $fillable = [
        'school_id',
        'total_students',
        'total_teachers',
        'total_classes',
        'total_subjects',
        'total_exams',
        'last_login',
        'last_activity',
        'feature_usage',
        'performance_metrics'
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'last_activity' => 'datetime',
        'feature_usage' => 'array',
        'performance_metrics' => 'array'
    ];

    /**
     * Get the school that owns the statistics
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Update statistics for a school
     */
    public function updateCounts(): void
    {
        $school = $this->school;

        $this->update([
            'total_students' => $school->students()->count(),
            'total_teachers' => $school->users()->role('teacher')->count(),
            'total_classes' => $school->classes()->count(),
            'total_subjects' => $school->subjects()->count(),
            'total_exams' => $school->exams()->count(),
            'last_activity' => now()
        ]);
    }

    /**
     * Record feature usage
     */
    public function recordFeatureUsage(string $feature): void
    {
        $usage = $this->feature_usage ?? [];
        $usage[$feature] = ($usage[$feature] ?? 0) + 1;

        $this->update(['feature_usage' => $usage]);
    }

    /**
     * Update performance metrics
     */
    public function updatePerformanceMetrics(array $metrics): void
    {
        $current = $this->performance_metrics ?? [];
        $updated = array_merge($current, $metrics);

        $this->update(['performance_metrics' => $updated]);
    }
}
