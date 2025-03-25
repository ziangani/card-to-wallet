<?php

namespace App\Filament\Resources\ChargebackResource\Pages;

use App\Filament\Resources\ChargebackResource;
use App\Models\Chargeback;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateChargeback extends CreateRecord
{
    protected static string $resource = ChargebackResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $transaction = Chargeback::findTransactionByApprovalCode($data['approval_code']);
        
        if (!$transaction) {
            Notification::make()
                ->danger()
                ->title('Transaction not found')
                ->body('No successful payment transaction found with this approval code.')
                ->send();
                
            $this->halt();
        }

        $chargeback = Chargeback::createFromTransaction(
            transaction: $transaction,
            rrn: $data['rrn'] ?? null,
            reason_code: $data['reason_code'] ?? null,
            chargeback_date: $data['chargeback_date']
        );

        return $chargeback->toArray();
    }
}
