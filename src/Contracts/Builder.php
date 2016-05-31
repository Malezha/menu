<?php
namespace Malezha\Menu\Contracts;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface Builder
 * @package Malezha\Menu\Contracts
 */
interface Builder extends HasAttributes, HasActiveAttributes, Arrayable, \ArrayAccess, \Serializable
{
    const UL = 'ul';

    const OL = 'ol';

    /**
     * @param Container $container
     * @param Attributes $attributes
     * @param Attributes $activeAttributes
     * @param string $type
     * @param string $view
     * @internal param string $name
     */
    function __construct(Container $container, Attributes $attributes, Attributes $activeAttributes,
                         $type = self::UL, $view = null);

    /**
     * @param string $name
     * @param string $type
     * @param callable $callback
     * @return Element
     */
    public function create($name, $type, $callback = null);

    /**
     * Insert values before item
     * 
     * @param string $name
     * @param \Closure $callback
     * @return Element
     */
    public function insertBefore($name, \Closure $callback);

    /**
     * Insert values after item
     * 
     * @param string $name
     * @param \Closure $callback
     * @return mixed
     */
    public function insertAfter($name, \Closure $callback);

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
     * @return Element|mixed
     */
    public function get($name, $default = null);

    /**
     * Get element or sub menu by index
     *
     * @param int $index
     * @param mixed|null $default
     * @return Element|mixed
     */
    public function getByIndex($index, $default = null);

    /**
     * Get all elements
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
     * Get render view
     *
     * @return string
     */
    public function getView();

    /**
     * Set render view
     *
     * @param string $view
     * @throws \Exception
     */
    public function setView($view);
}