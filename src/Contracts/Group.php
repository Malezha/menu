<?php
namespace Malezha\Menu\Contracts;

/**
 * Interface Group
 * @package Malezha\Menu\Contracts
 */
interface Group
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