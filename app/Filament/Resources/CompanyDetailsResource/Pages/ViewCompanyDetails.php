<?php

namespace App\Filament\Resources\CompanyDetailsResource\Pages;

use App\Filament\Resources\CompanyDetailsResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanyDetails extends ViewRecord
{
    protected static string $resource = CompanyDetailsResource::class;

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
                Section::make('Company Information')
                    ->schema([
                        TextEntry::make('company_name')->inlineLabel(),
                        TextEntry::make('trading_name')->inlineLabel(),
                        TextEntry::make('type_of_ownership')->inlineLabel(),
                        TextEntry::make('rc_number')->inlineLabel(),
                        TextEntry::make('tpin')->inlineLabel(),
                        TextEntry::make('date_registered')->date('d-M-Y')->inlineLabel(),
                        TextEntry::make('nature_of_business')->inlineLabel(),
                        TextEntry::make('country_of_incorporation')->inlineLabel(),
                    ])->columns(2),
                Section::make('Contact Information')
                    ->schema([
                        TextEntry::make('office_address')->inlineLabel(),
                        TextEntry::make('postal_address')->inlineLabel(),
                        TextEntry::make('office_telephone')->inlineLabel(),
                        TextEntry::make('customer_service_telephone')->inlineLabel(),
                        TextEntry::make('official_email')->inlineLabel(),
                        TextEntry::make('customer_service_email')->inlineLabel(),
                        TextEntry::make('official_website')->inlineLabel(),
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
        return "Company Details: " . $this->record->company_name;
    }
}
