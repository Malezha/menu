<?php

namespace Malezha\Menu;

use Illuminate\Support\Collection;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class Builder
{
    protected $items;

    protected $name;

    protected $attributes;

    protected $type;

    protected $active;

    protected $group = null;

    function __construct($name, $type, $attributes = [], $active = ['class' => 'active'])
    {
        $this->name = $name;
        $this->type = $type;
        $this->attributes = new Collection($attributes);
        $this->items = new Collection();
        $this->active = $active;
    }

    public function group($name, $type, $attributes = [], $callback)
    {
        if(is_callable($callback)) {
            $group = new Builder($name, $type, $attributes, $this->active);

            call_user_func($callback, $group);

            $this->items->push($group);

            return $group;
        } else {
            throw new InvalidArgumentException('Argument must be callable');
        }
    }

    public function add($name, $title, $url, $attributes = [])
    {
        $item = new Item($this, $name, $title, $url, $attributes);
        $this->items->push($item);

        return $item;
    }

    public function items()
    {
       return $this->items;
    }

    public function get($name)
    {
        return $this->items->get($name);
    }

    public function toArray()
    {
        return $this->items->all();
    }

    public function getType()
    {
        return $this->type;
    }

    public function render()
    {
        return view(config('menu.view'), [
            'menu' => $this,
        ])->render();
    }

    public function getActiveAttributes()
    {
        return $this->active;
    }

    public function setActiveAttributes($attributes)
    {
        $this->active = $attributes;
    }

    public function buildAttributes($attributes = [])
    {
        if(empty($attributes)) {
            $attributes = $this->attributes->all();
        }

        $result = '';

        foreach ($attributes as $key => $value) {
            $result .= $key . '="' . $value . '" ';
        }

        return $result;
    }

    public function thisGroup($title, $url, $attributes = [])
    {
        $group = new \stdClass();
        $group->title = $title;
        $group->url = $url;
        $group->attributes = $attributes;

        $this->group = $group;
    }

    public function getGroup()
    {
        return $this->group;
    }
}