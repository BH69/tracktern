<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
        'uploaded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    /**
     * Allowed file types for uploads.
     */
    const ALLOWED_FILE_TYPES = [
        'application/pdf',           // PDF
        'image/png',                // PNG
        'image/jpeg',               // JPG/JPEG
        'image/jpg',                // JPG
        'application/msword',       // DOC
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
        'application/vnd.ms-excel', // XLS
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // XLSX
        'text/plain',               // TXT
        'application/zip',          // ZIP
        'application/x-rar-compressed', // RAR
    ];

    /**
     * Maximum file size in bytes (10MB).
     */
    const MAX_FILE_SIZE = 10485760; // 10 MB

    /**
     * Get the user who uploaded the document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file extension from the file name.
     */
    public function getFileExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Check if the document is an image.
     */
    public function isImage(): bool
    {
        return in_array($this->file_type, [
            'image/png',
            'image/jpeg',
            'image/jpg',
            'image/gif',
            'image/webp'
        ]);
    }

    /**
     * Check if the document is a PDF.
     */
    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }

    /**
     * Check if the document is a Word document.
     */
    public function isWordDocument(): bool
    {
        return in_array($this->file_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * Get the download URL for the document.
     */
    public function getDownloadUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Check if the file exists in storage.
     */
    public function fileExists(): bool
    {
        return Storage::exists($this->file_path);
    }

    /**
     * Delete the file from storage.
     */
    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::delete($this->file_path);
        }
        return true;
    }

    /**
     * Get allowed file types for validation.
     */
    public static function getAllowedFileTypes(): array
    {
        return self::ALLOWED_FILE_TYPES;
    }

    /**
     * Get allowed file extensions.
     */
    public static function getAllowedExtensions(): array
    {
        return [
            'pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx', 
            'xls', 'xlsx', 'txt', 'zip', 'rar'
        ];
    }

    /**
     * Get the maximum file size in bytes.
     */
    public static function getMaxFileSize(): int
    {
        return self::MAX_FILE_SIZE;
    }

    /**
     * Get the maximum file size formatted.
     */
    public static function getMaxFileSizeFormatted(): string
    {
        return number_format(self::MAX_FILE_SIZE / 1048576, 0) . ' MB';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically delete file when model is deleted
        static::deleting(function ($document) {
            $document->deleteFile();
        });
    }
}
