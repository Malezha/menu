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
}