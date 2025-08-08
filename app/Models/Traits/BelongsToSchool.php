<?php

namespace App\Models\Traits;

use App\Models\School;
use App\Models\Scopes\SchoolScope;

trait BelongsToSchool
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToSchool()
    {
        // Add global scope for automatic school filtering
        static::addGlobalScope(new SchoolScope);
        
        // Automatically set school_id when creating new records
        static::creating(function ($model) {
            if (!$model->school_id) {
                $schoolId = static::getCurrentSchoolContext();
                if ($schoolId) {
                    $model->school_id = $schoolId;
                } else {
                    // Prevent creation without school context (except for super admin operations)
                    if (auth()->check() && !auth()->user()->hasRole('super-admin')) {
                        throw new \Exception('Cannot create record without school context');
                    }
                }
            }
        });
    }

    /**
     * Get the school that owns this model.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Scope a query to only include models for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to exclude school filtering (for super-admin use).
     */
    public function scopeWithoutSchoolScope($query)
    {
        return $query->withoutGlobalScope(SchoolScope::class);
    }

    /**
     * Check if this model belongs to a specific school.
     */
    public function belongsToSchool($schoolId)
    {
        return $this->school_id === $schoolId;
    }

    /**
     * Check if this model belongs to the current user's school.
     */
    public function belongsToCurrentSchool()
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->school_id === auth()->user()->school_id;
    }

    /**
     * Get the school context for this model.
     */
    public function getSchoolContext()
    {
        return $this->school_id;
    }

    /**
     * Set the school context for this model.
     */
    public function setSchoolContext($schoolId)
    {
        $this->school_id = $schoolId;
        return $this;
    }

    /**
     * Get the current school context from session or user
     */
    protected static function getCurrentSchoolContext(): ?int
    {
        // Prevent infinite recursion
        static $resolving = false;
        if ($resolving) {
            return null;
        }

        $resolving = true;

        try {
            // Check session context first (for school-level authentication)
            if (session('school_context')) {
                return session('school_context');
            }

            // Fall back to user's school_id (for user-level authentication)
            if (auth()->check()) {
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

                        if (!$isSuperAdmin && $userData->school_id) {
                            return $userData->school_id;
                        }
                    }
                }
            }

            return null;
        } finally {
            $resolving = false;
        }
    }

    /**
     * Update school statistics when model changes
     */
    protected function updateSchoolStatistics()
    {
        if ($this->school_id && class_exists('App\Models\SchoolStatistics')) {
            $statistics = \App\Models\SchoolStatistics::firstOrCreate(['school_id' => $this->school_id]);
            $statistics->updateCounts();
        }
    }
}
