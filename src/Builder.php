<?php

namespace Malezha\Menu;

use Illuminate\Support\Collection;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class Builder
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $attributes;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $active;

    /**
     * @param string $name
     * @param string $type
     * @param array $attributes
     * @param array $active
     */
    function __construct($name, $type = 'ul', $attributes = [], $active = ['class' => 'active'])
    {
        $this->name = $name;
        $this->type = $type;
        $this->attributes = new Collection($attributes);
        $this->items = new Collection();
        $this->active = $active;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $attributes
     * @param array $active
     * @param callable|null $callback
     * @return \Malezha\Menu\Builder
     */
    public static function make($name, $type = 'ul', $attributes = [], $active = ['class' => 'active'], $callback = null)
    {
        $menu = new Builder($name, $type, $attributes, $active);

        if (is_callable($callback)) {
            call_user_func($callback, $menu);
        }

        return $menu;
    }

    /**
     * @param string $name
     * @param callable $itemCallable
     * @param callable $menuCallable
     * @return \Malezha\Menu\Group
     */
    public function group($name, $itemCallable, $menuCallable)
    {
        if (is_callable($itemCallable) && is_callable($menuCallable)) {
            $item = new Item($this, $name);
            call_user_func($itemCallable, $item);

            $menu = new Builder($name);
            call_user_func($menuCallable, $menu);

            $group = new Group($menu, $item);

            $this->items->put($name, $group);

            return $group;
        } else {
            throw new InvalidArgumentException('Arguments must be callable');
        }
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $url
     * @param array $attributes
     * @param array $linkAttributes
     * @param callable|null $callback
     * @return Item
     */
    public function add($name, $title, $url, $attributes = [], $linkAttributes = [], $callback = null)
    {
        $item = new Item($this, $name, $attributes, $title, $url, $linkAttributes);

        if (is_callable($callback)) {
            call_user_func($callback, $item);
        }

        $this->items->put($name, $item);

        return $item;
    }

    /**
     * @return array
     */
    public function items()
    {
       return $this->items->values();
    }

    /**
     * @param string $name
     * @return \Malezha\Menu\Item|\Malezha\Menu\Group
     */
    public function get($name)
    {
        return $this->items->get($name);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items->all();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string|null $view
     * @return string
     */
    public function render($view = null)
    {
        $view = (empty($view)) ? config('menu.view') : $view;

        return view($view, [
            'menu' => $this,
        ])->render();
    }

    /**
     * @return array
     */
    public function getActiveAttributes()
    {
        return $this->active;
    }

    /**
     * @param $attributes
     * @return \Malezha\Menu\Builder
     */
    public function setActiveAttributes($attributes)
    {
        $this->active = $attributes;

        return $this;
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function buildAttributes($attributes = [])
    {
        return build_html_attributes(array_merge($this->attributes->toArray(), $attributes));
    }
}