<?php
namespace Malezha\Menu\Contracts;

/**
 * Interface Group
 * @package Malezha\Menu\Contracts
 */
interface Group extends DisplayRule
{
    /**
     * @param Builder $menu
     * @param Item $item
     */
    public function __construct(Builder $menu, Item $item);

    /**
     * @return Builder
     */
    public function getMenu();

    /**
     * @return Item
     */
    public function getItem();
}