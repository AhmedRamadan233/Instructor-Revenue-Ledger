<?php

namespace App\Livewire\Teachers;

use App\Enums\LedgerEntryType;
use App\Livewire\Concerns\InteractsWithTable;
use App\Models\TeacherLedgerEntry;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Ledger')]
class Ledger extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    #[Url(except: '')]
    public string $type = '';

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
        $query = TeacherLedgerEntry::query()
            ->with('revenuePeriod')
            ->search($this->search)
            ->type($this->type);

        $this->applySorting($query, [
            'created_at',
            'amount',
            'type',
            'currency',
        ]);

        return view('livewire.teachers.ledger.index', [
            'entries' => $query->paginate(10),
            'types' => LedgerEntryType::cases(),
        ]);
    }
}
