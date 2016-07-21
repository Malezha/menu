<?php
namespace Malezha\Menu\Factory;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Element\Link;
use Opis\Closure\SerializableClosure;

/**
 * Class LinkFactory
 * @package Malezha\Menu\Factory
 *
 * @property string $title
 * @property string $url
 * @property Attributes $attributes
 * @property Attributes $activeAttributes
 * @property Attributes $linkAttributes
 * @property string $view
 * @property mixed $displayRule
 *
 */
class LinkFactory extends AbstractElementFactory
{
    /**
     * @inheritdoc
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        
        $this->parameters = [
            'title' => '',
            'url' => '#',
            'attributes' => $this->app->make(Attributes::class, ['attributes' => []]),
            'activeAttributes' => $this->app->make(Attributes::class, ['attributes' => []]),
            'linkAttributes' => $this->app->make(Attributes::class, ['attributes' => []]),
            'view' => $this->getElementConfig(Link::class)['view'],
            'displayRule' => true,
        ];
    }

    /**
     * @param array $parameters
     * @return Link
     */
    public function build($parameters = [])
    {
        $link = $this->app->make(Link::class, $this->mergeParameters($parameters));
        $this->setDisplayRule($link);
        
        return $link;
    }
    
    protected function setDisplayRule(Link $link)
    {
        if (array_key_exists('displayRule', $this->parameters)) {
            $link->setDisplayRule($this->parameters['displayRule']);
        }
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        $params = $this->parameters;
        if ($params['displayRule'] instanceof \Closure) {
            $params['displayRule'] = new SerializableClosure($params['displayRule']);
        }

        return serialize($params);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        parent::unserialize($serialized);

        if ($this->parameters['displayRule'] instanceof SerializableClosure) {
            $this->parameters['displayRule'] = $this->parameters['displayRule']->getClosure();
        }
    }
}