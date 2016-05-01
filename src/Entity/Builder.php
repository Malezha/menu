<?php
namespace Malezha\Menu\Entity;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\Group as GroupContract;
use Malezha\Menu\Contracts\Item as ItemContract;
use Malezha\Menu\Contracts\Link as LinkContract;
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
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $view;
    
    /**
     * Builder constructor.
     *
     * @param Container $container
     * @param string $name
     * @param AttributesContract $attributes
     * @param AttributesContract $activeAttributes
     * @param string $type
     * @param string $view
     */
    public function __construct(Container $container, $name, AttributesContract $attributes,
                                AttributesContract $activeAttributes, $type = self::UL, $view = null)
    {
        $this->container = $container;
        $this->name = $name;
        $this->type = $type;
        $this->attributes = $attributes;
        $this->items = [];
        $this->activeAttributes = $activeAttributes;
        $this->viewFactory = $this->container->make(ViewFactory::class);
        $this->config = $this->container->make('config')->get('menu');
        try {
            $this->setView($view);
        } catch (\Exception $e) {
            $this->view = $this->config['view'];
        }
    }

    /**
     * Make sub menu
     *
     * @param string $name
     * @param \Closure $itemCallable
     * @param \Closure $menuCallable
     * @return mixed
     */
    public function group($name, \Closure $itemCallable, \Closure $menuCallable)
    {
        $item = $this->container->make(ItemContract::class, [
            'builder' => $this,
            'attributes' => $this->container->make(AttributesContract::class, ['attributes' => []]),
            'link' => $this->container->make(LinkContract::class, [
                'title' => $name,
                'attributes' => $this->container->make(AttributesContract::class, ['attributes' => []]),
            ]),
            'request' => $this->container->make('request'),
        ]);
        call_user_func($itemCallable, $item);

        $menu = $this->container->make(BuilderContract::class, [
            'container' => $this->container, 
            'name' => $name,
            'activeAttributes' => $this->activeAttributes(),
            'attributes' => $this->container->make(AttributesContract::class, ['attributes' => []]),
            'view' => $this->getView(),
        ]);
        call_user_func($menuCallable, $menu);

        $group = $this->container->make(GroupContract::class, [
            'menu' => $menu,
            'item' => $item,
        ]);
        $this->items[$name] = $group;

        return $group;
    }

    /**
     * Add new element
     *
     * @param string $name
     * @param string $title
     * @param string $url
     * @param array $attributes
     * @param array $linkAttributes
     * @param \Closure|null $callback
     * @return ItemContract
     */
    public function add($name, $title, $url, $attributes = [], $linkAttributes = [], $callback = null)
    {
        $link = $this->container->make(LinkContract::class, [
            'title' => $title,
            'url' => $url,
            'attributes' => $this->container->make(AttributesContract::class, ['attributes' => $linkAttributes]),
        ]);
        
        $item = $this->container->make(ItemContract::class, [
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
     * Check exits by name
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->items);
    }

    /**
     * Get element or sub menu by name
     *
     * @param string $name
     * @param mixed|null $default
     * @return ItemContract|GroupContract|null
     */
    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            return $this->items[$name];
        }
        return $default;
    }

    /**
     * Get all elements and sub menus
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Delete element
     *
     * @param string $name
     */
    public function forget($name)
    {
        if ($this->has($name)) {
            unset($this->items[$name]);
        }
    }

    /**
     * Get menu type: UL or OL
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set menu type. You can use constants at this interface
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = (string) $type;
    }

    /**
     * Render menu to html
     *
     * @param string|null $view
     * @return string
     */
    public function render($view = null)
    {
        try {
            $this->setView($view);
        } catch (\Exception $e) {}

        $view = $this->getView();

        $minify = $this->config['minify'];

        $rendered = $this->viewFactory->make($view, [
            'menu' => $this,
            'renderView' => $view,
        ])->render();
        
        if ($minify) {
            $rendered = $this->minifyHtmlOutput($rendered);
        }
        
        return $rendered;
    }

    /**
     * Get active attributes object.
     * If send \Closure option as parameter then returned callback result.
     *
     * @param \Closure|null $callback
     * @return AttributesContract|mixed
     */
    public function activeAttributes($callback = null)
    {
        if (is_callable($callback)) {
            return call_user_func($callback, $this->activeAttributes);
        }

        return $this->activeAttributes;
    }

    /**
     * Get render view
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set render view
     *
     * @param string $view
     * @throws \Exception
     */
    public function setView($view)
    {
        if (!$this->viewFactory->exists($view)) {
            throw new \Exception('View not found');
        }
        
        $this->view = $view;
    }

    /**
     * Minify html
     *
     * @param string $html
     * @return string
     */
    protected function minifyHtmlOutput($html)
    {
        $search = array(
            '/\>[^\S]+/s', // strip whitespaces after tags, except space
            '/[^\S]+\</s', // strip whitespaces before tags, except space
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