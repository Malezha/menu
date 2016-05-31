<?php
namespace Malezha\Menu\Support;

use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Support\MergeAttributes;

/**
 * Class Attributes
 * @package Malezha\Menu
 */
class Attributes implements AttributesContract
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @inheritDoc
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @inheritDoc
     */
    public function get($name, $default = null)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    public function set(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function has($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * @inheritDoc
     */
    public function forget($name)
    {
        if ($this->has($name)) {
            unset($this->attributes[$name]);
        }
    }

    /**
     * @inheritDoc
     */
    public function push(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function put($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function merge(array $attributes)
    {
        $this->set((new MergeAttributes($this->all(), $attributes))->merge());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build($attributes = [])
    {
        $attributes = (new MergeAttributes($this->all(), $attributes))->merge();
        ksort($attributes);

        $html = (count($attributes) > 0) ? ' ' : '';

        foreach ($attributes as $key => $value) {
            $html .= $key . '="' . $value . '" ';
        }

        return rtrim($html);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $array = $this->all();
        ksort($array);
        
        return $array;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->put($offset, $value);
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
    public function serialize()
    {
        return serialize($this->attributes);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->attributes = unserialize($serialized);
    }
}