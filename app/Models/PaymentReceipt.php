<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToSchool;

class PaymentReceipt extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'payment_id',
        'student_id',
        'receipt_number',
        'receipt_date',
        'amount',
        'payment_method',
        'is_duplicate',
        'is_cancelled',
        'cancelled_date',
        'cancellation_reason',
        'remarks',
        'receipt_data',
        'issued_by',
        'cancelled_by',
        'school_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'receipt_date' => 'date',
        'cancelled_date' => 'date',
        'is_duplicate' => 'boolean',
        'is_cancelled' => 'boolean',
        'receipt_data' => 'array',
    ];

    /**
     * Get the payment that owns the receipt.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the student that owns the receipt.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who issued the receipt.
     */
    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the user who cancelled the receipt.
     */
    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Scope to filter active receipts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_cancelled', false);
    }

    /**
     * Scope to filter cancelled receipts.
     */
    public function scopeCancelled($query)
    {
        return $query->where('is_cancelled', true);
    }

    /**
     * Scope to filter duplicate receipts.
     */
    public function scopeDuplicate($query)
    {
        return $query->where('is_duplicate', true);
    }

    /**
     * Scope to filter original receipts.
     */
    public function scopeOriginal($query)
    {
        return $query->where('is_duplicate', false);
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rs. ' . number_format($this->amount, 2);
    }

    /**
     * Get receipt type.
     */
    public function getReceiptTypeAttribute()
    {
        if ($this->is_cancelled) {
            return 'Cancelled';
        }
        
        return $this->is_duplicate ? 'Duplicate' : 'Original';
    }

    /**
     * Get receipt status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        if ($this->is_cancelled) {
            return 'badge-danger';
        }
        
        return $this->is_duplicate ? 'badge-warning' : 'badge-success';
    }

    /**
     * Generate unique receipt number with proper locking to prevent duplicates.
     * Now school-specific to avoid conflicts between schools.
     */
    public static function generateReceiptNumber($schoolId = null)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($schoolId) {
            $year = date('Y');

            // Use current user's school if not provided
            if (!$schoolId && auth()->check()) {
                $schoolId = auth()->user()->school_id;
            }

            // Get all receipts for current year and school, extract numbers
            $query = self::where('receipt_number', 'like', "REC-{$year}-%");

            if ($schoolId) {
                $query->where('school_id', $schoolId);
            }

            $existingReceipts = $query->pluck('receipt_number')->toArray();

            $maxNumber = 0;

            foreach ($existingReceipts as $receiptNumber) {
                // Handle both 3-digit and 6-digit formats
                if (preg_match('/REC-\d{4}-(\d+)/', $receiptNumber, $matches)) {
                    $number = (int)$matches[1];
                    if ($number > $maxNumber) {
                        $maxNumber = $number;
                    }
                }
            }

            $nextNumber = $maxNumber + 1;

            // Use 6-digit format for consistency
            return 'REC-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            if (empty($receipt->receipt_number)) {
                // Generate unique receipt number with retry logic
                $maxRetries = 5;
                $retryCount = 0;

                do {
                    $receipt->receipt_number = self::generateReceiptNumber($receipt->school_id);
                    // Check uniqueness within the same school
                    $exists = self::where('receipt_number', $receipt->receipt_number)
                                ->where('school_id', $receipt->school_id)
                                ->exists();
                    $retryCount++;
                } while ($exists && $retryCount < $maxRetries);

                if ($exists) {
                    throw new \Exception('Unable to generate unique receipt number after ' . $maxRetries . ' attempts');
                }
            }
        });
    }
}
