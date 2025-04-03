<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycDocument extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kyc_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'document_type',
        'document_number',
        'file_path',
        'status',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
        'expiry_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reviewer of the document.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if the document is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the document is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the document is rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the document type display name.
     *
     * @return string
     */
    public function getDocumentTypeDisplayAttribute()
    {
        $types = [
            'national_id' => 'National ID',
            'passport' => 'Passport',
            'drivers_license' => 'Driver\'s License',
            'proof_of_address' => 'Proof of Address',
            'selfie' => 'Selfie',
        ];

        return $types[$this->document_type] ?? $this->document_type;
    }

    /**
     * Get the document status display name.
     *
     * @return string
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}
