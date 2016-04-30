<?php

namespace Malezha\Menu;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Menu as MenuContract;
use Malezha\Menu\Contracts\Builder;

/**
 * Class Menu
 * @package Malezha\Menu
 */
class Menu implements MenuContract
{
    /**
     * @var array
     */
    protected $menus = [];

    /**
     * @var Container
     */
    protected $container;

    /**
     * Menu constructor.
     * 
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Make new global menu
     * 
     * @param string $name
     * @param \Closure $callback
     * @param string $type
     * @param array $attributes
     * @param array $activeAttributes
     * @return Builder
     */
    public function make($name, \Closure $callback, $type = Builder::UL, $attributes = [], $activeAttributes = [])
    {
        if(!is_callable($callback)) {
            throw new \InvalidArgumentException('Argument must be callable');
        }
        
        $menu = $this->container->make(Builder::class, [$this->container, $name, $type, $attributes, $activeAttributes]);
        call_user_func($callback, $menu);
        $this->menus[$name] = $menu;

        return $menu;
    }

    /**
     * Get global menu
     * 
     * @param string $name
     * @return Builder
     * @throws \RuntimeException
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->menus) && ($menu = $this->menus[$name]) instanceof Builder) {
            return $menu;
        }

        throw new \RuntimeException('Menu not found');
    }

    /**
     * Render global menu to html
     * 
     * @param string $name
     * @param null|string $view
     */
    public function render($name, $view = null)
    {
        $this->get($name)->render($view);
    }
}