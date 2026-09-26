<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = User::factory()->create([
                'name' => "Teacher {$i}",
                'email' => "teacher{$i}@example.com",
            ]);

            Teacher::query()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
