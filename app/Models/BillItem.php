<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class BillItem extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'bill_id',
        'fee_structure_id',
        'fee_category',
        'description',
        'unit_amount',
        'quantity',
        'total_amount',
        'discount_percentage',
        'discount_amount',
        'final_amount',
        'is_paid',
        'paid_amount',
    ];

    protected $casts = [
        'unit_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'quantity' => 'integer',
        'is_paid' => 'boolean',
    ];

    /**
     * Get the bill that owns the bill item.
     */
    public function bill()
    {
        return $this->belongsTo(StudentBill::class, 'bill_id');
    }

    /**
     * Get the fee structure that owns the bill item.
     */
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    /**
     * Scope to filter paid items.
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope to filter unpaid items.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope to filter by fee category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('fee_category', $category);
    }

    /**
     * Get formatted unit amount.
     */
    public function getFormattedUnitAmountAttribute()
    {
        return 'Rs. ' . number_format($this->unit_amount, 2);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAmountAttribute()
    {
        return 'Rs. ' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted final amount.
     */
    public function getFormattedFinalAmountAttribute()
    {
        return 'Rs. ' . number_format($this->final_amount, 2);
    }

    /**
     * Get formatted paid amount.
     */
    public function getFormattedPaidAmountAttribute()
    {
        return 'Rs. ' . number_format($this->paid_amount, 2);
    }

    /**
     * Get remaining amount.
     */
    public function getRemainingAmountAttribute()
    {
        return $this->final_amount - $this->paid_amount;
    }

    /**
     * Get formatted remaining amount.
     */
    public function getFormattedRemainingAmountAttribute()
    {
        return 'Rs. ' . number_format($this->remaining_amount, 2);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            // Calculate total amount
            $item->total_amount = $item->unit_amount * $item->quantity;
            
            // Calculate final amount after discount
            if ($item->discount_percentage > 0) {
                $item->discount_amount = ($item->total_amount * $item->discount_percentage) / 100;
            }
            
            $item->final_amount = $item->total_amount - $item->discount_amount;
            
            // Update paid status
            $item->is_paid = $item->paid_amount >= $item->final_amount;
        });
    }
}
