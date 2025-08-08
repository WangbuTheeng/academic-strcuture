<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\BelongsToSchool;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, BelongsToSchool;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'school_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the teacher's subject assignments.
     */
    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    /**
     * Get the subjects this teacher is assigned to.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects')
                    ->withPivot('class_id', 'academic_year_id', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is principal.
     */
    public function isPrincipal()
    {
        return $this->hasRole('principal');
    }

    /**
     * Check if user is teacher.
     */
    public function isTeacher()
    {
        return $this->hasRole('teacher');
    }

    /**
     * Check if user is student.
     */
    public function isStudent()
    {
        return $this->hasRole('student');
    }

    /**
     * Check if user is super-admin.
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Get the school this user belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the school context for this user.
     */
    public function getSchoolContext()
    {
        return $this->school_id;
    }

    /**
     * Check if user belongs to a specific school.
     */
    public function belongsToSchool($schoolId)
    {
        return $this->school_id === $schoolId;
    }

    /**
     * Boot the model to handle super-admin special cases
     */
    protected static function boot()
    {
        parent::boot();

        // Override the BelongsToSchool trait behavior for super-admin users
        static::addGlobalScope('user_school_scope', function ($builder) {
            // Prevent infinite recursion
            static $resolving = false;
            if ($resolving) {
                return;
            }

            $resolving = true;

            try {
                // Check session context first
                $schoolId = session('school_context');

                if (!$schoolId && auth()->check()) {
                    $user = auth()->user();
                    if ($user && isset($user->id)) {
                        // Use direct database query to avoid infinite recursion
                        $userData = \DB::table('users')->where('id', $user->id)->first();

                        if ($userData) {
                            // Check if user is super-admin
                            $isSuperAdmin = \DB::table('model_has_roles')
                                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                ->where('model_has_roles.model_id', $userData->id)
                                ->where('model_has_roles.model_type', 'App\\Models\\User')
                                ->where('roles.name', 'super-admin')
                                ->exists();

                            if ($isSuperAdmin) {
                                return; // No scoping for super-admin
                            }

                            $schoolId = $userData->school_id;
                        }
                    }
                }

                if ($schoolId) {
                    $builder->where('school_id', $schoolId);
                }
            } finally {
                $resolving = false;
            }
        });
    }
}
