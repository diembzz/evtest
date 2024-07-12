<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            if (!User::exists()) {
                echo '  Seed users ...' . "\n\n";
                User::factory()->create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
            }

            if (!Venue::exists()) {
                echo '  Seed venues ...' . "\n\n";
                Venue::factory(20)->create();
            }

            if (!Event::exists()) {
                echo '  Seed events ...' . "\n\n";
                Event::factory(50)->create();
            }
        });
    }
}
