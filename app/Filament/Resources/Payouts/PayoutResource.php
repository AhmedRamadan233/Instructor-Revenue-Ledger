<?php

namespace App\Filament\Resources\Payouts;

use App\Actions\Payouts\MarkPayoutPaid;
use App\Actions\Payouts\RejectPayout;
use App\Enums\PayoutStatus;
use App\Filament\Resources\Payouts\Pages\ManagePayouts;
use App\Models\Manager;
use App\Models\Payout;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PayoutResource extends Resource
{
    protected static ?string $model = Payout::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Money';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('teacher.user.name')->label('Teacher')->searchable(),
                TextColumn::make('amount')->money(fn (Payout $record): string => $record->currency)->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (PayoutStatus $state): string => $state->name)
                    ->color(fn (PayoutStatus $state): string => match ($state) {
                        PayoutStatus::Pending => 'warning',
                        PayoutStatus::Processing => 'info',
                        PayoutStatus::Paid => 'success',
                        PayoutStatus::Rejected, PayoutStatus::Failed => 'danger',
                    }),
                TextColumn::make('provider_reference')->placeholder('—')->toggleable(),
                TextColumn::make('requested_at')->dateTime()->sortable(),
                TextColumn::make('processed_at')->dateTime()->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')->options(PayoutStatus::class),
            ])
            ->defaultSort('requested_at', 'desc')
            ->recordActions([
                Action::make('markPaid')
                    ->label('Mark paid')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Payout $record): bool => $record->status === PayoutStatus::Pending)
                    ->action(function (Payout $record): void {
                        $manager = Manager::query()->withoutGlobalScopes()->where('user_id', auth()->id())->firstOrFail();
                        app(MarkPayoutPaid::class)->handle($record, $manager);

                        Notification::make()->title('Payout marked as paid')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Payout $record): bool => $record->status === PayoutStatus::Pending)
                    ->action(function (Payout $record): void {
                        $manager = Manager::query()->withoutGlobalScopes()->where('user_id', auth()->id())->firstOrFail();
                        app(RejectPayout::class)->handle($record, $manager);

                        Notification::make()->title('Payout rejected')->success()->send();
                    }),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePayouts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->with(['teacher.user']);
    }
}
