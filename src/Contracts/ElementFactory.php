<?php
namespace Malezha\Menu\Contracts;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface ElementFactory
 * @package Malezha\Menu\Contracts
 */
interface ElementFactory extends \Serializable, Arrayable
{
    /**
     * ElementFactory constructor.
     * @param Container $container
     */
    public function __construct(Container $container);

    /**
     * @param array $parameters
     * @return Element
     */
    public function build($parameters = []);
}