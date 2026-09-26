<?php

namespace App\Livewire\Students;

use App\Actions\Subscriptions\CancelSubscription;
use App\Enums\SubscriptionStatus;
use App\Livewire\Concerns\InteractsWithTable;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('My Subscriptions')]
class Subscriptions extends __AbstractStudentComponent
{
    use InteractsWithTable;

    private SubscriptionRepositoryInterface $subscriptions;

    #[Url(except: '')]
    public string $status = '';

    public function boot(SubscriptionRepositoryInterface $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status');
        $this->resetPage();
    }

    public function cancel(int $subscriptionId, CancelSubscription $action): void
    {
        $subscription = $this->subscriptions->getById($subscriptionId);
        $action->handle($subscription);

        session()->flash(
            'success',
            'تم إلغاء الاشتراك. الوصول اتقفل من دلوقتي، ومفيش استرجاع. مشاهدة الشهر الحالي لحد الإلغاء هتتحسب في التسوية.'
        );
    }

    public function render()
    {
        return view('livewire.students.subscriptions.index', [
            'subscriptions' => $this->subscriptions->forTable(
                relations: ['planOption.plan'],
                scopes: [
                    'search' => [$this->search],
                    'status' => [$this->status],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: [
                    'created_at',
                    'starts_at',
                    'ends_at',
                    'amount',
                    'status',
                ],
            ),
            'statuses' => SubscriptionStatus::cases(),
        ]);
    }
}
