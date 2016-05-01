<?php

namespace Malezha\Menu;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\Group as GroupContract;
use Malezha\Menu\Contracts\Item as ItemContract;
use Malezha\Menu\Contracts\Link as LinkContract;
use Malezha\Menu\Contracts\Menu as MenuContract;
use Malezha\Menu\Contracts\MenuRender;
use Malezha\Menu\Entity\Attributes;
use Malezha\Menu\Entity\Builder;
use Malezha\Menu\Entity\Group;
use Malezha\Menu\Entity\Item;
use Malezha\Menu\Entity\Link;

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
        
        $this->registerRenderSystem();
        $this->registerAttributes();
        $this->registerLink();
        $this->registerBuilder();
        $this->registerItem();
        $this->registerGroup();
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

    protected function registerGroup()
    {
        $this->app->bind('menu.group', Group::class);
        $this->app->alias('menu.group', GroupContract::class);
    }

    protected function registerItem()
    {
        $this->app->bind('menu.item', Item::class);
        $this->app->alias('menu.item', ItemContract::class);
    }

    protected function registerLink()
    {
        $this->app->bind('menu.link', Link::class);
        $this->app->alias('menu.link', LinkContract::class);
    }

    protected function registerAttributes()
    {
        $this->app->bind('menu.attributes', Attributes::class);
        $this->app->alias('menu.attributes', AttributesContract::class);
    }

    protected function registerRenderSystem()
    {
        $this->app->bind('menu.render', function (Container $app) {
            $config = $app['config']->get('menu');
            $key = $config['template-system'];
            $available = $config['available-template-systems'];

            if (array_key_exists($key, $available)) {
                return new $available[$key]($app);
            }

            throw new \Exception('Can use template system: ' . $config['template-system']);
        });
        $this->app->alias('menu.render', MenuRender::class);
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
