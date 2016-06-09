<?php
namespace Malezha\Menu\Factory;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\ComparativeUrl;
use Malezha\Menu\Element\Link;

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
            'currentUrl' => $this->app->make(ComparativeUrl::class),
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
}