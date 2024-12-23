<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Illuminate\Support\Facades\Config;

final class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap for your file.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // modify this to your own needs
        SitemapGenerator::create(Config::string('app.url'))
            ->writeToFile(public_path('sitemap.xml'));
    }
}
