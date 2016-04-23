<?php
namespace Malezha\Menu\Contracts;

use Illuminate\Contracts\Container\Container;

interface Menu
{
    /**
     * Menu constructor.
     * @param Container $container
     */
    public function __construct(Container $container);

    /**
     * @param string $name
     * @param callable $callback
     * @param string $type
     * @param array $attributes
     * @param array $activeAttributes
     * @return Builder
     */
    public function make($name, $callback, $type = Builder::UL, $attributes = [], $activeAttributes = []);

    /**
     * @param string $name
     * @return Builder
     * @throws \RuntimeException
     */
    public function get($name);

    /**
     * @param string $name
     * @param null|string $view
     */
    public function render($name, $view = null);
}