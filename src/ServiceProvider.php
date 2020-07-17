<?php

namespace SoluzioneSoftware\LaravelMediaLibrary;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->migrations();

        $this->routes();

        $this->translations();

        $this->registerPublishing();
    }

    private function migrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/2020_01_01_000000_create_pending_media_table.php');
    }

    private function routes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    private function translations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'laravel-media-library');
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/laravel-media-library'),
        ], ['lang', 'laravel-media-library.lang']);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
