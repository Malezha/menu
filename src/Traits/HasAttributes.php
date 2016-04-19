<?php

namespace Malezha\Menu\Traits;

use Malezha\Menu\Entity\Attributes;

trait HasAttributes
{
    /**
     * @var Attributes
     */
    protected $attributes;

    /**
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
     * @param array $attributes
     * @return string
     */
    public function buildAttributes($attributes = [])
    {
        return $this->attributes->build($attributes);
    }
}