<?php

namespace Malezha\Menu;

trait HasAttributes
{
    /**
     * @var \Malezha\Menu\Attributes
     */
    protected $attributes;

    /**
     * @return \Malezha\Menu\Attributes
     */
    public function attributes()
    {
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