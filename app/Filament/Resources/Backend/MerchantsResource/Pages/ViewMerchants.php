<?php

namespace App\Filament\Resources\Backend\MerchantsResource\Pages;

use App\Filament\Resources\Backend\MerchantsResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewMerchants extends ViewRecord
{
    protected static string $resource = MerchantsResource::class;

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
                Section::make('Merchant Information')
                    ->schema([
                        TextEntry::make('company.company_name')->inlineLabel(),
                        TextEntry::make('name')->inlineLabel(),
                        TextEntry::make('code')->inlineLabel(),
                        TextEntry::make('description')->inlineLabel(),
                        TextEntry::make('status')->inlineLabel(),
                    ])->columns(2),
                Section::make('Misc Details')
                    ->schema([
                        ImageEntry::make('logo')->inlineLabel(),
                    ])->columns(1),
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')->dateTime()->inlineLabel(),
                        TextEntry::make('updated_at')->dateTime()->inlineLabel(),
                    ])->columns(2),
            ]);
    }

    public function getTitle(): string
    {
        return "Merchant Details: " . $this->record->name;
    }
}
