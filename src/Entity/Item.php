<?php
namespace Malezha\Menu\Entity;

use Malezha\Menu\Traits\DisplayRule;
use Malezha\Menu\Traits\HasAttributes;
use Malezha\Menu\Traits\IsUrlEqual;
use Malezha\Menu\Support\MergeAttributes;

class Item
{
    use HasAttributes, DisplayRule, IsUrlEqual;

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
    public function __construct(Builder $builder, $name, $attributes = [], $title = '', $url = '#', $linkAttributes = [])
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
            (new MergeAttributes($this->builder->activeAttributes()->all(), $attributes))->merge() :
            $attributes;

        return $this->attributes->build($attributes);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $currentUrl = app('request')->url();
        $url = url($this->getLink()->getUrl());
        
        return $this->isUrlEqual($url, $currentUrl);
    }
}