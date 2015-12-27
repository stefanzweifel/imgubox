<?php namespace ImguBox\Providers;

use Illuminate\Support\ServiceProvider;
use ImguBox\Contracts\StorageProvider;
use ImguBox\Services\Dropbox\Client as DropboxClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(StorageProvider::class, DropboxClient::class);
    }
}
