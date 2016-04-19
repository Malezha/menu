<?php

namespace Malezha\Menu\Entity;

use Malezha\Menu\Traits\DisplayRule;
use Malezha\Menu\Traits\HasAttributes;

class Item
{
    use HasAttributes, DisplayRule;

    /**
     * @var Link
     */
    protected $link;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param Builder $builder
     * @param string $name
     * @param array $attributes
     * @param string $title
     * @param string $url
     * @param array $linkAttributes
     */
    function __construct(Builder $builder, $name, $attributes = [], $title = '', $url = '#', $linkAttributes = [])
    {
        $title = empty($title) ? $name : $title;
        $this->builder = $builder;
        $this->attributes = new Attributes($attributes);
        $this->link = new Link($title, $url, $linkAttributes);
    }

    /**
     * @return Link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function buildAttributes($attributes = [])
    {
        $attributes = $this->isActive() ?
            Attributes::mergeArrayValues($this->builder->activeAttributes(), $attributes) :
            $attributes;

        return $this->attributes->build($attributes);
    }

    /**
     * @return bool
     */
    protected function isActive()
    {
        return ($this->link()->url() == app('request')->url());
    }
}