<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalAction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'approval_request_id',
        'approver_id',
        'action',
        'comments',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the approval request that owns the approval action.
     */
    public function approvalRequest()
    {
        return $this->belongsTo(ApprovalRequest::class);
    }

    /**
     * Get the user who performed the approval action.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Check if the approval action is an approval.
     *
     * @return bool
     */
    public function isApproval()
    {
        return $this->action === 'approved';
    }

    /**
     * Check if the approval action is a rejection.
     *
     * @return bool
     */
    public function isRejection()
    {
        return $this->action === 'rejected';
    }

    /**
     * Get the action label.
     *
     * @return string
     */
    public function getActionLabel()
    {
        $labels = [
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Scope a query to only include approval actions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApprovals($query)
    {
        return $query->where('action', 'approved');
    }

    /**
     * Scope a query to only include rejection actions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejections($query)
    {
        return $query->where('action', 'rejected');
    }

    /**
     * Scope a query to only include approval actions performed by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByApprover($query, $userId)
    {
        return $query->where('approver_id', $userId);
    }

    /**
     * Scope a query to only include approval actions created within a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Support\Carbon  $startDate
     * @param  \Illuminate\Support\Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
