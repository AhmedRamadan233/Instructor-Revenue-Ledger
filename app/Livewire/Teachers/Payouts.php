<?php

namespace App\Livewire\Teachers;

use App\Actions\Payouts\RequestPayout;
use App\Enums\PayoutStatus;
use App\Livewire\Concerns\InteractsWithTable;
use App\Livewire\Requests\Teachers\RequestPayoutRequest;
use App\Repo\InterFace\PayoutRepositoryInterface;
use App\Repo\InterFace\TeacherRepositoryInterface;
use App\Support\AuthActor;
use App\Support\TeacherBalance;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Payouts')]
class Payouts extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    private PayoutRepositoryInterface $payouts;

    private TeacherRepositoryInterface $teachers;

    #[Url(except: '')]
    public string $status = '';

    public string $amount = '';

    public string $note = '';

    public function boot(
        PayoutRepositoryInterface $payouts,
        TeacherRepositoryInterface $teachers,
    ): void {
        $this->payouts = $payouts;
        $this->teachers = $teachers;
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

    public function requestPayout(RequestPayout $action): void
    {
        $this->validate(RequestPayoutRequest::rules());

        $teacherId = AuthActor::teacherId();

        if ($teacherId === null) {
            session()->flash('error', 'Teacher profile not found.');

            return;
        }

        $teacher = $this->teachers->getById($teacherId, withoutGlobalScopes: true);

        $action->handle(
            $teacher,
            (float) $this->amount,
            'EGP',
            $this->note !== '' ? $this->note : null,
        );

        $this->reset('amount', 'note');

        session()->flash('success', 'تم إرسال طلب الصرف. هيتعالج من الإدارة.');
    }

    public function render()
    {
        $teacherId = AuthActor::teacherId();
        $teacher = $teacherId
            ? $this->teachers->find($teacherId, withoutGlobalScopes: true)
            : null;

        return view('livewire.teachers.payouts.index', [
            'payouts' => $this->payouts->forTable(
                scopes: [
                    'search' => [$this->search],
                    'status' => [$this->status],
                ],
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
                allowedSorts: [
                    'created_at',
                    'requested_at',
                    'amount',
                    'status',
                ],
                defaultSort: 'requested_at',
            ),
            'statuses' => PayoutStatus::cases(),
            'availableBalance' => $teacher ? TeacherBalance::available($teacher) : 0.0,
        ]);
    }
}
