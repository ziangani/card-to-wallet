<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'document_type',
        'document_number',
        'file_path',
        'status',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the document.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who reviewed the document.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if the document is pending review.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
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
     * Check if the document is rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the document is reviewed.
     *
     * @return bool
     */
    public function isReviewed()
    {
        return $this->reviewed_at !== null;
    }

    /**
     * Get the document type label.
     *
     * @return string
     */
    public function getDocumentTypeLabel()
    {
        $labels = [
            'certificate_of_incorporation' => 'Certificate of Incorporation',
            'tax_clearance' => 'Tax Clearance',
            'business_license' => 'Business License',
            'company_profile' => 'Company Profile',
            'director_id' => 'Director ID',
            'other' => 'Other Document',
        ];

        return $labels[$this->document_type] ?? ucfirst(str_replace('_', ' ', $this->document_type));
    }

    /**
     * Get the document status label.
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get the document file URL.
     *
     * @return string|null
     */
    public function getFileUrl()
    {
        if (!$this->file_path) {
            return null;
        }

        return asset('storage/' . $this->file_path);
    }

    /**
     * Approve the document.
     *
     * @param  int  $reviewerId
     * @param  string|null  $notes
     * @return bool
     */
    public function approve($reviewerId, $notes = null)
    {
        $this->status = 'approved';
        $this->reviewed_by = $reviewerId;
        $this->reviewed_at = now();
        
        if ($notes) {
            $this->review_notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Reject the document.
     *
     * @param  int  $reviewerId
     * @param  string|null  $notes
     * @return bool
     */
    public function reject($reviewerId, $notes = null)
    {
        $this->status = 'rejected';
        $this->reviewed_by = $reviewerId;
        $this->reviewed_at = now();
        
        if ($notes) {
            $this->review_notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Scope a query to only include pending documents.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved documents.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected documents.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include documents of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope a query to only include documents for a specific company.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include documents reviewed by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReviewedBy($query, $userId)
    {
        return $query->where('reviewed_by', $userId);
    }

    /**
     * Scope a query to only include documents reviewed within a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Support\Carbon  $startDate
     * @param  \Illuminate\Support\Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReviewedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('reviewed_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include documents that have been reviewed.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReviewed($query)
    {
        return $query->whereNotNull('reviewed_at');
    }

    /**
     * Scope a query to only include documents that have not been reviewed.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotReviewed($query)
    {
        return $query->whereNull('reviewed_at');
    }
}
