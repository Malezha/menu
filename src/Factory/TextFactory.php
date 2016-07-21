<?php
namespace Malezha\Menu\Factory;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Element\Text;
use Opis\Closure\SerializableClosure;

/**
 * Class TextFactory
 * @package Malezha\Menu\Factory
 * 
 * @property string $text
 * @property Attributes $attributes
 * @property mixed $displayRule
 */
class TextFactory extends AbstractElementFactory
{
    /**
     * @param array $parameters
     * @return Text
     */
    public function build($parameters = [])
    {
        $text = $this->app->make(Text::class, $this->mergeParameters($parameters));
        if (array_key_exists('displayRule', $this->parameters)) {
            $text->setDisplayRule($this->parameters['displayRule']);
        }

        return $text;
    }

    /**
     * @inheritDoc
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        
        $this->parameters = [
            'text' => null,
            'displayRule' => true,
            'attributes' => $this->app->make(Attributes::class, ['attributes' => []]),
            'view' => $this->getElementConfig(Text::class)['view'],
        ];
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