<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Program extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'department_id',
        'level_id',
        'name',
        'code',
        'duration_years',
        'degree_type',
        'program_type',
        'description',
        'is_active'
    ];

    /**
     * Get the department that owns the program.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the level that owns the program.
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Get the student enrollments for this program.
     */
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Get the subjects associated with this program.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'program_subjects')
                    ->withPivot(['is_compulsory', 'credit_hours', 'year_no'])
                    ->withTimestamps();
    }

    /**
     * Get the classes associated with this program.
     */
    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'program_classes', 'program_id', 'class_id')
                    ->withPivot(['year_no'])
                    ->withTimestamps();
    }

    /**
     * Get the academic year associated with this program.
     * Note: This assumes programs are linked to academic years through enrollments or a direct relationship.
     * If there's no direct relationship, this returns the current academic year.
     */
    public function academicYear()
    {
        // If there's a direct academic_year_id field, use it
        if (isset($this->attributes['academic_year_id'])) {
            return $this->belongsTo(AcademicYear::class);
        }

        // Otherwise, get the academic year from the most recent enrollment
        return $this->hasOneThrough(
            AcademicYear::class,
            StudentEnrollment::class,
            'program_id', // Foreign key on enrollments table
            'id', // Foreign key on academic_years table
            'id', // Local key on programs table
            'academic_year_id' // Local key on enrollments table
        )->latest();
    }

    /**
     * Get the current academic year for this program.
     */
    public function getCurrentAcademicYear()
    {
        return AcademicYear::where('is_current', true)->first()
            ?? AcademicYear::latest()->first();
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope to search programs.
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
     * Scope to filter by level.
     */
    public function scopeByLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    /**
     * Scope to filter by program type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('program_type', $type);
    }

    /**
     * Scope to get active programs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
