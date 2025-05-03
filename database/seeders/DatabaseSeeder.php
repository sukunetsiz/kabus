<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        $this->call([
            UserSeeder::class,    // Must run first to create vendors
            ProductSeeder::class, // Depends on vendors existing
            ReviewSeeder::class,  // Depends on both users and products existing
        ]);

        $this->command->info('Database seeding completed successfully.');
    }
}
