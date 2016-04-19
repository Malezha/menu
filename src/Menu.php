<?php

namespace Malezha\Menu;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use Malezha\Menu\Contracts\Menu as MenuContract;
use Malezha\Menu\Contracts\Builder;

class Menu implements MenuContract
{
    /**
     * @var Collection
     */
    protected $menus;

    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->menus = new Collection();
    }

    /**
     * @param string $name
     * @param callable $callback
     * @param string $type
     * @param array $attributes
     * @param array $activeAttributes
     * @return Builder
     */
    public function make($name, $callback, $type = Builder::UL, $attributes = [], $activeAttributes = [])
    {
        if(!is_callable($callback)) {
            throw new \InvalidArgumentException('Argument must be callable');
        }
        
        $menu = $this->container->make(Builder::class, [$this->container, $name, $type, $attributes, $activeAttributes]);
        
        call_user_func($callback, $menu);
        $this->menus->put($name, $menu);

        return $menu;
    }

    /**
     * @param string $name
     * @return Builder
     */
    public function get($name)
    {
        if (!($menu = $this->menus->get($name)) instanceof Builder) {
            throw new \RuntimeException('Menu by not found');
        }

        return $menu;
    }

    /**
     * @param string $name
     * @param null|string $view
     */
    public function render($name, $view = null)
    {
        $this->get($name)->render($view);
    }
}