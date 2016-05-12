<?php
namespace Malezha\Menu\Contracts;

/**
 * Interface SubMenu
 * @package Malezha\Menu\Contracts
 */
interface SubMenu
{
    /**
     * @param Builder $menu
     * @param Item $item
     */
    public function __construct(Builder $menu, Item $item);

    /**
     * Get sub menu builder
     * 
     * @return Builder
     */
    public function getMenu();

    /**
     * Get sub menu item
     * 
     * @return Item
     */
    public function getItem();
}