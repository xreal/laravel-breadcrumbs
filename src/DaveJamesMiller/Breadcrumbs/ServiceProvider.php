<?php
namespace DaveJamesMiller\Breadcrumbs;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('breadcrumbs');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->prepareResources();
        
        $this->app['breadcrumbs'] = $this->app->share(function($app)
        {
            $breadcrumbs = new Manager($app['view'], $app['router']);
            $viewPath = __DIR__ . '/../../views/';
            $this->loadViewsFrom($viewPath, 'breadcrumbs');
            $breadcrumbs->setView($app['config']['breadcrumbs.view']);

            return $breadcrumbs;
        });
    }
    
    /**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        $configPath = __DIR__ . '/../../config/config.php';
        $this->mergeConfigFrom($configPath, 'breadcrumbs');
        $this->publishes([
            $configPath => config_path('breadcrumbs.php'),
        ]);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Register the package so the default view can be loaded
        //$this->package('davejamesmiller/laravel-breadcrumbs');

        // Load the app breadcrumbs if they're in app/Http/breadcrumbs.php (Laravel 5.x)
        if (file_exists($file = $this->app['path'].'/Http/breadcrumbs.php'))
        {
            require $file;
        }
        // Load the app breadcrumbs if they're in app/breadcrumbs.php (Laravel 4.x)
        elseif (file_exists($file = $this->app['path'].'/breadcrumbs.php'))
        {
            require $file;
        }
    }
}
