<?php
namespace Malezha\Menu\Contracts;

/**
 * Interface HasActiveAttributes
 * @package Malezha\Menu\Contracts
 */
interface HasActiveAttributes
{
    /**
     * Get attributes object.
     * If send \Closure option as parameter then returned callback result.
     *
     * @param callable|null $callback
     * @return Attributes|mixed
     */
    public function getActiveAttributes($callback = null);
}