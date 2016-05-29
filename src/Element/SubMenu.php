<?php
namespace Malezha\Menu\Element;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\MenuRender;

/**
 * Class SubMenu
 * @package Malezha\Menu\Element
 */
class SubMenu extends Link
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * SubMenu constructor.
     * @param string $title
     * @param string $url
     * @param Attributes $attributes
     * @param Attributes $activeAttributes
     * @param Attributes $linkAttributes
     * @param string $view
     * @param string $currentUrl
     * @param MenuRender $render
     * @param Builder $builder
     */
    public function __construct($title, $url, Attributes $attributes, Attributes $activeAttributes,
                                Attributes $linkAttributes, $view, $currentUrl, MenuRender $render, Builder $builder)
    {
        parent::__construct($title, $url, $attributes, $activeAttributes, $linkAttributes, $view, $currentUrl, $render);
        
        $this->builder = $builder;
    }

    /**
     * @inheritDoc
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @inheritdoc
     */
    public function renderWith()
    {
        $renderView = func_get_arg(0);

        return array_merge(parent::renderWith(), [
            'builder' => $this->builder,
            'renderView' => $renderView,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function render($view = null)
    {
        return $this->render->make($this->view)
            ->with($this->renderWith($view))
            ->render();
    }
}