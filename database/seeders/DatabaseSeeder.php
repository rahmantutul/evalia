<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\PlanFeatureSeeder as SeedersPlanFeatureSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PlanFeatureSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call([
        //     SeedersPlanFeatureSeeder::class,
        // ]);
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
