<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 80; $i++) {
            $user = User::factory()->create([
                'name' => "Student {$i}",
                'email' => "student{$i}@example.com",
            ]);

            Student::query()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
