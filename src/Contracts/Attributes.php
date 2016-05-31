<?php
namespace Malezha\Menu\Contracts;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface Attributes
 * @package Malezha\Menu\Contracts
 */
interface Attributes extends Arrayable, \ArrayAccess, \Serializable
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes);

    /**
     * Get attribute by name
     *
     * @param string $name
     * @param string|null $default
     * @return string|null
     */
    public function get($name, $default = null);

    /**
     * Set array attributes
     *
     * @param array $attributes
     * @return Attributes
     */
    public function set(array $attributes);

    /**
     * Get all attributes
     *
     * @return array
     */
    public function all();

    /**
     * Check exits attribute by name
     *
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * Delete attribute by name
     *
     * @param string $name
     */
    public function forget($name);

    /**
     * Set attribute or attributes.
     * No merge attributes value.
     *
     * @param array $attributes
     * @return Attributes
     */
    public function push(array $attributes);

    /**
     * Set attribute value
     *
     * @param string $name
     * @param string $value
     * @return Attributes
     */
    public function put($name, $value);

    /**
     * Merge attributes and merge their values
     *
     * @param array $attributes
     * @return Attributes
     */
    public function merge(array $attributes);

    /**
     * Build attributes html valid string
     *
     * @param array $attributes
     * @return string
     */
    public function build($attributes = []);

    /**
     * @return string
     */
    public function __toString();
}