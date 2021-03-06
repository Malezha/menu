<?php

namespace Malezha\Menu;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\ComparativeUrl as ComparativeUrlContract;
use Malezha\Menu\Contracts\FromArrayBuilder as FromArrayBuilderContract;
use Malezha\Menu\Contracts\Menu as MenuContract;
use Malezha\Menu\Contracts\MenuRender;
use Malezha\Menu\Support\Attributes;
use Malezha\Menu\Support\ComparativeUrl;
use Malezha\Menu\Support\FromArrayBuilder;

/**
 * Class MenuServiceProvider
 * @package Malezha\Menu
 */
class MenuServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

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
        $this->mergeConfigFrom(__DIR__ . '/../config/menu.php', 'menu');

        $this->registerFromArrayBuilder();
        $this->registerComparativeUrl();
        $this->registerRenderSystem();
        $this->registerAttributes();
        $this->registerBuilder();
        $this->registerSingleton();
    }

    protected function registerSingleton()
    {
        $this->app->singleton('menu.instance', function(Container $app) {
            return new Menu($app);
        });
        $this->app->alias('menu.instance', MenuContract::class);
    }

    protected function registerBuilder()
    {
        $this->app->bind('menu.builder', Builder::class);
        $this->app->alias('menu.builder', BuilderContract::class);
    }

    protected function registerAttributes()
    {
        $this->app->bind('menu.attributes', Attributes::class);
        $this->app->alias('menu.attributes', AttributesContract::class);
    }

    protected function registerRenderSystem()
    {
        $this->app->bind('menu.render', function (Container $app) {
            $config = $app->make(Repository::class)->get('menu');
            $key = $config['default'];
            $available = $config['renders'];

            if (array_key_exists($key, $available)) {
                return new $available[$key]($app);
            }

            throw new \Exception('Can use template system: ' . $config['default']);
        });
        $this->app->alias('menu.render', MenuRender::class);
    }

    protected function registerComparativeUrl()
    {
        $this->app->singleton('menu.compare-url', function (Container $app) {
            return $app->make(ComparativeUrl::class, [
                'skippedPaths' => $app->make(Repository::class)->get('menu.skippedPaths'),
            ]);
        });
        $this->app->alias('menu.compare-url', ComparativeUrlContract::class);
    }

    protected function registerFromArrayBuilder()
    {
        $this->app->singleton('menu.from-array-builder', function (Container $app) {
            return $app->make(FromArrayBuilder::class);
        });
        $this->app->alias('menu.from-array-builder', FromArrayBuilderContract::class);

        FromArrayBuilder::setInstance($this->app->make(FromArrayBuilderContract::class));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
