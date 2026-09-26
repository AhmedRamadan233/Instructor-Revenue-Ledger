<?php

namespace App\Filament\Resources\Teachers\RelationManagers;

use App\Enums\LedgerEntryType;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LedgerEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'ledgerEntries';

    protected static ?string $title = 'Ledger';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->badge()->formatStateUsing(fn (LedgerEntryType $state): string => $state->name),
                TextColumn::make('amount')->money(fn ($record) => $record->currency)->sortable(),
                TextColumn::make('currency'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
