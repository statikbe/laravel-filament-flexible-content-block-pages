<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FilamentFlexibleContentBlockPagesDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HomePageSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
