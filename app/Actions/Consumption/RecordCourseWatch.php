<?php

namespace App\Actions\Consumption;

use App\Enums\SubscriptionStatus;
use App\Models\Course;
use App\Models\CourseConsumptionSession;
use App\Models\Student;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordCourseWatch
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptions,
        private CourseConsumptionSessionRepositoryInterface $sessions,
    ) {}

    public function handle(Student $student, Course $course, int $seconds = 30): CourseConsumptionSession
    {
        $seconds = max(1, min($seconds, 300));

        $subscription = $this->subscriptions->query(true)
            ->where('student_id', $student->id)
            ->where('status', SubscriptionStatus::Active)
            ->where(function ($query): void {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->latest('id')
            ->first();

        if ($subscription === null) {
            throw ValidationException::withMessages([
                'course' => 'An active subscription is required to watch courses.',
            ]);
        }

        return DB::transaction(function () use ($student, $course, $subscription, $seconds): CourseConsumptionSession {
            $session = $this->sessions->query(true)
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->where('subscription_id', $subscription->id)
                ->whereNull('ended_at')
                ->latest('id')
                ->first();

            if ($session !== null && ! $session->started_at->isSameMonth(now())) {
                $this->sessions->update($session->id, [
                    'ended_at' => $session->last_activity_at ?? now(),
                ], withoutGlobalScopes: true);
                $session = null;
            }

            if ($session === null) {
                return $this->sessions->create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'subscription_id' => $subscription->id,
                    'started_at' => now(),
                    'last_activity_at' => now(),
                    'ended_at' => null,
                    'watch_seconds' => $seconds,
                ]);
            }

            $this->sessions->update($session->id, [
                'watch_seconds' => $session->watch_seconds + $seconds,
                'last_activity_at' => now(),
            ], withoutGlobalScopes: true);

            return $session->refresh();
        });
    }
}
