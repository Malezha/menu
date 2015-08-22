<?php

namespace Malezha\Menu;

use Illuminate\Support\Collection;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class Menu
{
    protected $collection;

    function __construct()
    {
        $this->collection = new Collection();
    }

    public function make($name, $type, $options = [], $callback)
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

    public function get($name)
    {
        return $this->collection->get($name);
    }

    public function render($name)
    {
        return $this->get($name)->render();
    }
}