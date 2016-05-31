<?php
namespace Malezha\Menu\Render;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\MenuRender;

class Basic implements MenuRender
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var string
     */
    protected $view;

    /**
     * @inheritDoc
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function make($view)
    {
        $view = $this->replaceNameFromBlade($view);

        if (!$template = $this->findFullPath($view)) {
            throw new \Exception('View not found');
        }

        $this->view = $template;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function with($params, $value = null)
    {
        if (is_array($params)) {
            $this->variables = array_merge($this->variables, $params);
            
            return $this;
        }
        
        $this->variables[$params] = $value;
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $__e = extract($this->variables);

        ob_start();
        include($this->view);
        return ob_get_clean();
    }

    /**
     * @inheritDoc
     */
    public function exists($view)
    {
        $view = $this->replaceNameFromBlade($view);
        return (bool) $this->findFullPath($view);
    }

    /**
     * @param string $view
     * @return bool|string
     */
    protected function findFullPath($view)
    {
        $paths = $this->container->make('config')->get('menu.paths');
        
        foreach ($paths as $path) {
            $template = $path . '/' . $view . '.php';
            if (file_exists($template)) {
                return $template;
            }
        }

        return false;
    }

    /**
     * @param string $view
     * @return string
     */
    protected function replaceNameFromBlade($view)
    {
        return str_replace('.', '/', preg_replace("/(.*)(::)(.*)/", "$3", $view));
    }
}