<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class SchoolScopedModel extends Model
{
    /**
     * Boot the model and add global scope for school context
     */
    protected static function boot()
    {
        parent::boot();
        
        // Suggestion: It's generally better to avoid direct session access in models to keep them
        // decoupled from the web layer. This makes them easier to use in contexts
        // like Artisan commands or queued jobs. Consider using a dedicated context
        // service or ensuring the school_id is set explicitly before saving.
        static::creating(function ($model) {
            if (!$model->school_id && session()->has('school_context')) {
                $model->school_id = session('school_context');
            }
        });
        
        // The global scope is a great way to enforce multi-tenancy.
        // Suggestion: For non-web requests (like commands or jobs), you'll need a way
        // to set this context manually or bypass the scope with withoutSchoolScope().
        // Adding a check for `runningInConsole` can prevent the scope from applying unexpectedly.
        static::addGlobalScope('school', function (Builder $builder) {
            if (session()->has('school_context') && !app()->runningInConsole()) {
                $builder->where('school_id', session('school_context'));
            }
        });
    }

    /**
     * Get the school that owns this model
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Scope query to a specific school
     */
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope query to active schools only
     */
    public function scopeActiveSchools(Builder $query): Builder
    {
        return $query->whereHas('school', function (Builder $query) {
            $query->where('status', 'active');
        });
    }

    /**
     * Get all records without school scope (for super admin use)
     */
    public static function withoutSchoolScope()
    {
        return static::withoutGlobalScope('school');
    }

    /**
     * Create a new instance for a specific school
     */
    public static function createForSchool(int $schoolId, array $attributes = [])
    {
        $attributes['school_id'] = $schoolId;
        return static::create($attributes);
    }

    /**
     * Update school statistics when model is created/updated/deleted
     */
    protected static function booted()
    {
        static::created(function ($model) {
            $model->updateSchoolStatistics();
        });

        static::updated(function ($model) {
            $model->updateSchoolStatistics();
        });

        static::deleted(function ($model) {
            $model->updateSchoolStatistics();
        });
    }

    /**
     * Update school statistics (to be implemented by child classes if needed)
     */
    protected function updateSchoolStatistics()
    {
        if ($this->school_id) {
            $statistics = SchoolStatistics::firstOrCreate(['school_id' => $this->school_id]);
            
            // Update relevant counts based on model type
            $this->updateSpecificStatistics($statistics);
        }
    }

    /**
     * Update specific statistics (override in child classes)
     */
    protected function updateSpecificStatistics(SchoolStatistics $statistics)
    {
        // Default implementation - child classes should override this
        $statistics->touch(); // Update last_activity
    }

    /**
     * Check if current user can access this school's data
     */
    public function canAccess(): bool
    {
        // This is a good check. For more complex authorization logic, consider moving this
        // to a Laravel Policy class (e.g., a generic SchoolScopedModelPolicy).
        // Policies help centralize authorization logic, making it more reusable and
        // aligned with Laravel conventions. You can then use checks like
        // `$this->authorize('view', $model)` in your controllers.
        // See: https://laravel.com/docs/authorization#creating-policies

        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Super admins can access all data
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // School users can only access their own school's data
        return $user->school_id === $this->school_id;
    }

    /**
     * Ensure school context is set before operations
     */
    public function save(array $options = [])
    {
        if (!$this->school_id && session()->has('school_context')) {
            $this->school_id = session('school_context');
        }

        return parent::save($options);
    }

    /**
     * Get the table name with school prefix for partitioning (future use)
     */
    public function getSchoolTable(): string
    {
        return $this->getTable() . '_school_' . $this->school_id;
    }

    /**
     * Validate that school_id is set
     */
    public function validateSchoolContext(): bool
    {
        return !is_null($this->school_id);
    }

    /**
     * Get school code for this model
     */
    public function getSchoolCode(): ?string
    {
        return $this->school?->code;
    }

    /**
     * Get school name for this model
     */
    public function getSchoolName(): ?string
    {
        return $this->school?->name;
    }

    /**
     * Check if the model belongs to an active school
     */
    public function isSchoolActive(): bool
    {
        return $this->school?->status === 'active';
    }

    /**
     * Scope to get records from multiple schools (super admin only)
     */
    public function scopeFromSchools(Builder $query, array $schoolIds): Builder
    {
        return $query->withoutGlobalScope('school')->whereIn('school_id', $schoolIds);
    }

    /**
     * Get the school context for this model
     */
    public function getSchoolContext(): array
    {
        return [
            'school_id' => $this->school_id,
            'school_code' => $this->getSchoolCode(),
            'school_name' => $this->getSchoolName(),
            'school_status' => $this->school?->status
        ];
    }

    /**
     * Clone model to another school (super admin only)
     */
    public function cloneToSchool(int $targetSchoolId): static
    {
        $newModel = $this->replicate();
        $newModel->school_id = $targetSchoolId;
        $newModel->save();
        return $newModel;
    }

    /**
     * Get audit trail for this model
     */
    public function getAuditTrail()
    {
        return AuditLog::where('resource_type', $this->getTable())
            ->where('resource_id', $this->id)
            ->orderBy('timestamp', 'desc')
            ->get();
    }

    /**
     * Log model activity
     */
    protected function logActivity(string $action, array $oldValues = [], array $newValues = [])
    {
        if (app()->bound(AuditLogger::class)) {
            app(AuditLogger::class)->logActivity($action, [
                'resource_type' => $this->getTable(),
                'resource_id' => $this->id,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'category' => 'model_activity'
            ]);
        }
    }
}
