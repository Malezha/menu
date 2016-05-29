<?php
namespace Malezha\Menu\Factory;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\ElementFactory;

/**
 * Class AbstractElementFactory
 * @package Malezha\Menu\Factory
 */
abstract class AbstractElementFactory implements ElementFactory
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @inheritdoc
     */
    public function __construct(Container $container)
    {
        $this->app = $container;
    }

    /**
     * @param string $class
     * @return array
     */
    protected function getElementConfig($class)
    {
        return $this->app->make(Repository::class)->get('menu.elements')[$class];
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function getParameter($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }

        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
        
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function unsetParameter($name)
    {
        unset($this->parameters[$name]);
        
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function existsParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @inheritDoc
     */
    function __call($name, $arguments)
    {
        preg_match("/^(get|set|unset|exists)([^;]+?)(;|$)/", $name, $matches);
        $parameter = lcfirst($matches[2]);
        $action = $matches[1];
        $method = $action . 'Parameter';

        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], array_merge(['name' => $parameter], $arguments));
        }

        throw new \RuntimeException("Parameter \"$parameter\" not found");
    }


}