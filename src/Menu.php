<?php

namespace Malezha\Menu;

use Illuminate\Support\Collection;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class Menu
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $collection;

    function __construct()
    {
        $this->collection = new Collection();
    }

    /**
     * @param $name
     * @param string $type
     * @param array $options
     * @param $callback
     * @return \Malezha\Menu\Builder
     */
    public function make($name, $type = 'ul', $options = [], $callback)
    {
        if(is_callable($callback)) {
            $menu = new Builder($name, $type, $options);
            call_user_func($callback, $menu);

            $this->collection->put($name, $menu);

            return $menu;
        } else {
            throw new InvalidArgumentException('Argument must be callable');
        }
    }

    /**
     * @param string $name
     * @return \Malezha\Menu\Builder
     */
    public function get($name)
    {
        return $this->collection->get($name);
    }

    /**
     * @param string $name
     * @param string|null $view
     * @return string
     */
    public function render($name, $view = null)
    {
        return $this->get($name)->render($view);
    }
}