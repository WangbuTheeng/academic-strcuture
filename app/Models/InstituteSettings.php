<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class InstituteSettings extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'institution_name',
        'institution_address',
        'institution_phone',
        'institution_email',
        'institution_website',
        'institution_logo',
        'institution_seal',
        'principal_name',
        'principal_phone',
        'principal_email',
        'academic_year_start_month',
        'academic_year_end_month',
        'default_grading_scale_id',
        'setup_completed',
        'setup_completed_at',
        'settings_data'
    ];

    protected $casts = [
        'setup_completed' => 'boolean',
        'setup_completed_at' => 'datetime',
        'settings_data' => 'array',
        'academic_year_start_month' => 'integer',
        'academic_year_end_month' => 'integer',
    ];

    /**
     * Get the default grading scale.
     */
    public function defaultGradingScale()
    {
        return $this->belongsTo(GradingScale::class, 'default_grading_scale_id');
    }

    /**
     * Get the institution logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->institution_logo ? asset('storage/' . $this->institution_logo) : null;
    }

    /**
     * Safely get the logo URL even for object instances.
     */
    public function getLogoUrl(): ?string
    {
        if ($this->institution_logo) {
            return asset('storage/' . $this->institution_logo);
        }
        return null;
    }

    /**
     * Get the institution seal URL.
     */
    public function getSealUrlAttribute(): ?string
    {
        return $this->institution_seal ? asset('storage/' . $this->institution_seal) : null;
    }

    /**
     * Get the current institute settings for the current school context.
     */
    public static function current(): ?self
    {
        // The BelongsToSchool trait should automatically scope this query
        // but let's be explicit about it for the current school context
        $schoolId = session('school_context');

        if ($schoolId) {
            return static::where('school_id', $schoolId)->first();
        }

        // Fallback to any settings if no school context (shouldn't happen in normal flow)
        return static::where('setup_completed', true)->first();
    }

    /**
     * Check if setup is completed.
     */
    public static function isSetupCompleted(): bool
    {
        return static::where('setup_completed', true)->exists();
    }

    /**
     * Get a specific setting value.
     */
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings_data, $key, $default);
    }

    /**
     * Set a specific setting value.
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings_data ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings_data' => $settings]);
    }

    /**
     * Get academic year months as array.
     */
    public function getAcademicYearMonthsAttribute(): array
    {
        return [
            'start' => $this->academic_year_start_month,
            'end' => $this->academic_year_end_month,
        ];
    }

    /**
     * Get formatted institution address.
     */
    public function getFormattedAddressAttribute(): string
    {
        $address = $this->institution_address;

        if ($this->institution_phone) {
            $address .= "\nPhone: " . $this->institution_phone;
        }

        if ($this->institution_email) {
            $address .= "\nEmail: " . $this->institution_email;
        }

        if ($this->institution_website) {
            $address .= "\nWebsite: " . $this->institution_website;
        }

        return $address;
    }
}
