<?php
namespace Malezha\Menu\Traits;

use Malezha\Menu\Contracts\Attributes;

/**
 * Class HasAttributes
 * @package Malezha\Menu\Traits
 */
trait HasAttributes
{
    /**
     * @var Attributes
     */
    protected $attributes;

    /**
     * Get attributes object.
     * If send \Closure option as parameter then returned callback result.
     * 
     * @param callable|null $callback
     * @return Attributes|mixed
     */
    public function getAttributes($callback = null)
    {
        if (is_callable($callback)) {
            return call_user_func($callback, $this->attributes);
        }

        return $this->attributes;
    }

    /**
     * Build attributes to html valid string
     * 
     * @param array $attributes
     * @return string
     */
    public function buildAttributes($attributes = [])
    {
        return $this->attributes->build($attributes);
    }
}