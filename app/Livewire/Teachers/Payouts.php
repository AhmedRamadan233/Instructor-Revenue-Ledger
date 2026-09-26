<?php

namespace App\Livewire\Teachers;

use App\Actions\Payouts\RequestPayout;
use App\Enums\PayoutStatus;
use App\Livewire\Concerns\InteractsWithTable;
use App\Livewire\Requests\Teachers\RequestPayoutRequest;
use App\Models\Payout;
use App\Models\Teacher;
use App\Support\AuthActor;
use App\Support\TeacherBalance;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Payouts')]
class Payouts extends __AbstractTeacherComponent
{
    use InteractsWithTable;

    #[Url(except: '')]
    public string $status = '';

    public string $amount = '';

    public string $note = '';

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

        $teacher = Teacher::query()->withoutGlobalScopes()->findOrFail($teacherId);

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
            ? Teacher::query()->withoutGlobalScopes()->find($teacherId)
            : null;

        $query = Payout::query()
            ->search($this->search)
            ->status($this->status);

        $this->applySorting($query, [
            'created_at',
            'requested_at',
            'amount',
            'status',
        ], 'requested_at');

        return view('livewire.teachers.payouts.index', [
            'payouts' => $query->paginate(10),
            'statuses' => PayoutStatus::cases(),
            'availableBalance' => $teacher ? TeacherBalance::available($teacher) : 0.0,
        ]);
    }
}
