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

        $this->console();
    }

    private function migrations()
    {
        //
    }

    private function console()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
