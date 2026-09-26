<?php

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Introduction to Laravel',
            'Database Design Basics',
            'PHP Advanced Patterns',
            'API Development',
            'Testing with PHPUnit',
        ];

        Teacher::query()->withoutGlobalScopes()->get()->each(function (Teacher $teacher, int $index) use ($titles): void {
            Course::query()->create([
                'teacher_id' => $teacher->id,
                'title' => $titles[$index] ?? "Course {$teacher->id}",
                'description' => 'A published course available on the platform.',
                'status' => CourseStatus::Published,
            ]);
        });
    }
}
