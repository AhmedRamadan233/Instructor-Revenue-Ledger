<?php

namespace App\Livewire\Students;

use App\Actions\Consumption\RecordCourseWatch;
use App\Livewire\Requests\Students\WatchRequest;
use App\Models\Course;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use App\Repo\InterFace\CourseRepositoryInterface;
use App\Repo\InterFace\StudentRepositoryInterface;
use App\Support\AuthActor;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

#[Title('Watch Course')]
class CourseWatch extends __AbstractStudentComponent
{
    private CourseRepositoryInterface $courses;

    private StudentRepositoryInterface $students;

    private CourseConsumptionSessionRepositoryInterface $sessions;

    public Course $course;

    public int $seconds = 30;

    public int $totalWatchSeconds = 0;

    public int $currentSessionSeconds = 0;

    public int $sessionsCount = 0;

    public ?string $lastActivityAt = null;

    public bool $hasOpenSession = false;

    public function boot(
        CourseRepositoryInterface $courses,
        StudentRepositoryInterface $students,
        CourseConsumptionSessionRepositoryInterface $sessions,
    ): void {
        $this->courses = $courses;
        $this->students = $students;
        $this->sessions = $sessions;
    }

    public function mount(Course $course): void
    {
        $this->course = $this->courses->getById($course->id, relations: [
            'teacher' => fn ($query) => $query->withoutGlobalScopes()->with('user'),
        ]);

        $this->refreshWatchStats();
    }

    public function heartbeat(RecordCourseWatch $action): void
    {
        $this->validate(WatchRequest::rules());

        $studentId = AuthActor::studentId();
        abort_if($studentId === null, 403);

        $student = $this->students->getById($studentId, withoutGlobalScopes: true);
        $action->handle($student, $this->course, $this->seconds);

        $this->refreshWatchStats();
    }

    public function stopWatching(): void
    {
        $studentId = AuthActor::studentId();
        abort_if($studentId === null, 403);

        $this->sessions->query()
            ->where('student_id', $studentId)
            ->where('course_id', $this->course->id)
            ->whereNull('ended_at')
            ->update([
                'ended_at' => now(),
                'last_activity_at' => now(),
            ]);

        $this->refreshWatchStats();

        session()->flash('success', 'Watching session ended. Your watch time was saved.');
        $this->redirectRoute('student.courses', navigate: true);
    }

    protected function refreshWatchStats(): void
    {
        $studentId = AuthActor::studentId();

        if ($studentId === null) {
            return;
        }

        $sessions = $this->sessions->getWith(
            conditions: [
                'student_id' => $studentId,
                'course_id' => $this->course->id,
            ],
            orderBy: 'id',
            direction: 'desc',
        );

        $this->sessionsCount = $sessions->count();
        $this->totalWatchSeconds = (int) $sessions->sum('watch_seconds');

        $openSession = $sessions->firstWhere('ended_at', null);
        $this->hasOpenSession = $openSession !== null;
        $this->currentSessionSeconds = (int) ($openSession?->watch_seconds ?? 0);

        $last = $sessions->sortByDesc(fn ($session) => $session->last_activity_at)->first();
        $this->lastActivityAt = optional($last?->last_activity_at)->format('Y-m-d H:i') ?? null;
    }

    #[Computed]
    public function totalWatchLabel(): string
    {
        return $this->formatDuration($this->totalWatchSeconds);
    }

    #[Computed]
    public function currentSessionLabel(): string
    {
        return $this->formatDuration($this->currentSessionSeconds);
    }

    protected function formatDuration(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remain = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $remain);
        }

        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $remain);
        }

        return sprintf('%ds', $remain);
    }

    public function render()
    {
        return view('livewire.students.courses.watch', [
            'course' => $this->course,
            'sessions' => $this->sessions->take(
                limit: 10,
                conditions: [
                    'student_id' => AuthActor::studentId(),
                    'course_id' => $this->course->id,
                ],
                orderBy: 'id',
                direction: 'desc',
            ),
        ]);
    }
}
