<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraceMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'mark_id',
        'grace_marks',
        'reason',
        'justification',
        'status',
        'approval_remarks',
        'rejection_reason',
        'requested_by',
        'approved_by',
        'rejected_by',
        'requested_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'grace_marks' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the mark that this grace mark is for.
     */
    public function mark(): BelongsTo
    {
        return $this->belongsTo(Mark::class);
    }

    /**
     * Get the user who requested the grace mark.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who approved the grace mark.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected the grace mark.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the status color for UI display.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'green',
            'rejected' => 'red',
            'pending' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'pending' => 'Pending',
            default => 'Unknown'
        };
    }

    /**
     * Check if grace mark is pending.
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if grace mark is approved.
     */
    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if grace mark is rejected.
     */
    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the processing time.
     */
    public function getProcessingTimeAttribute(): ?string
    {
        $endTime = $this->approved_at ?? $this->rejected_at;

        if (!$this->requested_at || !$endTime) {
            return null;
        }

        $duration = $endTime->diffInHours($this->requested_at);

        if ($duration < 24) {
            return $duration . ' hours';
        } else {
            return round($duration / 24, 1) . ' days';
        }
    }

    /**
     * Get the impact on percentage.
     */
    public function getPercentageImpactAttribute(): float
    {
        if (!$this->mark) {
            return 0;
        }

        return ($this->grace_marks / $this->mark->total_marks) * 100;
    }

    /**
     * Get the new total marks after grace marks.
     */
    public function getNewTotalMarksAttribute(): float
    {
        if (!$this->mark) {
            return 0;
        }

        return $this->mark->obtained_marks + $this->grace_marks;
    }

    /**
     * Get the new percentage after grace marks.
     */
    public function getNewPercentageAttribute(): float
    {
        if (!$this->mark) {
            return 0;
        }

        return ($this->new_total_marks / $this->mark->total_marks) * 100;
    }

    /**
     * Scope to get pending grace marks.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved grace marks.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected grace marks.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to get grace marks for a specific exam.
     */
    public function scopeForExam($query, $examId)
    {
        return $query->whereHas('mark', function($q) use ($examId) {
            $q->where('exam_id', $examId);
        });
    }

    /**
     * Scope to get grace marks for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->whereHas('mark', function($q) use ($studentId) {
            $q->where('student_id', $studentId);
        });
    }

    /**
     * Scope to get grace marks for a specific subject.
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->whereHas('mark', function($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }

    /**
     * Scope to get recent grace marks.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
