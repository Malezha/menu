<?php

namespace Malezha\Menu;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Attributes;
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
    protected $menuList = [];

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
        $menu = $this->container->make(Builder::class, [
            'container' => $this->container, 
            'name' => $name, 
            'type' => $type, 
            'attributes' => $this->container->make(Attributes::class, ['attributes' => $attributes]), 
            'activeAttributes' => $this->container->make(Attributes::class, ['attributes' => $activeAttributes]),
        ]);
        call_user_func($callback, $menu);
        $this->menuList[$name] = $menu;

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
        if (array_key_exists($name, $this->menuList) && ($menu = $this->menuList[$name]) instanceof Builder) {
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

    /**
     * Check exits global menu by name
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->menuList);
    }

    /**
     * Delete menu from global list
     *
     * @param string $name
     */
    public function forget($name)
    {
        if ($this->has($name)) {
            unset($this->menuList[$name]);
        }
    }
}