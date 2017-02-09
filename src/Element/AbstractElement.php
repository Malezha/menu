<?php
namespace Malezha\Menu\Element;

use Malezha\Menu\Contracts\Element;
use Malezha\Menu\Contracts\MenuRender;
use Serafim\Properties\Properties;

/**
 * Class AbstractElement
 * @package Malezha\Menu\Element
 */
abstract class AbstractElement implements Element
{
    use Properties;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var MenuRender
     */
    protected $render;

    /**
     * @inheritdoc
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @inheritdoc
     */
    public function setView($view)
    {
        if ($this->render->exists($view)) {
            $this->view = $view;
        }
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'view' => $this->view,
        ];
    }
}