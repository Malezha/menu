<?php

namespace Malezha\Menu;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\Menu as MenuContract;
use Malezha\Menu\Entity\Builder;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'menu');

        $this->publishes([
            __DIR__ . '/../views' => base_path('resources/views/vendor/menu'),
            __DIR__ . '/../config/menu.php' => config_path('menu.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBuilder();
        $this->registerSingleton();

        $this->mergeConfigFrom(__DIR__ . '/../config/menu.php', 'menu');
    }

    protected function registerSingleton()
    {
        $this->app->singleton(MenuContract::class, function (Container $app) {
            return new Menu($app);
        });
        $this->app->alias('menu', MenuContract::class);
    }

    protected function registerBuilder()
    {
        $this->app->bind(BuilderContract::class, Builder::class);
        $this->app->alias('menu.builder', BuilderContract::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['menu'];
    }
}
