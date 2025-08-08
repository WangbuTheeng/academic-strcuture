<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToSchool;
use App\Services\DataIsolationService;

class Student extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'religion',
        'caste',
        'nationality',
        'mother_tongue',
        'phone',
        'email',
        'address',
        'temporary_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'guardian_name',
        'guardian_relation',
        'guardian_phone',
        'guardian_email',
        'citizenship_number',
        'citizenship_issue_date',
        'citizenship_issue_district',
        'citizenship_document',
        'previous_school_name',
        'transfer_certificate_no',
        'transfer_certificate_date',
        'migration_certificate_no',
        'disability_status',
        'special_needs',
        'admission_number',
        'admission_date',
        'photo',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'citizenship_issue_date' => 'date',
        'transfer_certificate_date' => 'date',
        'admission_date' => 'date',
    ];

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the student's enrollments.
     */
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Get the student's current enrollment.
     */
    public function currentEnrollment()
    {
        return $this->hasOne(StudentEnrollment::class)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_current', true);
            });
    }

    /**
     * Get the student's marks.
     */
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Get the student's documents.
     */
    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    /**
     * Get the student's subjects.
     */
    public function subjects()
    {
        return $this->hasMany(StudentSubject::class);
    }

    /**
     * Get the student's promotion records.
     */
    public function promotionRecords()
    {
        return $this->hasMany(PromotionRecord::class);
    }

    /**
     * Get the student's current class through enrollment.
     */
    public function class()
    {
        return $this->hasOneThrough(
            ClassModel::class,
            StudentEnrollment::class,
            'student_id',
            'id',
            'id',
            'class_id'
        );
    }

    /**
     * Get the student's current class (accessor method).
     */
    public function getClassAttribute()
    {
        return $this->currentEnrollment?->class;
    }

    /**
     * Get the student's current program through enrollment.
     */
    public function program()
    {
        return $this->hasOneThrough(
            Program::class,
            StudentEnrollment::class,
            'student_id',
            'id',
            'id',
            'program_id'
        )->whereHas('academicYear', function ($query) {
            $query->where('is_current', true);
        });
    }

    /**
     * Get roll number from current enrollment.
     */
    public function getRollNumberAttribute()
    {
        return $this->currentEnrollment?->roll_no;
    }

    /**
     * Scope to filter active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to search students by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('admission_number', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter students by class.
     */
    public function scopeInClass($query, $classId)
    {
        return $query->whereHas('currentEnrollment', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }

    /**
     * Scope to filter students by academic year.
     */
    public function scopeInAcademicYear($query, $academicYearId)
    {
        return $query->whereHas('enrollments', function ($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        });
    }

    /**
     * Get the student's bills.
     */
    public function bills()
    {
        return $this->hasMany(StudentBill::class);
    }

    /**
     * Get the student's payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the student's payment receipts.
     */
    public function paymentReceipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    /**
     * Get the student's current bills.
     */
    public function currentBills()
    {
        return $this->hasMany(StudentBill::class)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_current', true);
            });
    }

    /**
     * Get the student's pending bills.
     */
    public function pendingBills()
    {
        return $this->hasMany(StudentBill::class)
            ->whereIn('status', ['pending', 'partial', 'overdue']);
    }

    /**
     * Get total outstanding amount.
     */
    public function getTotalOutstandingAttribute()
    {
        return $this->pendingBills->sum('balance_amount');
    }

    /**
     * Get formatted total outstanding amount.
     */
    public function getFormattedTotalOutstandingAttribute()
    {
        return 'Rs. ' . number_format($this->total_outstanding, 2);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            // Auto-generate admission number if not provided
            if (!$student->admission_number && $student->school_id) {
                $dataIsolationService = app(DataIsolationService::class);
                $student->admission_number = $dataIsolationService->generateAdmissionNumber($student->school_id);
            }
        });
    }
}
