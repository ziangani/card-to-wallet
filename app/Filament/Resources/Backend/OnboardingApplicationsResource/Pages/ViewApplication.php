<?php

namespace App\Filament\Resources\Backend\OnboardingApplicationsResource\Pages;

use App\Common\GeneralStatus;
use App\Common\Helpers;
use App\Filament\Resources\Backend\OnboardingApplicationsResource;
use App\Models\OnboardingApplications;
use App\Models\SmsNotifications;
use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewApplication extends ViewRecord
{
    protected static string $resource = OnboardingApplicationsResource::class;

    public function getTitle(): string|Htmlable
    {
        $record = $this->getRecord();

        return $record->reference;
    }

    protected function getActions(): array
    {
        return [
            Action::make('sendForApproval')
                ->label('Send for Approval')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->form([
                    TextInput::make('comments')
                        ->label('Comments')
                        ->required()
                ])
                ->visible(fn (OnboardingApplications $record): bool => 
                    $record->approval_level === 0 && $record->status !== 'APPROVED'
                )
                ->action(function (array $data, OnboardingApplications $record): void {
                    // Create approval record
                    Approval::create([
                        'reference' => $record->reference,
                        'module' => 'onboarding',
                        'level' => $record->approval_level,
                        'level_name' => $record->current_level_name,
                        'status' => 'APPROVED',
                        'initiated_by' => Auth::id(),
                        'actioned_by' => Auth::id(),
                        'comments' => $data['comments'],
                    ]);

                    // Move to next level
                    $record->update([
                        'approval_level' => $record->getNextLevel(),
                        'status' => 'IN_REVIEW'
                    ]);

                    // Notify next level reviewers
                    $record->notifyReviewers();

                    Notification::make()
                        ->title('Application sent for approval')
                        ->success()
                        ->send();
                }),

            Action::make('reject')
                ->label('Reject Application')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->form([
                    TextInput::make('comments')
                        ->label('Rejection Reason')
                        ->required()
                ])
                ->visible(fn (OnboardingApplications $record): bool => 
                    $record->approval_level > 0 && $record->status === 'IN_REVIEW'
                )
                ->action(function (array $data, OnboardingApplications $record): void {
                    // Create approval record
                    Approval::create([
                        'reference' => $record->reference,
                        'module' => 'onboarding',
                        'level' => $record->approval_level,
                        'level_name' => $record->current_level_name,
                        'status' => 'REJECTED',
                        'initiated_by' => Auth::id(),
                        'actioned_by' => Auth::id(),
                        'comments' => $data['comments'],
                    ]);

                    $record->update(['status' => 'REJECTED']);

                    // Send SMS notification
                    $sms_text = "Hi {$record->contact->primary_full_name},\n"
                        . "Your application was rejected for the following reason: {$data['comments']}\n"
                        . "Tracking No.: {$record->reference}\n"
                        . "Thank you.\n"
                        . Helpers::getAppName();

                    $sms = new SmsNotifications();
                    $sms->message = $sms_text;
                    $sms->mobile = $record->contact->primary_phone_number;
                    $sms->status = GeneralStatus::STATUS_PENDING;
                    $sms->sender = Helpers::getSenderId();
                    $sms->save();

                    Notification::make()
                        ->title('Application rejected')
                        ->success()
                        ->send();
                }),

            Action::make('approve')
                ->label('Approve Application')
                ->icon('heroicon-o-check')
                ->color('success')
                ->form([
                    TextInput::make('comments')
                        ->label('Comments')
                        ->required()
                ])
                ->visible(fn (OnboardingApplications $record): bool => 
                    $record->approval_level > 0 && $record->status === 'IN_REVIEW'
                )
                ->action(function (array $data, OnboardingApplications $record): void {
                    // Create approval record
                    Approval::create([
                        'reference' => $record->reference,
                        'module' => 'onboarding',
                        'level' => $record->approval_level,
                        'level_name' => $record->current_level_name,
                        'status' => 'APPROVED',
                        'initiated_by' => Auth::id(),
                        'actioned_by' => Auth::id(),
                        'comments' => $data['comments'],
                    ]);

                    // Check if at final level using array size
                    $isFinalLevel = $record->approval_level === count($record::getApprovalLevels()) - 1;

                    if ($isFinalLevel) {
                        $record->update(['status' => 'APPROVED']);

                        // Send SMS notification
                        $sms_text = "Hi {$record->contact->primary_full_name},\n"
                            . "Your application was approved.\n"
                            . "Tracking No.: {$record->reference}\n"
                            . "Thank you.\n"
                            . Helpers::getAppName();

                        $sms = new SmsNotifications();
                        $sms->message = $sms_text;
                        $sms->mobile = $record->contact->primary_phone_number;
                        $sms->status = GeneralStatus::STATUS_PENDING;
                        $sms->sender = Helpers::getSenderId();
                        $sms->save();
                    } else {
                        $record->update([
                            'approval_level' => $record->getNextLevel(),
                            'status' => 'IN_REVIEW'
                        ]);

                        // Notify next level reviewers
                        $record->notifyReviewers();
                    }

                    Notification::make()
                        ->title('Application approved')
                        ->success()
                        ->send();
                }),

            Action::make('sendBackForReview')
                ->label('Send Back for Review')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->form([
                    TextInput::make('comments')
                        ->label('Comments')
                        ->required()
                ])
                ->visible(fn (OnboardingApplications $record): bool => 
                    $record->approval_level > 0 && $record->status === 'IN_REVIEW'
                )
                ->action(function (array $data, OnboardingApplications $record): void {
                    // Create approval record
                    Approval::create([
                        'reference' => $record->reference,
                        'module' => 'onboarding',
                        'level' => $record->approval_level,
                        'level_name' => $record->current_level_name,
                        'status' => 'NEEDS_CLARITY',
                        'initiated_by' => Auth::id(),
                        'actioned_by' => Auth::id(),
                        'comments' => $data['comments'],
                    ]);

                    // Send back to level 0
                    $record->update([
                        'approval_level' => 0,
                        'status' => 'NEEDS_CLARITY'
                    ]);

                    // Notify data entry users
                    $record->notifyReviewers('NEEDS_CLARITY');

                    // Send SMS notification
                    $sms_text = "Hi {$record->contact->primary_full_name},\n"
                        . "Your application needs review: {$data['comments']}\n"
                        . "Tracking No.: {$record->reference}\n"
                        . "Thank you.\n"
                        . Helpers::getAppName();

                    $sms = new SmsNotifications();
                    $sms->message = $sms_text;
                    $sms->mobile = $record->contact->primary_phone_number;
                    $sms->status = GeneralStatus::STATUS_PENDING;
                    $sms->sender = Helpers::getSenderId();
                    $sms->save();

                    Notification::make()
                        ->title('Application sent back for review')
                        ->success()
                        ->send();
                }),
        ];
    }
}
