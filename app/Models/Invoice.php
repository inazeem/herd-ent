<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_number',
        'patient_id',
        'encounter_id',
        'invoice_date',
        'due_date',
        'total_amount',
        'amount_paid',
        'amount_due',
        'status',
        'notes',
        'created_by'
    ];
    
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
    ];
    
    /**
     * Get the patient this invoice belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    
    /**
     * Get the encounter this invoice is for.
     */
    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }
    
    /**
     * Get the user who created this invoice.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get all line items for this invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
    
    /**
     * Get all payments made for this invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    
    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'ENT-INV';
        $year = date('Y');
        $month = date('m');
        $lastInvoice = self::latest()->first();
        
        if ($lastInvoice) {
            $lastId = (int)substr($lastInvoice->invoice_number, -5);
            $newId = $lastId + 1;
        } else {
            $newId = 1;
        }
        
        return $prefix . '-' . $year . $month . '-' . str_pad($newId, 5, '0', STR_PAD_LEFT);
    }
    
    /**
     * Format total amount with currency symbol.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total_amount, 2);
    }
    
    /**
     * Format amount due with currency symbol.
     */
    public function getFormattedAmountDueAttribute(): string
    {
        return '$' . number_format($this->amount_due, 2);
    }
    
    /**
     * Format amount paid with currency symbol.
     */
    public function getFormattedAmountPaidAttribute(): string
    {
        return '$' . number_format($this->amount_paid, 2);
    }
    
    /**
     * Calculate if the invoice is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }
    
    /**
     * Update the invoice totals after adding or removing items.
     */
    public function updateTotals()
    {
        $total = $this->items()->sum('total_price');
        $paid = $this->payments()->sum('amount');
        
        $this->total_amount = $total;
        $this->amount_paid = $paid;
        $this->amount_due = $total - $paid;
        
        // Update status based on payment
        if ($this->amount_due <= 0) {
            $this->status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partially_paid';
        } elseif ($this->due_date < now()) {
            $this->status = 'overdue';
        }
        
        $this->save();
        
        return $this;
    }
    
    /**
     * Add a payment to this invoice.
     */
    public function addPayment(float $amount, string $method, string $transactionId = null, string $notes = null, int $recordedBy)
    {
        $payment = $this->payments()->create([
            'amount' => $amount,
            'payment_date' => now(),
            'payment_method' => $method,
            'transaction_id' => $transactionId,
            'notes' => $notes,
            'recorded_by' => $recordedBy
        ]);
        
        $this->updateTotals();
        
        return $payment;
    }
}
