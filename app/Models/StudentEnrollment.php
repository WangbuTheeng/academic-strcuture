<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_id',
        'program_id',
        'roll_no',
        'section',
        'enrollment_date',
        'status',
        'academic_standing',
        'backlog_count'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'backlog_count' => 'integer',
    ];

    /**
     * Scope to get enrollments with valid students only
     */
    public function scopeWithValidStudents($query)
    {
        return $query->whereHas('student', function($q) {
            $q->whereNull('deleted_at');
        });
    }

    /**
     * Get the student that owns the enrollment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class)->withoutTrashed();
    }

    /**
     * Get the academic year that owns the enrollment.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the class that owns the enrollment.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the program that owns the enrollment.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the student subjects for this enrollment.
     */
    public function studentSubjects()
    {
        return $this->hasMany(StudentSubject::class, 'student_enrollment_id');
    }

    /**
     * Scope to get active enrollments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get current year enrollments.
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereHas('academicYear', function ($q) {
            $q->where('is_current', true);
        });
    }
}
