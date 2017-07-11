<?php

namespace Malezha\Menu;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\Menu as MenuContract;

/**
 * Class Menu
 * @package Malezha\Menu
 */
class Menu implements MenuContract
{
    /**
     * @var BuilderContract[]
     */
    protected $menuList = [];

    /**
     * @var Container
     */
    protected $container;

    /**
     * @inheritDoc
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function make($name, callable $callback = null, $type = Builder::UL, $attributes = [], $activeAttributes = [])
    {
        $menu = $this->container->makeWith(BuilderContract::class, [
            'container' => $this->container, 
            'name' => $name, 
            'type' => $type, 
            'attributes' => $this->container->makeWith(Attributes::class, ['attributes' => $attributes]),
            'activeAttributes' => $this->container->makeWith(Attributes::class, ['attributes' => $activeAttributes]),
        ]);

        call_if_callable($callback, $menu);

        $this->menuList[$name] = $menu;

        return $menu;
    }

    /**
     * @inheritDoc
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->menuList) && ($menu = $this->menuList[$name]) instanceof BuilderContract) {
            return $menu;
        }

        throw new \RuntimeException('Menu not found');
    }

    /**
     * @inheritDoc
     */
    public function render($name, $view = null)
    {
        return $this->get($name)->render($view);
    }

    /**
     * @inheritDoc
     */
    public function has($name)
    {
        return array_key_exists($name, $this->menuList);
    }

    /**
     * @inheritDoc
     */
    public function forget($name)
    {
        if ($this->has($name)) {
            unset($this->menuList[$name]);
        }
    }

    /**
     * @inheritDoc
     */
    public function fromArray($name, array $builder)
    {
        /** @var BuilderContract $menu */
        $menu = $this->container->makeWith(BuilderContract::class, [
            'attributes' => $this->container->makeWith(Attributes::class, ['attributes' => []]),
            'activeAttributes' => $this->container->makeWith(Attributes::class, ['attributes' => []]),
        ]);
        
        $menu = $menu->fromArray($builder);

        $this->menuList[$name] = $menu;
        
        return $menu;
    }

    /**
     * @inheritDoc
     */
    public function toArray($name)
    {
        if (!$this->has($name)) {
            throw new \RuntimeException("Menu not found");
        }
        
        return $this->menuList[$name]->toArray();
    }
}