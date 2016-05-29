<?php
namespace Malezha\Menu\Render;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Malezha\Menu\Contracts\MenuRender;

/**
 * Class Illuminate
 * @package Malezha\Menu\Render
 */
class Illuminate implements MenuRender
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var View
     */
    protected $view = null;

    /**
     * @inheritDoc
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->factory = $this->container->make(Factory::class);
    }

    /**
     * @inheritDoc
     */
    public function make($view)
    {
        if (!$this->exists($view)) {
            throw new \Exception('View not found');
        }

        $this->view = $this->factory->make($view);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function with($params, $value = null)
    {
        if (!is_null($this->view)) {
            $this->view->with($params, $value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        if (!is_null($this->view)) {
            return $this->view->render();
        }

        return '';
    }

    /**
     * Determine if a given view exists.
     *
     * @param  string  $view
     * @return bool
     */
    public function exists($view)
    {
        return $this->factory->exists($view);
    }
}