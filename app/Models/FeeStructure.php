<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToSchool;

class FeeStructure extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'academic_year_id',
        'level_id',
        'program_id',
        'class_id',
        'fee_category',
        'fee_name',
        'description',
        'amount',
        'is_mandatory',
        'is_active',
        'due_date_offset',
        'billing_frequency',
        'late_fee_amount',
        'grace_period_days',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'due_date_offset' => 'integer',
        'grace_period_days' => 'integer',
    ];

    /**
     * Get the academic year that owns the fee structure.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the level that owns the fee structure.
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Get the program that owns the fee structure.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the class that owns the fee structure.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the bill items for the fee structure.
     */
    public function billItems()
    {
        return $this->hasMany(BillItem::class);
    }

    /**
     * Scope to filter active fee structures.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by fee category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('fee_category', $category);
    }

    /**
     * Scope to filter by academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope to filter by level.
     */
    public function scopeForLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    /**
     * Scope to filter by program.
     */
    public function scopeForProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope to filter by class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope to filter mandatory fees.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope to filter optional fees.
     */
    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }

    /**
     * Get fee categories as array.
     */
    public static function getFeeCategories()
    {
        return [
            'tuition' => 'Tuition Fees',
            'laboratory' => 'Laboratory Fees',
            'library' => 'Library Fees',
            'examination' => 'Examination Fees',
            'activity' => 'Activity Fees',
            'transport' => 'Transport Fees',
            'hostel' => 'Hostel Fees',
            'miscellaneous' => 'Miscellaneous Fees',
        ];
    }

    /**
     * Get billing frequencies as array.
     */
    public static function getBillingFrequencies()
    {
        return [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semester' => 'Semester',
            'annual' => 'Annual',
        ];
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rs. ' . number_format($this->amount, 2);
    }

    /**
     * Get formatted late fee amount.
     */
    public function getFormattedLateFeeAmountAttribute()
    {
        return 'Rs. ' . number_format($this->late_fee_amount, 2);
    }

    /**
     * Get fee category label.
     */
    public function getFeeCategoryLabelAttribute()
    {
        $categories = self::getFeeCategories();
        return $categories[$this->fee_category] ?? $this->fee_category;
    }

    /**
     * Get billing frequency label.
     */
    public function getBillingFrequencyLabelAttribute()
    {
        $frequencies = self::getBillingFrequencies();
        return $frequencies[$this->billing_frequency] ?? $this->billing_frequency;
    }
}
