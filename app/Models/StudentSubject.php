<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class StudentSubject extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_enrollment_id',
        'subject_id',
        'date_added',
        'status',
    ];

    protected $casts = [
        'date_added' => 'date',
    ];

    /**
     * Get the student enrollment that owns the student subject.
     */
    public function studentEnrollment()
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    /**
     * Get the subject that owns the student subject.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Scope to get active student subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get dropped student subjects.
     */
    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }
}
