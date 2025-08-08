<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Faculty extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'code'
    ];

    /**
     * Get the departments for this faculty.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the programs through departments.
     */
    public function programs()
    {
        return $this->hasManyThrough(Program::class, Department::class);
    }

    /**
     * Get the classes through departments.
     */
    public function classes()
    {
        return $this->hasManyThrough(ClassModel::class, Department::class);
    }

    /**
     * Scope to search faculties.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
    }

    /**
     * Get the total number of departments.
     */
    public function getDepartmentCountAttribute()
    {
        return $this->departments()->count();
    }

    /**
     * Get the total number of programs.
     */
    public function getProgramCountAttribute()
    {
        return $this->programs()->count();
    }
}
