<?php

namespace Malezha\Menu;

use Illuminate\Support\Collection;

class Item
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Collection
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

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param Builder $builder
     * @param string $name
     * @param string $title
     * @param string $url
     * @param array $attributes
     */
    function __construct(Builder $builder, $name, $title, $url, $attributes = [])
    {
        $this->name = $name;
        $this->title = $title;
        $this->url = $url;
        $this->attributes = new Collection($attributes);
        $this->builder = $builder;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function buildAttributes()
    {
        $result = '';

        $attributes = ($this->isActive()) ?
            $this->attributes->merge($this->builder->getActiveAttributes())->all() :
            $this->attributes->all();

        foreach ($attributes as $key => $value) {
            $result .= $key . '="' . $value . '" ';
        }

        return $result;
    }

    public function isActive()
    {
        return ($this->url == \Request::url());
    }
}