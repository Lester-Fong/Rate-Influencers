<?php

namespace Database\Seeders;

use App\Models\Administrator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! Administrator::where('email', 'arthur.white@example.net')->exists()) {
            Administrator::create([
                'email' => 'arthur.white@example.net',
                'password' => Hash::make('Test_123'),
            ]);
        }
    }
}
