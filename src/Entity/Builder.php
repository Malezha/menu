<?php
namespace Malezha\Menu\Entity;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\Group;
use Malezha\Menu\Contracts\HasAttributes as HasAttributesContract;
use Malezha\Menu\Contracts\Item;
use Malezha\Menu\Contracts\Link;
use Malezha\Menu\Traits\HasAttributes;

/**
 * Class Builder
 * @package Malezha\Menu\Entity
 */
class Builder implements BuilderContract
{
    use HasAttributes;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var AttributesContract
     */
    protected $activeAttributes;

    /**
     * @param Container $container
     * @param string $name
     * @param string $type
     * @param array $attributes
     * @param array $activeAttributes
     */
    public function __construct(Container $container, $name, $type = self::UL, $attributes = [], $activeAttributes = [])
    {
        $this->container = $container;
        $this->name = $name;
        $this->type = $type;
        $this->attributes = $this->container->make(AttributesContract::class, ['attributes' => $attributes]);
        $this->items = [];
        $this->activeAttributes = $this->container->make(AttributesContract::class, ['attributes' => $activeAttributes]);
    }

    /**
     * @param string $name
     * @param \Closure $itemCallable
     * @param \Closure $menuCallable
     * @return Group
     */
    public function group($name, \Closure $itemCallable, \Closure $menuCallable)
    {
        $item = $this->container->make(Item::class, [
            'builder' => $this,
            'attributes' => $this->container->make(AttributesContract::class, ['attributes' => []]),
            'link' => $this->container->make(Link::class, [
                'title' => $name,
                'attributes' => $this->container->make(AttributesContract::class, ['attributes' => []]),
            ]),
            'request' => $this->container->make('request'),
        ]);
        call_user_func($itemCallable, $item);

        $menu = $this->container->make(BuilderContract::class, [
            'container' => $this->container, 
            'name' => $name,
            'activeAttributes' => $this->activeAttributes()->all(),
        ]);
        call_user_func($menuCallable, $menu);

        $group = $this->container->make(Group::class, [
            'menu' => $menu,
            'item' => $item,
        ]);
        $this->items[$name] = $group;

        return $group;
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $url
     * @param array $attributes
     * @param array $linkAttributes
     * @param \Closure|null $callback
     * @return Item
     */
    public function add($name, $title, $url, $attributes = [], $linkAttributes = [], $callback = null)
    {
        $link = $this->container->make(Link::class, [
            'title' => $title,
            'url' => $url,
            'attributes' => $this->container->make(AttributesContract::class, ['attributes' => $linkAttributes]),
        ]);
        
        $item = $this->container->make(Item::class, [
            'builder' => $this,
            'attributes' => $this->container->make(AttributesContract::class, ['attributes' => $attributes]),
            'link' => $link,
            'request' => $this->container->make('request'),
        ]);

        if (is_callable($callback)) {
            call_user_func($callback, $item);
        }

        $this->items[$name] = $item;

        return $item;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->items);
    }

    /**
     * @param string $name
     * @param mixed|null $default
     * @return Item|Group|null
     */
    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            return $this->items[$name];
        }
        return $default;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param string $name
     */
    public function forget($name)
    {
        if ($this->has($name)) {
            unset($this->items[$name]);
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = (string)$type;
    }

    /**
     * @param string|null $view
     * @return string
     */
    public function render($view = null)
    {
        /** @var Repository $config */
        $config = $this->container->make('config');
        
        $view = (empty($view)) ? $config->get('menu.view') : $view;
        $minify = $config->get('menu.minify', false);
        
        /* @var ViewFactory $viewFactory */
        $viewFactory = $this->container->make(ViewFactory::class);

        $rendered = $viewFactory->make($view, [
            'menu' => $this,
            'renderView' => $view,
        ])->render();
        
        if ($minify) {
            $rendered = $this->minifyHtmlOutput($rendered);
        }
        
        return $rendered;
    }

    /**
     * @param \Closure|null $callback
     * @return Attributes|mixed
     */
    public function activeAttributes($callback = null)
    {
        if (is_callable($callback)) {
            return call_user_func($callback, $this->activeAttributes);
        }

        return $this->activeAttributes;
    }

    /**
     * @param $html
     * @return mixed
     */
    protected function minifyHtmlOutput($html)
    {
        $search = array(
            '/\>[^\S]+/s',  // strip whitespaces after tags, except space
            '/[^\S]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );

        $replace = array(
            '>',
            '<',
            '\\1'
        );

        return preg_replace($search, $replace, $html);
    }
}