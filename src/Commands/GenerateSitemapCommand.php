<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Commands;

use Exception;
use Illuminate\Console\Command;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Services\Contracts\GeneratesSitemap;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flexible-content-block-pages:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! FilamentFlexibleContentBlockPages::config()->isSitemapEnabled()) {
            $this->info('Sitemap generation is disabled in configuration.');

            return Command::SUCCESS;
        }

        $this->info('Generating sitemap...');

        try {
            $generator = app(GeneratesSitemap::class);

            $generator->generate();

            $this->info('Sitemap generated successfully at: '.public_path('sitemap.xml'));
        } catch (Exception $e) {
            report($e);
            $this->error('Failed to generate sitemap: '.$e->getMessage());
            $this->error($e->getTraceAsString(), 1);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
