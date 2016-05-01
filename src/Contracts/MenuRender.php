<?php
namespace Malezha\Menu\Contracts;

use Illuminate\Contracts\Container\Container;

/**
 * Interface MenuRender
 * @package Malezha\Menu\Contracts
 */
interface MenuRender
{
    /**
     * MenuRender constructor.
     * @param Container $container
     */
    public function __construct(Container $container);

    /**
     * Set view for render
     * 
     * @param string $view
     * @throws \Exception
     * @return MenuRender
     */
    public function make($view);

    /**
     * Add variables to view
     * 
     * @param array|string
     * @param null|mixed
     * @return MenuRender
     */
    public function with($params, $value = null);

    /**
     * @return string
     */
    public function render();

    /**
     * @param string $view
     * @return bool
     */
    public function exists($view);
}