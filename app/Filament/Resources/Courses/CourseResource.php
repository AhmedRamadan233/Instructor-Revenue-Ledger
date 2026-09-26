<?php

namespace App\Filament\Resources\Courses;

use App\Enums\CourseStatus;
use App\Filament\Resources\Courses\Pages\ManageCourses;
use App\Models\Course;
use App\Models\Teacher;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('teacher_id')
                    ->label('Teacher')
                    ->options(
                        fn (): array => Teacher::query()
                            ->withoutGlobalScopes()
                            ->with('user')
                            ->get()
                            ->mapWithKeys(fn (Teacher $teacher): array => [
                                $teacher->id => $teacher->user?->name ?? "Teacher #{$teacher->id}",
                            ])
                            ->all()
                    )
                    ->searchable()
                    ->required(),
                TextInput::make('title')->required()->maxLength(255),
                Textarea::make('description')->columnSpanFull(),
                Select::make('status')
                    ->options(CourseStatus::class)
                    ->required()
                    ->default(CourseStatus::Draft),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('teacher.user.name')->label('Teacher')->searchable(),
                TextColumn::make('status')->badge()->formatStateUsing(fn (CourseStatus $state): string => $state->name),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options(CourseStatus::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCourses::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->with(['teacher.user']);
    }
}
