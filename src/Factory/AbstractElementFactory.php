<?php
namespace Malezha\Menu\Factory;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Element;
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
     * @param array $parameters
     * @return array
     */
    protected function mergeParameters($parameters = [])
    {
        return array_merge($this->parameters, $parameters);
    }

    /**
     * @inheritDoc
     */
    function __get($name)
    {
        return $this->getParameter($name);
    }

    /**
     * @inheritDoc
     */
    function __set($name, $value)
    {
        $this->setParameter($name, $value);
    }

    /**
     * @inheritDoc
     */
    function __isset($name)
    {
        return $this->existsParameter($name);
    }

    /**
     * @inheritDoc
     */
    function __unset($name)
    {
        $this->unsetParameter($name);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->build()->toArray();
    }

    /**
     * @param Element $element
     */
    protected function setDisplayRule(Element $element)
    {
        if (array_key_exists('displayRule', $this->parameters) && method_exists($element, 'setDisplayRule')) {
            $element->setDisplayRule($this->parameters['displayRule']);
        }
    }
}