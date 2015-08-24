<?php

namespace Malezha\Menu;

use Illuminate\Support\Collection;

class Link
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $attributes;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $title;


    function __construct($title = '', $url = '#', $attributes = [])
    {
        $this->title = $title;
        $this->url = $url;
        $this->attributes = new Collection($attributes);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \Malezha\Menu\Item
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     * @return \Malezha\Menu\Item
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
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
     * @param array $attributes
     * @return string
     */
    public function buildAttributes($attributes = [])
    {
        return build_html_attributes(array_merge($this->attributes->toArray(), $attributes));
    }
}