<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingCode extends Model
{
    use HasFactory;
    
    /**
     * Constants for billing code types.
     */
    public const TYPE_CONSULTATION = 'consultation';
    public const TYPE_PROCEDURE = 'procedure';
    public const TYPE_LABORATORY = 'laboratory';
    public const TYPE_MEDICATION = 'medication';
    public const TYPE_SUPPLY = 'supply';
    // Add your new code type here
    public const TYPE_IMAGING = 'imaging';
    
    /**
     * Get all available billing code types.
     *
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_CONSULTATION => 'Consultation',
            self::TYPE_PROCEDURE => 'Procedure',
            self::TYPE_LABORATORY => 'Laboratory',
            self::TYPE_MEDICATION => 'Medication',
            self::TYPE_SUPPLY => 'Supply',
            self::TYPE_IMAGING => 'Imaging', // Your new code type
        ];
    }
    
    protected $fillable = [
        'code',
        'description',
        'type',
        'default_price',
        'active'
    ];
    
    protected $casts = [
        'default_price' => 'decimal:2',
        'active' => 'boolean',
    ];
    
    /**
     * Get all invoice items that use this billing code.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
    
    /**
     * Scope a query to only include active billing codes.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Scope a query to filter by code type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Format the price with currency symbol.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->default_price, 2);
    }
    
    /**
     * Get a displayable name consisting of code and description.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->code} - {$this->description}";
    }
}
