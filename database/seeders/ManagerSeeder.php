<?php

namespace Database\Seeders;

use App\Models\Manager;
use App\Models\User;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Platform Manager',
            'email' => 'manager@example.com',
        ]);

        Manager::query()->create([
            'user_id' => $user->id,
        ]);
    }
}
