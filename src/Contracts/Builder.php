<?php

namespace Malezha\Menu\Contracts;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Attributes;
use Malezha\Menu\Item;

interface Builder
{
    const UL = 'ul';

    const OL = 'ol';
    
    /**
     * @param Container $container 
     * @param string $name
     * @param string $type
     * @param array $attributes
     * @param array $activeAttributes
     */
    function __construct(Container $container, $name, $type = \Malezha\Menu\Builder::UL, $attributes = [], $activeAttributes = []);

    /**
     * @param string $name
     * @param callable $itemCallable
     * @param callable $menuCallable
     * @return \Malezha\Menu\Group
     */
    public function group($name, $itemCallable, $menuCallable);

    /**
     * @param string $name
     * @param string $title
     * @param string $url
     * @param array $attributes
     * @param array $linkAttributes
     * @param callable|null $callback
     * @return Item
     */
    public function add($name, $title, $url, $attributes = [], $linkAttributes = [], $callback = null);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function items();

    /**
     * @return array
     */
    public function values();

    /**
     * @param string $name
     */
    public function has($name);

    /**
     * @param string $name
     * @return \Malezha\Menu\Item|\Malezha\Menu\Group
     */
    public function get($name);

    /**
     * @return array
     */
    public function all();

    /**
     * @param string $name
     */
    public function forget($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @param string|null $view
     * @return string
     */
    public function render($view = null);

    /**
     * @param callable|null $callback
     * @return Attributes|mixed
     */
    public function activeAttributes($callback = null);

    /**
     * @param callable|null $callback
     * @return Attributes|mixed
     */
    public function attributes($callback = null);

    /**
     * @param array $attributes
     * @return string
     */
    public function buildAttributes($attributes = []);
}