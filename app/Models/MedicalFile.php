<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MedicalFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'encounter_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'original_filename',
        'description',
        'file_category'
    ];

    /**
     * Get the patient associated with this file.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the encounter associated with this file.
     */
    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    /**
     * Get the user who uploaded the file.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Determine if the file is an image.
     */
    public function isImage(): bool
    {
        return $this->file_category === 'image' || 
               strpos($this->file_type, 'image/') === 0;
    }
    
    /**
     * Delete file from storage when model is deleted.
     */
    public static function boot()
    {
        parent::boot();
        
        static::deleting(function($file) {
            if (Storage::exists($file->file_path)) {
                Storage::delete($file->file_path);
            }
        });
    }
}
