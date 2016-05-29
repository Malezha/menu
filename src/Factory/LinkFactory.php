<?php
namespace Malezha\Menu\Factory;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Element\Link;

/**
 * Class LinkFactory
 * @package Malezha\Menu\Factory
 *
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method $this unsetTitle()
 * @method bool existsTitle()
 * @method string getUrl()
 * @method $this setUrl(string $value)
 * @method $this unsetUrl()
 * @method bool existsUrl()
 * @method Attributes getAttributes()
 * @method $this setAttributes(Attributes $value)
 * @method $this unsetAttributes()
 * @method bool existsAttributes()
 * @method Attributes getActiveAttributes()
 * @method $this setActiveAttributes(Attributes $value)
 * @method $this unsetActiveAttributes()
 * @method bool existsActiveAttributes()
 * @method Attributes getLinkAttributes()
 * @method $this setLinkAttributes(Attributes $value)
 * @method $this unsetLinkAttributes()
 * @method bool existsLinkAttributes()
 * @method string getView()
 * @method $this setView(string $value)
 * @method $this unsetView()
 * @method bool existsView()
 * @method string getCurrentUrl()
 * @method $this setCurrentUrl(string $value)
 * @method $this unsetCurrentUrl()
 * @method bool existsCurrentUrl()
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
            'currentUrl' => $this->app->make(Request::class)->url(),
        ];
    }

    /**
     * @param array ...$options
     * @return Link
     */
    public function build(...$options)
    {
        $parameters = array_merge($this->parameters, $options);
        
        return $this->app->make(Link::class, $parameters);
    }
}