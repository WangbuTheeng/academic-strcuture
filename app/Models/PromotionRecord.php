<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'from_class_id',
        'to_class_id',
        'from_academic_year_id',
        'to_academic_year_id',
        'status',
        'remarks',
        'promoted_by',
        'promoted_at',
    ];

    protected $casts = [
        'promoted_at' => 'datetime',
    ];

    /**
     * Get the student that was promoted.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class the student was promoted from.
     */
    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'from_class_id');
    }

    /**
     * Get the class the student was promoted to.
     */
    public function toClass(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'to_class_id');
    }

    /**
     * Get the academic year the student was promoted from.
     */
    public function fromAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'from_academic_year_id');
    }

    /**
     * Get the academic year the student was promoted to.
     */
    public function toAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'to_academic_year_id');
    }

    /**
     * Get the user who performed the promotion.
     */
    public function promotedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }

    /**
     * Get the status color for UI display.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'promoted' => 'green',
            'retained' => 'yellow',
            'transferred' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'promoted' => 'Promoted',
            'retained' => 'Retained',
            'transferred' => 'Transferred',
            default => 'Unknown'
        };
    }

    /**
     * Check if the promotion was to a higher class.
     */
    public function getIsPromotionAttribute(): bool
    {
        return $this->status === 'promoted' && $this->from_class_id !== $this->to_class_id;
    }

    /**
     * Get promotion summary.
     */
    public function getSummaryAttribute(): string
    {
        if ($this->status === 'retained') {
            return "Retained in {$this->fromClass->name}";
        }

        if ($this->from_class_id === $this->to_class_id) {
            return "Continued in {$this->fromClass->name}";
        }

        return "Promoted from {$this->fromClass->name} to {$this->toClass->name}";
    }

    /**
     * Scope to get promotions for a specific academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('from_academic_year_id', $academicYearId);
    }

    /**
     * Scope to get promoted students.
     */
    public function scopePromoted($query)
    {
        return $query->where('status', 'promoted');
    }

    /**
     * Scope to get retained students.
     */
    public function scopeRetained($query)
    {
        return $query->where('status', 'retained');
    }
}
