<?php
namespace Malezha\Menu\Contracts;

/**
 * Interface HasAttributes
 * @package Malezha\Menu\Contracts
 */
interface HasAttributes
{
    /**
     * Get attributes object.
     * If send \Closure option as parameter then returned callback result.
     * 
     * @param callable|null $callback
     * @return Attributes|mixed
     */
    public function getAttributes($callback = null);

    /**
     * Build attributes to html valid string
     * 
     * @param array $attributes
     * @return string
     */
    public function buildAttributes($attributes = []);
}