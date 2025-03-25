<?php

namespace App\Filament\Resources\Backend\TerminalsResource\Pages;

use App\Filament\Resources\Backend\TerminalsResource;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewTerminals extends ViewRecord
{
    protected static string $resource = TerminalsResource::class;

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
                Section::make('Terminal Information')
                    ->schema([
                        TextEntry::make('serial_number')->inlineLabel(),
                        TextEntry::make('type')->inlineLabel(),
                        TextEntry::make('model')->inlineLabel(),
                        TextEntry::make('manufacturer')->inlineLabel(),
                    ])->columns(2),
                Section::make('Status Information')
                    ->schema([
                        TextEntry::make('merchant.name')->inlineLabel(),
                        TextEntry::make('status')->inlineLabel(),
                        TextEntry::make('terminal_id')->inlineLabel(),
                        TextEntry::make('date_activated')->dateTime()->inlineLabel(),
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
        return "Terminal Details: " . $this->record->terminal_id;
    }
}
