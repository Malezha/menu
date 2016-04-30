<?php
namespace Malezha\Menu\Contracts;

use Illuminate\Contracts\Container\Container;

/**
 * Interface Builder
 * @package Malezha\Menu\Contracts
 */
interface Builder extends HasAttributes
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
     * Make sub menu
     *
     * @param string $name
     * @param \Closure $itemCallable
     * @param \Closure $menuCallable
     * @return mixed
     */
    public function group($name, \Closure $itemCallable, \Closure $menuCallable);

    /**
     * Add new element
     *
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
     * Check exits by name
     *
     * @param string $name
     */
    public function has($name);

    /**
     * Get element or sub menu by name
     *
     * @param string $name
     * @param mixed|null $default
     * @return Item|Group|null
     */
    public function get($name, $default = null);

    /**
     * Get all elements and sub menus
     *
     * @return array
     */
    public function all();

    /**
     * Delete element
     *
     * @param string $name
     */
    public function forget($name);

    /**
     * Get menu type: UL or OL
     *
     * @return string
     */
    public function getType();

    /**
     * Set menu type. You can use constants at this interface
     *
     * @param string $type
     */
    public function setType($type);

    /**
     * Render menu to html
     *
     * @param string|null $view
     * @return string
     */
    public function render($view = null);

    /**
     * Get active attributes object.
     * If send \Closure option as parameter then returned callback result.
     *
     * @param \Closure|null $callback
     * @return Attributes|mixed
     */
    public function activeAttributes($callback = null);
}