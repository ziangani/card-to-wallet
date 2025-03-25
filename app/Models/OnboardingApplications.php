<?php

namespace App\Models;

use App\Models\Traits\HasApprovals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OnboardingApplications extends Model
{
    use HasFactory, HasApprovals, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Application has been {$eventName}")
            ->useLogName('onboarding');
    }

    protected static function getModuleName(): string
    {
        return 'onboarding';
    }

    protected $appends = ['current_level_name'];

    protected $fillable = [
        'reference',
        'status',
        'comment',
        'company_id',
        'reviewed_by',
        'reviewed_at',
        'approval_level',
        'assigned_to',
        'previous_level'
    ];

    private static $approvalLevels = [
        0 => 'DATA_ENTRY',
        1 => 'COMPLIANCE'
    ];

    public static function getApprovalLevels(): array
    {
        return self::$approvalLevels;
    }

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function contact()
    {
        return $this->belongsTo(CompanyContact::class, 'company_id', 'company_id');
    }

    public function owner()
    {
        return $this->belongsTo(CompanyOwnership::class, 'company_id', 'company_id');
    }

    public function owners()
    {
        return $this->hasMany(CompanyOwnership::class, 'company_id', 'company_id');
    }

    public function bank()
    {
        return $this->belongsTo(CompanyBank::class, 'company_id', 'company_id');
    }

    public function website()
    {
        return $this->belongsTo(CompanyWebsite::class, 'company_id', 'company_id');
    }

    public function finance()
    {
        return $this->belongsTo(CompanyFinancial::class, 'company_id', 'company_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachments::class, 'reference', 'reference');
    }

    public function notifyReviewers($status = 'IN_REVIEW')
    {
        Log::info("Starting notifyReviewers for application {$this->reference} with status: {$status}");
        
        // Initialize level with a default value
        $level = 0;

        // Determine the appropriate level based on status
        if ($status === 'NEEDS_CLARITY') {
            // For needs clarity, notify the current level
            $level = $this->approval_level ?? 0;
        } else if ($status === 'IN_REVIEW') {
            if ($this->approval_level === null || $this->approval_level === 0) {
                // For new applications or when at level 0, notify level 0
                $level = 0;
            } else if ($this->needsClarity()) {
                // If coming from NEEDS_CLARITY, notify the same level again
                $level = $this->approval_level;
            } else {
                // Otherwise, notify the next level
                $level = $this->getNextLevel() ?? $this->approval_level ?? 0;
            }
        }

        // Ensure level is not null
        $level = $level ?? 0;
        
        Log::info("Determined level: {$level}");
        
        if (!isset(self::getApprovalLevels()[$level])) {
            Log::warning("Invalid level {$level}, returning early");
            return;
        }

        $nextLevelRole = self::getApprovalLevels()[$level];
        Log::info("Next level role: {$nextLevelRole}");
        
        $users = User::getUsersByRole($nextLevelRole);
        Log::info("Found " . $users->count() . " users for role {$nextLevelRole}");
        
        $template = match($status) {
            'NEEDS_CLARITY' => 'emails.approval-needs-clarity',
            default => 'emails.approval-next-level'
        };
        Log::info("Using template: {$template}");
        
        foreach ($users as $user) {
            Log::info("Creating email for user: {$user->email}");
            
            try {
                // Load relationships and prepare application data
                $this->load(['company']);
                $applicationData = $this->toArray();
                $applicationData['initiator'] = ['name' => 'Frontend Submission']; // Default for frontend submissions
                $applicationData['current_level_name'] = self::getApprovalLevels()[$level];

                $emailData = [
                    'subject' => "Merchant Application - {$this->reference}",
                    'from' => config('mail.from.address'),
                    'email' => $user->email,
                    'message' => "Application {$this->reference} status update",
                    'view' => $template,
                    'status' => 'PENDING',
                    'data' => [
                        'application' => $applicationData,
                        'user' => $user->toArray(),
                        'level' => $level,
                        'comments' => $status === 'NEEDS_CLARITY' && $this->currentApproval() ? 
                            $this->currentApproval()->comments : null
                    ]
                ];
                
                Log::info("Email data prepared", $emailData);
                
                $email = Emails::create($emailData);
                Log::info("Email created with ID: " . $email->id);
                
            } catch (\Exception $e) {
                Log::error("Failed to create email for user {$user->email}: " . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }
        
        Log::info("Completed notifyReviewers for application {$this->reference}");
    }
}
