<?php

namespace Malezha\Menu\Entity;

use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Traits\DisplayRule;

class Group
{
    use DisplayRule;

    /**
     * @var Item
     */
    protected $item;

    /**
     * @var BuilderContract
     */
    protected $menu;

    /**
     * @param BuilderContract $menu
     * @param Item $item
     */
    function __construct(BuilderContract $menu, Item $item)
    {
        $this->menu = $menu;
        $this->item = $item;
    }

    /**
     * @return BuilderContract
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