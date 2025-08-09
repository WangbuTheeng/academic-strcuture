<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\FeeStructure;
use App\Models\StudentBill;
use App\Models\Student;

class SchoolDataIsolation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply isolation if user is authenticated and has a school
        if (auth()->check() && auth()->user()->school_id) {
            $schoolId = auth()->user()->school_id;

            // Add global scopes for school-specific models
            FeeStructure::addGlobalScope('school', function ($builder) use ($schoolId) {
                $builder->where('school_id', $schoolId);
            });

            StudentBill::addGlobalScope('school', function ($builder) use ($schoolId) {
                $builder->where('school_id', $schoolId);
            });

            Student::addGlobalScope('school', function ($builder) use ($schoolId) {
                $builder->where('school_id', $schoolId);
            });
        }

        return $next($request);
    }
}
