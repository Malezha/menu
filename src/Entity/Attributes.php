<?php
namespace Malezha\Menu\Entity;

use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Support\MergeAttributes;

/**
 * Class Attributes
 * @package Malezha\Menu\Entity
 */
class Attributes implements AttributesContract
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get attribute by name
     *
     * @param string $name
     * @param string|null $default
     * @return string|null
     */
    public function get($name, $default = null)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        return $default;
    }

    /**
     * Set array attributes
     *
     * @param array $attributes
     * @return AttributesContract
     */
    public function set(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get all attributes
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Check exits attribute by name
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Delete attribute by name
     *
     * @param string $name
     */
    public function forget($name)
    {
        if ($this->has($name)) {
            unset($this->attributes[$name]);
        }
    }

    /**
     * Set attribute or attributes.
     * No merge attributes value.
     *
     * @param array $attributes
     * @return AttributesContract
     */
    public function push(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * Set attribute value
     *
     * @param string $name
     * @param string $value
     * @return AttributesContract
     */
    public function put($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Merge attributes and merge their values
     *
     * @param array $attributes
     * @return AttributesContract
     */
    public function merge(array $attributes)
    {
        $this->set((new MergeAttributes($this->all(), $attributes))->merge());

        return $this;
    }

    /**
     * Build attributes html valid string
     *
     * @param array $attributes
     * @return string
     */
    public function build($attributes = [])
    {
        $attributes = (new MergeAttributes($this->all(), $attributes))->merge();

        $html = (count($attributes) > 0) ? ' ' : '';

        foreach ($attributes as $key => $value) {
            $html .= $key . '="' . $value . '" ';
        }

        return rtrim($html);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }
}