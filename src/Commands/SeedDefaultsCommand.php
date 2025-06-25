<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Commands;

use Statikbe\FilamentFlexibleContentBlockPages\Database\Seeders\HomePageSeeder;
use Statikbe\FilamentFlexibleContentBlockPages\Database\Seeders\SettingsSeeder;

class SeedDefaultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flexible-content-block-pages:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed default home page and settings';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Seeding default home page and settings...');

        $homeSeeder = new HomePageSeeder();
        $homeSeeder->run();

        $settingsSeeder = new SettingsSeeder();
        $settingsSeeder->run();

        $this->info('Default home page and settings seeded successfully!');

    }
}
