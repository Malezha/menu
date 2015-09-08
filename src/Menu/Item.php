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
     * @return \Malezha\Menu\Link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param \Malezha\Menu\Link $link
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
        $attributes = $this->isActive() ?
            Attributes::mergeArrayValues($this->builder->getActiveAttributes(), $attributes) :
            $attributes;

        return $this->attributes->build($attributes);
    }

    /**
     * @return bool
     */
    protected function isActive()
    {
        return ($this->getLink()->getUrl() == app('request')->url());
    }
}