<?php

namespace Malezha\Menu\Contracts;

use Illuminate\Contracts\Container\Container;

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
    function __construct(Container $container, $name, $type = self::UL, $attributes = [], $activeAttributes = []);

    /**
     * @param string $name
     * @param \Closure $itemCallable
     * @param \Closure $menuCallable
     * @return mixed
     */
    public function group($name, \Closure $itemCallable, \Closure $menuCallable);

    /**
     * @param string $name
     * @param string $title
     * @param string $url
     * @param array $attributes
     * @param array $linkAttributes
     * @param \Closure|null $callback
     * @return mixed
     */
    public function add($name, $title, $url, $attributes = [], $linkAttributes = [], $callback = null);

    /**
     * @param string $name
     */
    public function has($name);

    /**
     * @param string $name
     * @param mixed|null $default
     * @return Item|Group|null
     */
    public function get($name, $default = null);

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
     * @param \Closure|null $callback
     * @return mixed
     */
    public function activeAttributes($callback = null);

    /**
     * @param \Closure|null $callback
     * @return mixed
     */
    public function getAttributes($callback = null);
}