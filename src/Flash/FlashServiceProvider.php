<?php namespace Znck\Flash;

use Illuminate\Support\ServiceProvider;

class FlashServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('flash', function () {
            return $this->app->make('Znck\Flash\FlashNotifier');
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/flash.php', 'znck.flash');
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'flash');

        $this->publishes([
            __DIR__ . '/../views'            => base_path('resources/views/vendor/znck/flash'),
            __DIR__ . '/../config/flash.php' => config_path('znck/flash.php'),
        ]);
    }

    public function provides()
    {
        return ['flash'];
    }


}
