<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Mark extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'exam_id',
        'subject_id',
        'assess_marks',
        'theory_marks',
        'practical_marks',
        'total',
        'percentage',
        'grade',
        'gpa',
        'result',
        'status',
        'grace_marks',
        'grace_reason',
        'remarks',
        'created_by',
        'updated_by',
        'approved_by',
        'submitted_at',
        'approved_at'
    ];

    protected $casts = [
        'assess_marks' => 'decimal:2',
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'total' => 'decimal:2',
        'percentage' => 'decimal:2',
        'gpa' => 'decimal:2',
        'grace_marks' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Accessor for total_marks to maintain backward compatibility.
     */
    public function getTotalMarksAttribute()
    {
        return $this->total;
    }

    /**
     * Mutator for total_marks to maintain backward compatibility.
     */
    public function setTotalMarksAttribute($value)
    {
        // Since total is a computed column, we don't actually set it
        // This is just for compatibility
    }

    /**
     * Get the student that owns the mark.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the exam that owns the mark.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the subject that owns the mark.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the user who created the mark.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the mark.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the mark.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the grace mark for this mark.
     */
    public function graceMark()
    {
        return $this->hasOne(GraceMark::class);
    }

    /**
     * Scope to filter by exam.
     */
    public function scopeByExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    /**
     * Scope to filter by subject.
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope to filter by student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by result.
     */
    public function scopeByResult($query, $result)
    {
        return $query->where('result', $result);
    }

    /**
     * Calculate total marks automatically.
     */
    public function calculateTotalMarks()
    {
        $this->total = ($this->assess_marks ?? 0) +
                      ($this->theory_marks ?? 0) +
                      ($this->practical_marks ?? 0);
        return $this;
    }

    /**
     * Calculate percentage based on exam max marks.
     */
    public function calculatePercentage()
    {
        if ($this->exam && $this->exam->max_marks > 0) {
            // Add grace marks to the computed total for percentage calculation
            $totalWithGrace = $this->total + ($this->grace_marks ?? 0);
            $this->percentage = ($totalWithGrace / $this->exam->max_marks) * 100;
        }
        return $this;
    }

    /**
     * Calculate grade and GPA based on grading scale.
     */
    public function calculateGradeAndGPA()
    {
        $gradingScale = $this->exam->gradingScale ??
                       $this->exam->class->level->gradingScale ??
                       GradingScale::where('is_default', true)->first();

        if ($gradingScale && $this->percentage !== null) {
            $gradeRange = $gradingScale->gradeRanges()
                                     ->where('min_percentage', '<=', $this->percentage)
                                     ->where('max_percentage', '>=', $this->percentage)
                                     ->first();

            if ($gradeRange) {
                $this->grade = $gradeRange->grade;
                $this->gpa = $gradeRange->gpa;
                $this->result = $gradeRange->is_passing ? 'Pass' : 'Fail';
            }
        }

        return $this;
    }

    /**
     * Perform all calculations.
     */
    public function performCalculations()
    {
        return $this->calculateTotalMarks()
                   ->calculatePercentage()
                   ->calculateGradeAndGPA();
    }

    /**
     * Check if mark is editable.
     */
    public function getIsEditableAttribute()
    {
        return in_array($this->status, ['draft', 'pending']) &&
               $this->exam->can_enter_marks;
    }

    /**
     * Check if mark can be submitted.
     */
    public function getCanSubmitAttribute()
    {
        return $this->status === 'draft' &&
               $this->total !== null;
    }

    /**
     * Check if mark can be approved.
     */
    public function getCanApproveAttribute()
    {
        return $this->status === 'submitted';
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'submitted' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get result color for UI.
     */
    public function getResultColorAttribute()
    {
        return match($this->result) {
            'Pass' => 'green',
            'Fail' => 'red',
            default => 'gray'
        };
    }

    /**
     * Submit marks for approval.
     */
    public function submit()
    {
        $this->status = 'submitted';
        $this->submitted_at = now();
        $this->save();
        return $this;
    }

    /**
     * Approve marks.
     */
    public function approve($userId = null)
    {
        $this->status = 'approved';
        $this->approved_by = $userId ?? auth()->id();
        $this->approved_at = now();
        $this->save();
        return $this;
    }

    /**
     * Reject marks.
     */
    public function reject($reason = null)
    {
        $this->status = 'rejected';
        $this->remarks = $reason;
        $this->save();
        return $this;
    }

    /**
     * Apply grace marks.
     */
    public function applyGraceMarks($graceMarks, $reason)
    {
        $this->grace_marks = $graceMarks;
        $this->grace_reason = $reason;
        $this->performCalculations();
        $this->save();
        return $this;
    }
}
