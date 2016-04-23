<?php
namespace Malezha\Menu\Entity;

use Malezha\Menu\Support\MergeAttributes;

class Attributes
{
    protected $list;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->list = $attributes;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (array_key_exists($name, $this->list)) {
            return $this->list[$name];
        }
        return $default;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function set(array $attributes)
    {
        $this->list = $attributes;

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->list;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->list);
    }

    /**
     * @param string $name
     */
    public function forget($name)
    {
        if ($this->has($name)) {
            unset($this->list[$name]);
        }
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function push(array $attributes)
    {
        $this->list = array_merge($this->list, $attributes);

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function put($name, $value)
    {
        $this->list[$name] = $value;

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function merge(array $attributes)
    {
        $this->set((new MergeAttributes($this->all(), $attributes))->merge());

        return $this;
    }

    /**
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