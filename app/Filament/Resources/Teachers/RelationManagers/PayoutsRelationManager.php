<?php

namespace App\Filament\Resources\Teachers\RelationManagers;

use App\Enums\PayoutStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayoutsRelationManager extends RelationManager
{
    protected static string $relationship = 'payouts';

    protected static ?string $title = 'Payout history';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')->money(fn ($record) => $record->currency)->sortable(),
                TextColumn::make('status')->badge()->formatStateUsing(fn (PayoutStatus $state): string => $state->name),
                TextColumn::make('provider_reference')->placeholder('—'),
                TextColumn::make('requested_at')->dateTime()->sortable(),
                TextColumn::make('processed_at')->dateTime()->placeholder('—'),
            ])
            ->defaultSort('requested_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
