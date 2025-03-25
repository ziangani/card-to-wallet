<?php

namespace App\Filament\Resources\Backend\ApiLogsResource\Pages;

use App\Filament\Resources\Backend\ApiLogsResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewApiLogs extends ViewRecord
{
    protected static string $resource = ApiLogsResource::class;

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
                Section::make('API Log Details')
                    ->schema([
                        TextEntry::make('created_at')->dateTime()->inlineLabel(),
                        TextEntry::make('request_time')->inlineLabel(),
                        TextEntry::make('response_time')->inlineLabel(),
                        TextEntry::make('request_status')->inlineLabel(),
                        TextEntry::make('source_ip')->inlineLabel(),
                        TextEntry::make('source_reference')->inlineLabel(),
                        TextEntry::make('request_type')->inlineLabel(),
                    ])->columns(2),
                Section::make('Request and Response')
                    ->schema([
                        TextEntry::make('request')->limit(1000)->inlineLabel(),
                        TextEntry::make('response')->limit(1000)->inlineLabel(),
                    ])->columns(1),
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('updated_at')->dateTime()->inlineLabel(),
                    ])->columns(2),
            ]);
    }

    public function getTitle(): string
    {
        return "API Log Details: " . $this->record->id;
    }
}
