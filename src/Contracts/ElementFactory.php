<?php
namespace Malezha\Menu\Contracts;

use Illuminate\Contracts\Container\Container;

/**
 * Interface ElementFactory
 * @package Malezha\Menu\Contracts
 */
interface ElementFactory
{
    /**
     * ElementFactory constructor.
     * @param Container $container
     */
    public function __construct(Container $container);

    /**
     * @param array ...$options
     * @return Element
     */
    public function build(...$options);
}