<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'billing_code_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    /**
     * Get the invoice that this item belongs to.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the billing code associated with this invoice item.
     */
    public function billingCode(): BelongsTo
    {
        return $this->belongsTo(BillingCode::class);
    }
}
