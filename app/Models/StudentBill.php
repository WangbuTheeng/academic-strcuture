<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToSchool;
use Carbon\Carbon;

class StudentBill extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'academic_year_id',
        'class_id',
        'program_id',
        'bill_number',
        'bill_title',
        'description',
        'total_amount',
        'discount_amount',
        'late_fee_amount',
        'paid_amount',
        'balance_amount',
        'bill_date',
        'due_date',
        'last_payment_date',
        'status',
        'is_locked',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'bill_date' => 'date',
        'due_date' => 'date',
        'last_payment_date' => 'date',
        'is_locked' => 'boolean',
    ];

    /**
     * Get the student that owns the bill.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic year that owns the bill.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the class that owns the bill.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the program that owns the bill.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user who created the bill.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the bill.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the bill items for the bill.
     */
    public function billItems()
    {
        return $this->hasMany(BillItem::class, 'bill_id');
    }

    /**
     * Get the payments for the bill.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }

    /**
     * Get the verified payments for the bill.
     */
    public function verifiedPayments()
    {
        return $this->hasMany(Payment::class, 'bill_id')->where('is_verified', true);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter pending bills.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter paid bills.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to filter overdue bills.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function ($q) {
                        $q->whereIn('status', ['pending', 'partial'])
                          ->where('due_date', '<', Carbon::today());
                    });
    }

    /**
     * Scope to filter by academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope to filter by student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('bill_date', [$startDate, $endDate]);
    }

    /**
     * Check if bill is overdue.
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date < Carbon::today() && !in_array($this->status, ['paid', 'cancelled']);
    }

    /**
     * Get days overdue.
     */
    public function getDaysOverdueAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return Carbon::today()->diffInDays($this->due_date);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAmountAttribute()
    {
        return 'Rs. ' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted paid amount.
     */
    public function getFormattedPaidAmountAttribute()
    {
        return 'Rs. ' . number_format($this->paid_amount, 2);
    }

    /**
     * Get formatted balance amount.
     */
    public function getFormattedBalanceAmountAttribute()
    {
        return 'Rs. ' . number_format($this->balance_amount, 2);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'partial' => 'badge-info',
            'paid' => 'badge-success',
            'overdue' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            default => 'badge-secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'partial' => 'Partially Paid',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Update bill amounts and status.
     */
    public function updateAmounts()
    {
        $this->total_amount = $this->billItems->sum('final_amount') + $this->late_fee_amount - $this->discount_amount;
        $this->paid_amount = $this->verifiedPayments->sum('amount');
        $this->balance_amount = $this->total_amount - $this->paid_amount;
        
        // Update status based on payment
        if ($this->balance_amount <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } elseif ($this->is_overdue) {
            $this->status = 'overdue';
        } else {
            $this->status = 'pending';
        }
        
        $this->save();
    }

    /**
     * Generate unique bill number.
     */
    public static function generateBillNumber()
    {
        $year = date('Y');
        $lastBill = self::whereYear('created_at', $year)
                       ->orderBy('id', 'desc')
                       ->first();

        $number = $lastBill ? (int)substr($lastBill->bill_number, -3) + 1 : 1;

        return 'BILL-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bill) {
            if (empty($bill->bill_number)) {
                $bill->bill_number = self::generateBillNumber();
            }
        });
    }
}
