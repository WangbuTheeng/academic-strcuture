<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToSchool;

class Payment extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'student_id',
        'bill_id',
        'payment_number',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'bank_name',
        'cheque_number',
        'cheque_date',
        'status',
        'is_verified',
        'verification_date',
        'verified_by',
        'notes',
        'remarks',
        'created_by',
        'updated_by',
        'school_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'cheque_date' => 'date',
        'verification_date' => 'date',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the student that owns the payment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the bill that owns the payment.
     */
    public function bill()
    {
        return $this->belongsTo(StudentBill::class, 'bill_id');
    }

    /**
     * Get the user who created the payment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who verified the payment.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the payment receipts for the payment.
     */
    public function receipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    /**
     * Get the latest receipt for the payment.
     */
    public function latestReceipt()
    {
        return $this->hasOne(PaymentReceipt::class)->latest();
    }

    /**
     * Scope to filter verified payments.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to filter pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter by payment method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Get payment methods as array.
     */
    public static function getPaymentMethods()
    {
        return [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'online' => 'Online Payment',
            'cheque' => 'Cheque',
            'card' => 'Credit/Debit Card',
            'mobile_wallet' => 'Mobile Wallet',
        ];
    }

    /**
     * Get payment statuses as array.
     */
    public static function getPaymentStatuses()
    {
        return [
            'pending' => 'Pending',
            'verified' => 'Verified',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
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
     * Get payment method label.
     */
    public function getPaymentMethodLabelAttribute()
    {
        $methods = self::getPaymentMethods();
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        $statuses = self::getPaymentStatuses();
        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'verified' => 'badge-success',
            'failed' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            default => 'badge-secondary',
        };
    }

    /**
     * Generate unique payment number with proper locking to prevent duplicates.
     * Now school-specific to avoid conflicts between schools.
     */
    public static function generatePaymentNumber($schoolId = null)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($schoolId) {
            $year = date('Y');

            // Use current user's school if not provided
            if (!$schoolId && auth()->check()) {
                $schoolId = auth()->user()->school_id;
            }

            // Get all payments for current year and school, extract numbers
            $query = self::where('payment_number', 'like', "PAY-{$year}-%");

            if ($schoolId) {
                $query->where('school_id', $schoolId);
            }

            $existingPayments = $query->pluck('payment_number')->toArray();

            $maxNumber = 0;

            foreach ($existingPayments as $paymentNumber) {
                // Handle both 3-digit and 6-digit formats
                if (preg_match('/PAY-\d{4}-(\d+)/', $paymentNumber, $matches)) {
                    $number = (int)$matches[1];
                    if ($number > $maxNumber) {
                        $maxNumber = $number;
                    }
                }
            }

            $nextNumber = $maxNumber + 1;

            // Use 6-digit format for consistency
            return 'PAY-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                // Generate unique payment number with retry logic
                $maxRetries = 5;
                $retryCount = 0;

                do {
                    $payment->payment_number = self::generatePaymentNumber($payment->school_id);
                    // Check uniqueness within the same school
                    $exists = self::where('payment_number', $payment->payment_number)
                                ->where('school_id', $payment->school_id)
                                ->exists();
                    $retryCount++;
                } while ($exists && $retryCount < $maxRetries);

                if ($exists) {
                    throw new \Exception('Unable to generate unique payment number after ' . $maxRetries . ' attempts');
                }
            }
        });

        static::updated(function ($payment) {
            // Update bill amounts when payment is verified
            if ($payment->isDirty('is_verified') && $payment->is_verified) {
                $payment->bill->updateAmounts();
            }
        });
    }
}
