<?php
namespace Malezha\Menu\Entity;

use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\Group as GroupContract;
use Malezha\Menu\Contracts\Item as ItemContract;

/**
 * Class Group
 * @package Malezha\Menu\Entity
 */
class Group implements GroupContract
{

    /**
     * @var ItemContract
     */
    protected $item;

    /**
     * @var BuilderContract
     */
    protected $menu;

    /**
     * @param BuilderContract $menu
     * @param ItemContract $item
     */
    public function __construct(BuilderContract $menu, ItemContract $item)
    {
        $this->menu = $menu;
        $this->item = $item;
    }

    /**
     * @return BuilderContract
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return ItemContract
     */
    public function getItem()
    {
        return $this->item;
    }
}