<?php
namespace Malezha\Menu\Entity;

use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\SubMenu as SubMenuContract;
use Malezha\Menu\Contracts\Item as ItemContract;

/**
 * Class SubMenu
 * @package Malezha\Menu\Entity
 */
class SubMenu implements SubMenuContract
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
     * @inheritDoc
     */
    public function __construct(BuilderContract $menu, ItemContract $item)
    {
        $this->menu = $menu;
        $this->item = $item;
    }

    /**
     * @inheritDoc
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @inheritDoc
     */
    public function getItem()
    {
        return $this->item;
    }
}