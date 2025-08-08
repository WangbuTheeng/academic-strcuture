<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Exam extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'exam_type',
        'academic_year_id',

        'class_id',
        'program_id',
        'subject_id',
        'max_marks',
        'theory_max',
        'theory_pass_marks',
        'practical_max',
        'practical_pass_marks',
        'assess_max',
        'assess_pass_marks',
        'has_practical',
        'has_assessment',
        'start_date',
        'end_date',
        'submission_deadline',
        'result_status',
        'grading_scale_id',
        'created_by',
        'approved_by',
        'published_by',
        'locked_by',
        'approved_at',
        'published_at',
        'locked_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submission_deadline' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
        'locked_at' => 'datetime',
        'max_marks' => 'decimal:2',
        'theory_max' => 'decimal:2',
        'theory_pass_marks' => 'decimal:2',
        'practical_max' => 'decimal:2',
        'practical_pass_marks' => 'decimal:2',
        'assess_max' => 'decimal:2',
        'assess_pass_marks' => 'decimal:2',
        'has_practical' => 'boolean',
        'has_assessment' => 'boolean',
    ];

    /**
     * Get the academic year that owns the exam.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the class that owns the exam.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the program that owns the exam.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the subject that owns the exam.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the grading scale for this exam.
     */
    public function gradingScale()
    {
        return $this->belongsTo(GradingScale::class);
    }

    /**
     * Get the user who created the exam.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the exam.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who published the exam.
     */
    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Get the user who locked the exam.
     */
    public function locker()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Get the marks for this exam.
     */
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Get the subjects for this exam.
     * If exam has a specific subject_id, returns that subject.
     * Otherwise, returns all subjects for the exam's class.
     */
    public function subjects()
    {
        if ($this->subject_id) {
            // If exam is for a specific subject, return that subject
            return Subject::where('id', $this->subject_id);
        } else if ($this->class_id) {
            // Get all subjects assigned to this class
            return Subject::whereHas('classes', function($query) {
                $query->where('class_id', $this->class_id);
            });
        } else {
            // Fallback to all active subjects
            return Subject::where('is_active', true);
        }
    }

    /**
     * Get the students for this exam.
     * Returns students enrolled in the exam's class and academic year.
     */
    public function students()
    {
        $query = Student::with(['currentEnrollment.class', 'currentEnrollment.program'])
                       ->where('status', 'active');

        if ($this->class_id) {
            $query->whereHas('currentEnrollment', function($q) {
                $q->where('class_id', $this->class_id)
                  ->where('academic_year_id', $this->academic_year_id);
            });
        } else {
            $query->whereHas('currentEnrollment', function($q) {
                $q->where('academic_year_id', $this->academic_year_id);
            });
        }

        return $query;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('result_status', $status);
    }

    /**
     * Scope to filter by exam type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('exam_type', $type);
    }

    /**
     * Scope to filter by academic year.
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope to filter by class.
     */
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope to search exams.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('subject', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('class', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
    }

    /**
     * Check if exam is editable.
     */
    public function getIsEditableAttribute()
    {
        return in_array($this->result_status, ['draft', 'scheduled']);
    }

    /**
     * Check if marks can be entered.
     */
    public function getCanEnterMarksAttribute()
    {
        return $this->result_status === 'ongoing';
    }

    /**
     * Check if exam is published.
     */
    public function getIsPublishedAttribute()
    {
        return in_array($this->result_status, ['published', 'locked']);
    }

    /**
     * Get status attribute (alias for result_status for compatibility).
     */
    public function getStatusAttribute()
    {
        return $this->result_status;
    }

    /**
     * Set status attribute (alias for result_status for compatibility).
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['result_status'] = $value;
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute()
    {
        return match($this->result_status) {
            'draft' => 'gray',
            'scheduled' => 'blue',
            'ongoing' => 'yellow',
            'submitted' => 'purple',
            'approved' => 'green',
            'published' => 'indigo',
            'locked' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get exam type label.
     */
    public function getTypeLabel()
    {
        return match($this->exam_type) {
            'assessment' => 'Assessment',
            'terminal' => 'Terminal Exam',
            'quiz' => 'Quiz',
            'project' => 'Project',
            'practical' => 'Practical',
            'final' => 'Final Exam',
            default => ucwords(str_replace(['_', '-'], ' ', $this->exam_type))
        };
    }
}
