<?php
namespace Malezha\Menu\Factory;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Element\SubMenu;

/**
 * Class SubMenuFactory
 * @package Malezha\Menu\Factory
 * 
 * @method Builder getBuilder()
 * @method $this setBuilder(Builder $value)
 * @method $this unsetBuilder()
 * @method bool existsBuilder()
 * @inheritdoc
 */
class SubMenuFactory extends LinkFactory
{
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->parameters['builder'] = $this->app->make(Builder::class, [
            'container' => $this->app,
            'name' => 'submenu' . spl_object_hash($this),
            'activeAttributes' => $this->app->make(Attributes::class, ['attributes' => []]),
            'attributes' => $this->app->make(Attributes::class, ['attributes' => []]),
        ]);
        $this->parameters['view'] = $this->getElementConfig(SubMenu::class)['view'];
    }

    public function build(...$options)
    {
        $parameters = array_merge($this->parameters, $options);

        return $this->app->make(SubMenu::class, $parameters);
    }
}