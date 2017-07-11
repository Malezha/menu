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
use Malezha\Menu\Support\FromArrayBuilder;
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
    protected $elements;

    /**
     * @var array
     */
    protected $indexes = [];

    /**
     * @var string
     */
    protected $type;

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
    public function __construct(Container $container, AttributesContract $attributes, 
                                AttributesContract $activeAttributes, 
                                $type = self::UL, $view = null)
    {
        $this->app = $container;
        $this->type = $type;
        $this->attributes = $attributes;
        $this->elements = [];
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
    public function create($name, $type, callable $callback = null)
    {
        if ($this->has($name)) {
            throw new \RuntimeException("Duplicate menu key \"${name}\"");
        }

        $factory = $this->getFactory($type);

        if ($this->hasActiveAttributes($type)) {
            $factory->activeAttributes = clone $this->getActiveAttributes();
        }

        $result = call_if_callable($callback, $factory);

        if (! $result instanceof Element) {
            throw new \RuntimeException("Result of callback must be [" . Element::class . "]");
        }

        $this->saveItem($name, $result);
        
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function insertBefore($name, callable $callback)
    {
        $prepared = $this->prepareInsert($name, $callback);
        $this->insert($this->indexes[$name], $prepared);
    }

    /**
     * @inheritDoc
     */
    public function insertAfter($name, callable $callback)
    {
        $prepared = $this->prepareInsert($name, $callback);
        $this->insert($this->indexes[$name] + 1, $prepared);
    }

    /**
     * @inheritDoc
     */
    public function has($name)
    {
        return array_key_exists($name, $this->elements);
    }

    /**
     * @inheritDoc
     */
    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            return $this->elements[$name];
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
        return $this->elements;
    }

    /**
     * @inheritDoc
     */
    public function forget($name)
    {
        if ($this->has($name)) {
            unset($this->elements[$name]);
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
            '/(\s)+/s'     // shorten multiple whitespace sequences
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
        $this->elements[$name] = $item;
        $this->indexes[$name] = count($this->elements) - 1;
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
        $menu = $this->app->makeWith(BuilderContract::class, [
            'container' => $this->app,
            'name' => $name,
            'activeAttributes' => $this->app->makeWith(AttributesContract::class,
                ['attributes' => $activeAttributes]),
            'attributes' => $this->app->makeWith(AttributesContract::class,
                ['attributes' => $attributes]),
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

        foreach ($this->elements as $key => $value) {
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
        $diff = array_intersect_key($this->elements, $forInsert);

        if (count($diff) > 0) {
            throw new \RuntimeException('Duplicated keys: ' . implode(', ', array_keys($diff)));
        }

//        foreach ($forInsert as &$item) {
//            if ($item instanceof ElementFactory) {
//                $item = $item->build();
//            }
//        }
        
        return $forInsert;
    }

    /**
     * @param int $position
     * @param array $values
     */
    protected function insert($position, $values)
    {
        $firstArray = array_splice($this->elements, 0, $position);
        $this->elements = array_merge($firstArray, $values, $this->elements);
        $this->rebuildIndexesArray();
    }

    /**
     * @param $element
     * @return ElementFactory
     * @throws \RuntimeException
     */
    protected function getFactory($element)
    {
        if (!array_key_exists($element, $this->config['elements'])) {
            throw new \RuntimeException('Not found factory for element:' . $element);
        }

        $factoryClass = $this->config['elements'][$element]['factory'];
        
        return $this->app->make($factoryClass);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $this->view = $this->getRenderView($this->view);
        $elements = [];
        
        foreach ($this->elements as $key => $element) {
//            if ($element instanceof ElementFactory) {
//                $element = $element->build();
//            }

            $elements[$key] = $element->toArray();
            $elements[$key]['type'] = array_search(get_class($element), $this->config['alias']);
        }
        
        return [
            'type' => $this->type,
            'view' => $this->view,
            'attributes' => $this->attributes->toArray(),
            'activeAttributes' => $this->activeAttributes->toArray(),
            'elements' => $elements,
        ];
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        if (is_int($offset)) {
            return (bool) array_search($offset, $this->indexes, true);
        }
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        if (is_int($offset)) {
            $offset = array_search($offset, $this->indexes, true);
            if ($offset === false) {
                return null;
            }
        }
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof Element) {
            $this->elements[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }

    /**
     * @inheritDoc
     */
    static public function fromArray(array $builder)
    {
        return FromArrayBuilder::getInstance()->build($builder);
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function hasActiveAttributes($type)
    {
        $reflection = new \ReflectionClass($type);

        return $reflection->implementsInterface(HasActiveAttributes::class);
    }
}