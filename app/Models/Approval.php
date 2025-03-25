<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'module',
        'level',
        'level_name',
        'status',
        'initiated_by',
        'actioned_by',
        'comments'
    ];

    public static $states = [
        'IN_REVIEW',
        'APPROVED',
        'REJECTED',
        'NEEDS_CLARITY'
    ];

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by')->withDefault([
            'name' => 'Frontend Submission'
        ]);
    }

    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isRejected(): bool
    {
        return $this->status === 'REJECTED';
    }

    public function needsClarity(): bool
    {
        return $this->status === 'NEEDS_CLARITY';
    }

    public function isInReview(): bool
    {
        return $this->status === 'IN_REVIEW';
    }
}
