<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Subject extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'department_id',
        'name',
        'code',
        'credit_hours',
        'subject_type',
        'max_theory',
        'max_practical',
        'max_assess',
        'is_practical',
        'has_internal',
        'is_active'
    ];

    protected $casts = [
        'credit_hours' => 'decimal:2',
        'max_theory' => 'integer',
        'max_practical' => 'integer',
        'max_assess' => 'integer',
        'is_practical' => 'boolean',
        'has_internal' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the department that owns the subject.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the programs that include this subject.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_subjects')
                    ->withPivot(['is_compulsory', 'credit_hours', 'year_no'])
                    ->withTimestamps();
    }

    /**
     * Get the teacher assignments for this subject.
     */
    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    /**
     * Get the classes that include this subject.
     */
    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'class_subjects', 'subject_id', 'class_id')
                    ->withPivot(['is_compulsory', 'credit_hours', 'year_no', 'sort_order'])
                    ->withTimestamps()
                    ->orderBy('sort_order');
    }

    /**
     * Get the teachers assigned to this subject.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_subjects')
                    ->withPivot(['class_id', 'academic_year_id'])
                    ->withTimestamps();
    }

    /**
     * Get the student enrollments for this subject.
     */
    public function studentSubjects()
    {
        return $this->hasMany(StudentSubject::class);
    }

    /**
     * Get the marks for this subject.
     */
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Scope to get active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search subjects.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
    }

    /**
     * Scope to filter by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to filter by subject type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('subject_type', $type);
    }

    /**
     * Get the total number of programs using this subject.
     */
    public function getProgramCountAttribute()
    {
        return $this->programs()->count();
    }

    /**
     * Get the total number of teachers assigned.
     */
    public function getTeacherCountAttribute()
    {
        return $this->teachers()->count();
    }
}
