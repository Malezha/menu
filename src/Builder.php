<?php
namespace Malezha\Menu;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Contracts\Builder as BuilderContract;
use Malezha\Menu\Contracts\Element;
use Malezha\Menu\Contracts\ElementFactory;
use Malezha\Menu\Contracts\HasActiveAttributes;
use Malezha\Menu\Contracts\MenuRender;
use Malezha\Menu\Element\SubMenu;
use Malezha\Menu\Traits\HasActiveAttributes as TraitHasActiveAttributes;
use Malezha\Menu\Traits\HasAttributes;

/**
 * Class Builder
 * @package Malezha\Menu
 */
class Builder implements BuilderContract
{
    use HasAttributes, TraitHasActiveAttributes;

    /**
     * @var Container
     */
    protected $app;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var array
     */
    protected $indexes = [];

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
        $this->app = $container;
        $this->name = $name;
        $this->type = $type;
        $this->attributes = $attributes;
        $this->items = [];
        $this->activeAttributes = $activeAttributes;
        $this->viewFactory = $this->app->make(MenuRender::class);
        $this->config = $this->app->make(Repository::class)->get('menu');
        try {
            $this->setView($view);
        } catch (\Exception $e) {}
    }

    /**
     * @inheritDoc
     */
    public function create($name, $type, $callback = null)
    {
        if ($this->has($name)) {
            throw new \RuntimeException("Duplicate menu key \"${name}\"");
        }

        $factory = $this->getFactory($type);
        $result = null;

        $reflection = new \ReflectionClass($type);
        if ($reflection->implementsInterface(HasActiveAttributes::class)) {
            $factory->setActiveAttributes($this->activeAttributes);
        }
        
        if (is_callable($callback)) {
            $result = call_user_func($callback, $factory);
            
            if (empty($result)) {
                $result = $factory;
            }
        }
        
        if ($result instanceof ElementFactory) {
            $result = $result->build();
        }
        
        $this->saveItem($name, $result);
        
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function insertBefore($name, \Closure $callback)
    {
        $this->insert($this->indexes[$name], $this->prepareInsert($name, $callback));
    }

    /**
     * @inheritDoc
     */
    public function insertAfter($name, \Closure $callback)
    {
        $this->insert($this->indexes[$name] + 1, $this->prepareInsert($name, $callback));
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
    public function getByIndex($index, $default = null)
    {
        $key = array_search($index, $this->indexes);
        
        return $key === false ? $default : $this->get($key, $default);
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

    /**
     * @param string $name
     * @param Element $item
     */
    protected function saveItem($name, $item)
    {
        $this->items[$name] = $item;
        $this->indexes[$name] = count($this->items) - 1;
    }

    /**
     * @param string $name
     * @param array $attributes
     * @param array $activeAttributes
     * @param \Closure|null $callback
     * @return BuilderContract
     */
    protected function builderFactory($name, $attributes = [], $activeAttributes = [], $callback = null)
    {
        $menu = $this->app->make(BuilderContract::class, [
            'container' => $this->app,
            'name' => $name,
            'activeAttributes' => $this->app->make(AttributesContract::class, ['attributes' => $activeAttributes]),
            'attributes' => $this->app->make(AttributesContract::class, ['attributes' => $attributes]),
            'view' => $this->getView(),
        ]);

        if (is_callable($callback)) {
            call_user_func($callback, $menu);
        }

        return $menu;
    }

    protected function rebuildIndexesArray()
    {
        $this->indexes = [];
        $iterator = 0;

        foreach ($this->items as $key => $value) {
            $this->indexes[$key] = $iterator++;
        }
    }

    /**
     * @param string $name
     * @param \Closure $callback
     * @return array
     */
    protected function prepareInsert($name, $callback)
    {
        if (!$this->has($name)) {
            throw new \RuntimeException("Menu item \"${name}\" must be exists");
        }

        $forInsert = $this->builderFactory('tmp', [], [], $callback)->all();
        $diff = array_diff(array_keys(array_diff_key($this->items, $forInsert)), array_keys($this->items));

        if (count($diff) > 0) {
            throw new \RuntimeException('Duplicated keys: ' . implode(', ', array_keys($diff)));
        }
        
        return $forInsert;
    }

    /**
     * @param int $position
     * @param array $values
     */
    protected function insert($position, $values)
    {
        $firstArray = array_splice($this->items, 0, $position);
        $this->items = array_merge($firstArray, $values, $this->items);
        $this->rebuildIndexesArray();
    }

    /**
     * @param $element
     * @return ElementFactory
     */
    protected function getFactory($element)
    {
        $factoryClass = $this->app->make(Repository::class)->get('menu.elements')[$element]['factory'];
        
        return $this->app->make($factoryClass);
    }
}