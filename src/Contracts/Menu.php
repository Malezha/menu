<?php
namespace Malezha\Menu\Contracts;

use Illuminate\Contracts\Container\Container;

/**
 * Interface Menu
 * @package Malezha\Menu\Contracts
 */
interface Menu
{
    /**
     * Menu constructor.
     * @param Container $container
     */
    public function __construct(Container $container);

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
    public function make($name, \Closure $callback, $type = Builder::UL, $attributes = [], $activeAttributes = []);

    /**
     * Get global menu
     * 
     * @param string $name
     * @return Builder
     * @throws \RuntimeException
     */
    public function get($name);

    /**
     * Check exits global menu by name
     * 
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * Delete menu from global list
     * 
     * @param string $name
     */
    public function forget($name);

    /**
     * Render global menu to html
     * 
     * @param string $name
     * @param null|string $view
     */
    public function render($name, $view = null);
}