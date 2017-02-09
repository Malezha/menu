<?php
namespace Malezha\Menu\Contracts;

interface FromArrayBuilder
{
    /**
     * @return static
     */
    public static function getInstance();

    /**
     * @param static $instance
     */
    public static function setInstance($instance);

    /**
     * @param array $array
     * @return Builder
     */
    public function build(array $array);
}