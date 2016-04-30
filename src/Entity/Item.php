<?php
namespace Malezha\Menu\Entity;

use Illuminate\Http\Request;
use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\Link as LinkContract;
use Malezha\Menu\Contracts\Item as ItemContract;
use Malezha\Menu\Traits\DisplayRule;
use Malezha\Menu\Traits\HasAttributes;
use Malezha\Menu\Support\MergeAttributes;

/**
 * Class Item
 * @package Malezha\Menu\Entity
 */
class Item implements ItemContract
{
    use HasAttributes, DisplayRule;

    /**
     * @var LinkContract
     */
    protected $link;

    /**
     * @var BuilderContract
     */
    protected $builder;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Item constructor.
     * @param BuilderContract $builder
     * @param AttributesContract $attributes
     * @param LinkContract $link
     * @param Request $request
     */
    public function __construct(BuilderContract $builder, AttributesContract $attributes, 
                                LinkContract $link, Request $request)
    {
        $this->builder = $builder;
        $this->attributes = $attributes;
        $this->link = $link;
        $this->request = $request;
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
            (new MergeAttributes($this->builder->activeAttributes()->all(), $attributes))->merge() : $attributes;

        return $this->attributes->build($attributes);
    }

    /**
     * @return bool
     */
    protected function isActive()
    {
        $currentUrl = $this->request->url();
        $url = url($this->getLink()->getUrl());
        
        return $this->isUrlEqual($url, $currentUrl);
    }

    /**
     * Check is two url equal
     *
     * @param string $first
     * @param string $second
     * @return bool
     */
    protected function isUrlEqual($first, $second)
    {
        $uriForTrim = [
            '#',
            '/index',
            '/'
        ];
        
        foreach ($uriForTrim as $trim) {
            $first = rtrim($first, $trim);
            $second = rtrim($second, $trim);
        }

        return $first == $second;
    }
}