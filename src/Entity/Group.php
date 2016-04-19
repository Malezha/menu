<?php

namespace Malezha\Menu\Entity;

use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Traits\DisplayRule;

class Group
{
    use DisplayRule;

    /**
     * @var Item
     */
    protected $item;

    /**
     * @var Builder
     */
    protected $menu;

    /**
     * @param Builder $menu
     * @param Item $item
     */
    function __construct(Builder $menu, Item $item)
    {
        $this->menu = $menu;
        $this->item = $item;
    }

    /**
     * @return Builder
     */
    public function menu()
    {
        return $this->menu;
    }

    /**
     * @return Item
     */
    public function item()
    {
        return $this->item;
    }
}