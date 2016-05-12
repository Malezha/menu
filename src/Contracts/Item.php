<?php
namespace Malezha\Menu\Contracts;

use Illuminate\Http\Request;

/**
 * Interface Item
 * @package Malezha\Menu\Contracts
 */
interface Item extends HasAttributes, DisplayRule
{
    /**
     * Item constructor.
     * 
     * @param Builder $builder
     * @param Attributes $attributes
     * @param Link $link
     * @param Request $request
     */
    public function __construct(Builder $builder, Attributes $attributes, Link $link, Request $request);

    /**
     * Get link object
     * 
     * @return Link
     */
    public function getLink();
}