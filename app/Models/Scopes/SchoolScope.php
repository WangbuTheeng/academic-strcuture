<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        $schoolId = $this->getSchoolContext();

        if ($schoolId) {
            $builder->where($model->getTable() . '.school_id', $schoolId);
        }
    }

    /**
     * Get the current school context
     */
    private function getSchoolContext(): ?int
    {
        // Prevent infinite recursion by checking if we're already in a scope resolution
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

            // Only check auth if user is authenticated, but avoid triggering User model loading
            if (auth()->check()) {
                $user = auth()->user();

                // Use direct database query to avoid triggering scopes
                if ($user && isset($user->id)) {
                    $userData = \DB::table('users')->where('id', $user->id)->first();

                    if ($userData) {
                        // Check if user is super-admin by checking roles table directly
                        $isSuperAdmin = \DB::table('model_has_roles')
                            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                            ->where('model_has_roles.model_id', $userData->id)
                            ->where('model_has_roles.model_type', 'App\\Models\\User')
                            ->where('roles.name', 'super-admin')
                            ->exists();

                        if ($isSuperAdmin) {
                            return null; // No scoping for super-admin
                        }

                        if ($userData->school_id) {
                            // Set session context from user's school_id for consistency
                            session(['school_context' => $userData->school_id]);
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
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder)
    {
        $this->addWithoutSchoolScope($builder);
        $this->addWithSchoolScope($builder);
        $this->addForSchool($builder);
    }

    /**
     * Add the without-school-scope extension to the builder.
     */
    protected function addWithoutSchoolScope(Builder $builder)
    {
        $builder->macro('withoutSchoolScope', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the with-school-scope extension to the builder.
     */
    protected function addWithSchoolScope(Builder $builder)
    {
        $builder->macro('withSchoolScope', function (Builder $builder) {
            return $builder->withGlobalScope('school', new static);
        });
    }

    /**
     * Add the for-school extension to the builder.
     */
    protected function addForSchool(Builder $builder)
    {
        $builder->macro('forSchool', function (Builder $builder, $schoolId) {
            return $builder->withoutGlobalScope($this)->where('school_id', $schoolId);
        });
    }
}
