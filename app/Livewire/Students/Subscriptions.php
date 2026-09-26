<?php

namespace App\Livewire\Students;

use App\Enums\SubscriptionStatus;
use App\Livewire\Concerns\InteractsWithTable;
use App\Models\Subscription;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('My Subscriptions')]
class Subscriptions extends __AbstractStudentComponent
{
    use InteractsWithTable;

    #[Url(except: '')]
    public string $status = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status');
        $this->resetPage();
    }

    public function render()
    {
        $query = Subscription::query()
            ->with(['planOption.plan'])
            ->search($this->search)
            ->status($this->status);

        $this->applySorting($query, [
            'created_at',
            'starts_at',
            'ends_at',
            'amount',
            'status',
        ]);

        return view('livewire.students.subscriptions', [
            'subscriptions' => $query->paginate(10),
            'statuses' => SubscriptionStatus::cases(),
        ]);
    }
}
