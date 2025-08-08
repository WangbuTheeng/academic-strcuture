<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'password', 'email', 'phone',
        'address', 'logo_path', 'status', 'settings', 'created_by'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'settings' => 'array',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statistics()
    {
        return $this->hasOne(SchoolStatistics::class);
    }

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function levels()
    {
        return $this->hasMany(Level::class);
    }

    // Mutators
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public static function findByCode($code)
    {
        return static::where('code', $code)->first();
    }

    public function getActiveUsersCount()
    {
        return $this->users()->count();
    }

    public function getActiveStudentsCount()
    {
        return $this->students()->count();
    }
}
