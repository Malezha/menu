<?php

namespace Malezha\Menu;

class Group
{
    /**
     * @var \Malezha\Menu\Item
     */
    protected $item;

    /**
     * @var \Malezha\Menu\Builder
     */
    protected $menu;

    /**
     * @param \Malezha\Menu\Builder $menu
     * @param \Malezha\Menu\Item $item
     */
    function __construct(Builder $menu, Item $item)
    {
        $this->menu = $menu;
        $this->item = $item;
    }

    /**
     * @return \Malezha\Menu\Builder
     */
    public function menu()
    {
        return $this->menu;
    }

    /**
     * @return \Malezha\Menu\Item
     */
    public function item()
    {
        return $this->item;
    }
}