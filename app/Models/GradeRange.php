<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeRange extends Model
{
    use HasFactory;

    protected $fillable = [
        'grading_scale_id',
        'grade',
        'min_percentage',
        'max_percentage',
        'gpa',
        'description',
        'is_passing'
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'gpa' => 'decimal:2',
        'is_passing' => 'boolean',
    ];

    /**
     * Get the grading scale that owns the grade range.
     */
    public function gradingScale(): BelongsTo
    {
        return $this->belongsTo(GradingScale::class);
    }

    /**
     * Check if a percentage falls within this grade range.
     */
    public function containsPercentage(float $percentage): bool
    {
        return $percentage >= $this->min_percentage && $percentage <= $this->max_percentage;
    }

    /**
     * Get the grade letter with description.
     */
    public function getFullGradeAttribute(): string
    {
        return $this->grade . ($this->description ? " ({$this->description})" : '');
    }

    /**
     * Scope to get passing grades.
     */
    public function scopePassing($query)
    {
        return $query->where('is_passing', true);
    }

    /**
     * Scope to get failing grades.
     */
    public function scopeFailing($query)
    {
        return $query->where('is_passing', false);
    }

    /**
     * Get grade color for UI display.
     */
    public function getGradeColorAttribute(): string
    {
        return match($this->grade) {
            'A+', 'A' => 'green',
            'B+', 'B' => 'blue',
            'C+', 'C' => 'yellow',
            'D+', 'D' => 'orange',
            'F' => 'red',
            default => 'gray'
        };
    }
}
