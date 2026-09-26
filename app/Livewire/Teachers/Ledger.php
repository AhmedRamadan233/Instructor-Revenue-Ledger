<?php

namespace App\Livewire\Teachers;

use App\Enums\LedgerEntryType;
use App\Livewire\Concerns\InteractsWithTable;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Ledger')]
class Ledger extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    private TeacherLedgerEntryRepositoryInterface $ledgerEntries;

    #[Url(except: '')]
    public string $type = '';

    public function boot(TeacherLedgerEntryRepositoryInterface $ledgerEntries): void
    {
        $this->ledgerEntries = $ledgerEntries;
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'type');
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.teachers.ledger.index', [
            'entries' => $this->ledgerEntries->forTable(
                relations: ['revenuePeriod'],
                scopes: [
                    'search' => [$this->search],
                    'type' => [$this->type],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: [
                    'created_at',
                    'amount',
                    'type',
                    'currency',
                ],
            ),
            'types' => LedgerEntryType::cases(),
        ]);
    }
}
