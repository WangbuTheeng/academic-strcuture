<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class ClassModel extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'level_id',
        'department_id',
        'name',
        'code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the level that owns the class.
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Get the department that owns the class.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the student enrollments for this class.
     */
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'class_id');
    }

    /**
     * Get the students enrolled in this class.
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            StudentEnrollment::class,
            'class_id',
            'id',
            'id',
            'student_id'
        )->where('student_enrollments.status', 'active');
    }

    /**
     * Get the programs associated with this class.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_classes', 'class_id', 'program_id')
                    ->withPivot(['year_no'])
                    ->withTimestamps();
    }

    /**
     * Get the subjects associated with this class.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
                    ->withPivot(['is_compulsory', 'credit_hours', 'year_no', 'sort_order'])
                    ->withTimestamps()
                    ->orderBy('sort_order');
    }

    /**
     * Get the programs that belong to the same department as this class.
     */
    public function departmentPrograms()
    {
        return $this->hasMany(Program::class, 'department_id', 'department_id');
    }


    /**
     * Scope to get active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by level.
     */
    public function scopeByLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    /**
     * Scope to filter by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
