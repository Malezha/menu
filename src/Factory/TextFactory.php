<?php
namespace Malezha\Menu\Factory;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Element\Text;

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
        $text = $this->app->makeWith(Text::class, $this->mergeParameters($parameters));
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
            'attributes' => $this->app->makeWith(Attributes::class, ['attributes' => []]),
            'view' => $this->getElementConfig(Text::class)['view'],
        ];
    }
}