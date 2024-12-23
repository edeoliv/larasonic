<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use EchoLabs\Prism\Prism;
use Knuckles\Scribe\Scribe;
use Laravel\Sanctum\Sanctum;
use EchoLabs\Prism\Text\Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use EchoLabs\Prism\Facades\PrismServer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use EchoLabs\Prism\Enums\Provider as PrismProvider;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * This is optional, but it's recommended to register Telescope in local environment.
         * You are free to remove this if you don't want to use Telescope.
         * Remove the migration files if you don't want to use Telescope.
         *
         * @see https://laravel.com/docs/telescope
         * @see migrations/0001_01_01_000009_create_telescope_entries_table.php
         */
        if (App::isLocal()) {
            App::singleton(\Laravel\Telescope\TelescopeServiceProvider::class);
            App::singleton(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->configureUrl();
        $this->configureVite();
        $this->configurePrisms();
        $this->configureScribeDocumentation();
    }

    private function configureScribeDocumentation(): void
    {
        if (class_exists(Scribe::class)) {
            Scribe::beforeResponseCall(function (HttpFoundationRequest $request, ExtractedEndpointData $endpointData): void {
                $user = User::query()->first();
                if ($user) {
                    Sanctum::actingAs($user, ['*']);
                }
            });
        }
    }

    /**
     * Configure the application's commands.
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(App::isProduction());
    }

    /**
     * Configure the application's models.
     * This is optional, but it's recommended to enable strict mode and disable mass assignment.
     *
     * @see https://laravel.com/docs/eloquent#configuring-eloquent-strictness
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict();

        Model::unguard();
    }

    /**
     * Configure the application's URL.
     * This is optional, but it's recommended to force HTTPS in production.
     *
     * @see https://laravel.com/docs/octane#serving-your-application-via-https
     */
    private function configureUrl(): void
    {
        URL::forceHttps(App::isProduction());
    }

    /**
     * Configure the application's Vite loading strategy.
     * This is optional, but it's recommended to use aggressive prefetching so the UI feels snappy.
     */
    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    /**
     * Configure the application's Prisms.
     * This is optional to demonstrate how to register a Prism.
     * If you don't use AI, you can remove this method.
     *
     * @see https://prism.echolabs.dev/getting-started/introduction.html
     */
    private function configurePrisms(): void
    {
        // This is example of how to register a Prism.
        PrismServer::register(
            'Larasonic Small',
            fn (): Generator => Prism::text()->using(PrismProvider::Groq, 'llama3-8b-8192')
                ->withSystemPrompt(view('prompts.system')->render())
                ->withMaxTokens(50)
        );

        PrismServer::register(
            'Larasonic Medium',
            fn (): Generator => Prism::text()->using(PrismProvider::Groq, 'llama3-8b-8192')
                ->withSystemPrompt(view('prompts.system')->render())
                ->withMaxTokens(70)
        );

        PrismServer::register(
            'Larasonic Large',
            fn (): Generator => Prism::text()->using(PrismProvider::Groq, 'llama3-8b-8192')
                ->withSystemPrompt(view('prompts.system')->render())
                ->withMaxTokens(90)
        );
    }
}
