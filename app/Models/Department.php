<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Department extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'faculty_id',
        'name',
        'code'
    ];

    /**
     * Get the faculty that owns the department.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the programs for this department.
     */
    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the classes for this department.
     */
    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    /**
     * Get the subjects for this department.
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Scope to search departments.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('faculty', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
    }

    /**
     * Scope to filter by faculty.
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    /**
     * Get the total number of programs.
     */
    public function getProgramCountAttribute()
    {
        return $this->programs()->count();
    }

    /**
     * Get the total number of classes.
     */
    public function getClassCountAttribute()
    {
        return $this->classes()->count();
    }

    /**
     * Get the total number of subjects.
     */
    public function getSubjectCountAttribute()
    {
        return $this->subjects()->count();
    }
}
