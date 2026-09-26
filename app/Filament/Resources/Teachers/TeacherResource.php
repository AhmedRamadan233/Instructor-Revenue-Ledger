<?php

namespace App\Filament\Resources\Teachers;

use App\Filament\Resources\Teachers\Pages\ManageTeachers;
use App\Filament\Resources\Teachers\Pages\ViewTeacher;
use App\Filament\Resources\Teachers\RelationManagers\LedgerEntriesRelationManager;
use App\Filament\Resources\Teachers\RelationManagers\PayoutsRelationManager;
use App\Models\Teacher;
use App\Models\User;
use App\Support\TeacherBalance;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'People';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')->label('Name'),
                TextEntry::make('user.email')->label('Email'),
                TextEntry::make('available_balance')
                    ->label('Available balance')
                    ->state(fn (Teacher $record): string => number_format(TeacherBalance::available($record), 2).' EGP'),
                TextEntry::make('courses_count')
                    ->label('Courses')
                    ->state(fn (Teacher $record): int => $record->courses()->withoutGlobalScopes()->count()),
                TextEntry::make('created_at')->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')->label('Name')->searchable()->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('courses_count')->counts('courses')->label('Courses')->sortable(),
                TextColumn::make('available_balance')
                    ->label('Available')
                    ->state(fn (Teacher $record): string => number_format(TeacherBalance::available($record), 2).' EGP'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Teacher $record): array {
                        $data['name'] = $record->user?->name;
                        $data['email'] = $record->user?->email;
                        $data['password'] = null;

                        return $data;
                    })
                    ->using(function (Teacher $record, array $data): Teacher {
                        $payload = [
                            'name' => $data['name'],
                            'email' => $data['email'],
                        ];

                        if (! empty($data['password'])) {
                            $payload['password'] = $data['password'];
                        }

                        $record->user?->update($payload);

                        return $record;
                    }),
                DeleteAction::make()
                    ->using(function (Teacher $record): void {
                        $userId = $record->user_id;
                        $record->delete();
                        if ($userId) {
                            User::query()->whereKey($userId)->delete();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LedgerEntriesRelationManager::class,
            PayoutsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTeachers::route('/'),
            'view' => ViewTeacher::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->with(['user']);
    }
}
