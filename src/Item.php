<?php

namespace Malezha\Menu;

use Illuminate\Support\Collection;

class Item
{
    /**
     * @var \Malezha\Menu\Link
     */
    protected $link;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $attributes;

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
        $this->attributes = new Collection($attributes);
        $this->link = new Link($title, $url, $linkAttributes);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return \Malezha\Menu\Item
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = new Collection($attributes);

        return $this;
    }

    /**
     * @return \Malezha\Menu\Link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param Link $link
     * @return \Malezha\Menu\Item
     */
    public function setLink(Link $link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function buildAttributes($attributes = [])
    {
        $attributes = ($this->isActive()) ?
            array_merge($this->attributes->toArray(), $this->builder->getActiveAttributes(), $attributes) :
            array_merge($this->attributes->all(), $attributes);

        return build_html_attributes($attributes);
    }

    /**
     * @return bool
     */
    protected function isActive()
    {
        return ($this->getLink()->getUrl() == app('request')->url());
    }
}