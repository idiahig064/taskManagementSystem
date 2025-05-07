<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'P@ssw0rd!',
            'email' => 'idh160505@gmail.com',
            'email_verified_at' => '2025-04-09 04:55:02',
            'password' => '$2y$12$RkfyK9ZIEVDM2ZS.bj.pWOpLZjazyOFxPaD3K0MQVaqXZcd74UHum',
            'remember_token' => 'Nld1WpEciYu6qles2gnE5qgoWi4RUknmtHiKKwpl9rcl1j1Akr9l1oSyCdCI',
        ]);

        \App\Models\Category::factory(5)->create();
        \App\Models\Task::factory(20)->create();
    }
}
