<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'filename',
        'path',
        'filetype',
        'file_size',
        'visibility',
        'is_active',
        'user_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Get the admin user who uploaded the document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' Go';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' Mo';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' Ko';
        } else {
            return $bytes . ' octets';
        }
    }

    /**
     * Scope to filter documents by visibility based on user role.
     */
    public function scopeVisibleTo($query, $user)
    {
        if ($user->role == 0) {
            // Admin sees all documents
            return $query;
        } elseif ($user->role == 1) {
            // Professor sees 'all' and 'teachers'
            return $query->whereIn('visibility', ['all', 'teachers']);
        } else {
            // Student sees 'all' and 'students'
            return $query->whereIn('visibility', ['all', 'students']);
        }
    }

    /**
     * Scope to filter only active documents.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
