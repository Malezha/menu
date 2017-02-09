<?php
namespace Malezha\Menu\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\ElementFactory;
use Malezha\Menu\Contracts\FromArrayBuilder as FromArrayBuilderContract;
use Malezha\Menu\Contracts\HasBuilder;

class FromArrayBuilder implements FromArrayBuilderContract
{
    /**
     * @var static
     */
    protected static $instance = null;

    /**
     * @var Container
     */
    private $container;

    /**
     * FromArrayBuilder constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            throw new \RuntimeException(static::class . ' must be set instance.');
        }

        return static::$instance;
    }

    /**
     * @param @inheritdoc
     */
    public static function setInstance($instance)
    {
        static::$instance = $instance;
    }

    /**
     * @param @inheritdoc
     */
    public function build(array $array)
    {
        $aliases = $this->container->make(Repository::class)->get('menu.alias');

        /** @var Builder $builder */
        $builder = $this->container->make(Builder::class, [
            'attributes' => $this->container->make(Attributes::class, ['attributes' => $array['attributes']]),
            'activeAttributes' => $this->container->make(Attributes::class, ['attributes' => $array['activeAttributes']]),
            'view' => $array['view'],
            'type' => $array['type'],
        ]);

        foreach ($array['elements'] as $key => $element) {
            $class = $aliases[$element['type']];

            $builder->create($key, $class, function (ElementFactory $factory) use ($class, $element) {
                // If element is submenu
                if ($this->isBuilder($class)) {
                    $element['builder'] = $this->build($element['builder']);
                }

                $attributes = preg_grep("/.*(attributes)/i", array_keys($element));

                foreach ($attributes as $key) {
                    $element[$key] = $this->container->make(Attributes::class, ['attributes' => $element[$key]]);
                }

                return $factory->build($element);
            });
        }

        return $builder;
    }

    /**
     * @param string $class
     * @return bool
     */
    private function isBuilder($class)
    {
        $reflection = new \ReflectionClass($class);

        return $reflection->implementsInterface(HasBuilder::class);
    }
}