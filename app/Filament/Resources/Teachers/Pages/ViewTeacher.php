<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTeacher extends ViewRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->mutateRecordDataUsing(function (array $data): array {
                    $data['name'] = $this->record->user?->name;
                    $data['email'] = $this->record->user?->email;
                    $data['password'] = null;

                    return $data;
                })
                ->using(function (array $data): void {
                    $payload = [
                        'name' => $data['name'],
                        'email' => $data['email'],
                    ];

                    if (! empty($data['password'])) {
                        $payload['password'] = $data['password'];
                    }

                    $this->record->user?->update($payload);
                }),
        ];
    }
}
