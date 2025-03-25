<?php

namespace App\Filament\Resources\Backend\PaymentProvidersResource\Pages;

use App\Filament\Resources\Backend\PaymentProvidersResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentProviders extends ViewRecord
{
    protected static string $resource = PaymentProvidersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Provider Details')
                    ->schema([
                        TextEntry::make('name')->inlineLabel(),
                        TextEntry::make('code')->inlineLabel(),
                        TextEntry::make('status')->inlineLabel(),
                        TextEntry::make('environment')->inlineLabel(),
                    ])->columns(2),
                Section::make('API Details')
                    ->schema([
                        TextEntry::make('api_key_id')->inlineLabel(),
                        TextEntry::make('api_key_secret')->inlineLabel(),
                        TextEntry::make('api_url')->inlineLabel(),
                        TextEntry::make('api_token')->inlineLabel(),
                    ])->columns(2),
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')->dateTime()->inlineLabel(),
                        TextEntry::make('updated_at')->dateTime()->inlineLabel(),
                    ])->columns(2),
            ]);
    }

    public function getTitle(): string
    {
        return "Payment Provider Details: " . $this->record->name;
    }
}
