<?php
namespace Malezha\Menu\Entity;

use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\SubMenu as GroupContract;
use Malezha\Menu\Contracts\Item as ItemContract;
use Malezha\Menu\Contracts\Link as LinkContract;
use Malezha\Menu\Contracts\MenuRender;
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
     * @var MenuRender
     */
    protected $viewFactory;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $view = null;
    
    /**
     * @inheritDoc
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
        $this->viewFactory = $this->container->make(MenuRender::class);
        $this->config = $this->container->make('config')->get('menu');
        try {
            $this->setView($view);
        } catch (\Exception $e) {}
    }

    /**
     * @inheritDoc
     */
    public function submenu($name, \Closure $itemCallable, \Closure $menuCallable)
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
     * @inheritDoc
     */
    public function create($name, $title, $url, $attributes = [], $linkAttributes = [], $callback = null)
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
     * @inheritDoc
     */
    public function has($name)
    {
        return array_key_exists($name, $this->items);
    }

    /**
     * @inheritDoc
     */
    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            return $this->items[$name];
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function forget($name)
    {
        if ($this->has($name)) {
            unset($this->items[$name]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->type = (string) $type;
    }

    /**
     * @inheritDoc
     */
    public function render($renderView = null)
    {
        $view = $this->getRenderView($renderView);

        $minify = $this->config['minify'];

        $rendered = $this->viewFactory->make($view)->with([
            'menu' => $this,
            'renderView' => $renderView,
        ])->render();
        
        if ($minify) {
            $rendered = $this->minifyHtmlOutput($rendered);
        }
        
        return $rendered;
    }

    /**
     * @inheritDoc
     */
    public function activeAttributes($callback = null)
    {
        if (is_callable($callback)) {
            return call_user_func($callback, $this->activeAttributes);
        }

        return $this->activeAttributes;
    }

    /**
     * @inheritDoc
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @inheritDoc
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

    /**
     * Get view for render
     * 
     * @param string $view
     * @return string
     */
    protected function getRenderView($view = null)
    {
        $renderView = $this->config['view'];
        
        if (!empty($this->view)) {
            $renderView = $this->view;
        }
        
        if (!empty($view) && $this->viewFactory->exists($view)) {
            $renderView = $view;
        }
        
        return $renderView;
    }
}