<?php

namespace Malezha\Menu;

class Item
{
    use HasAttributes;

    /**
     * @var \Malezha\Menu\Link
     */
    protected $link;

    /**
     * @var \Malezha\Menu\Builder
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
        $this->builder = $builder;
        $this->attributes = new Attributes($attributes);
        $this->link = new Link($title, $url, $linkAttributes);
    }

    /**
     * @param null|\Malezha\Menu\Link $link
     * @return \Malezha\Menu\Link|\Malezha\Menu\Item
     */
    public function link($link = null)
    {
        if ($link instanceof Link) {
            $this->link = $link;

            return $this;
        }

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