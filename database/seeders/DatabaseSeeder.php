<?php

namespace Database\Seeders;

use App\Models\Shop;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Shop::factory(10)->create();

        Shop::factory()->create([
            'name' => 'Test Shop',
            'phone' => '09388104024',
        ]);
    }
}
