<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseConsumptionSession;
use App\Models\Student;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseConsumptionSession>
 */
class CourseConsumptionSessionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = now()->subHour();

        return [
            'student_id' => Student::factory(),
            'course_id' => Course::factory(),
            'subscription_id' => Subscription::factory(),
            'started_at' => $startedAt,
            'last_activity_at' => $startedAt->copy()->addMinutes(25),
            'ended_at' => null,
            'watch_seconds' => 1500,
        ];
    }
}
