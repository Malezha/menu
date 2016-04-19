<?php

namespace Malezha\Menu\Entity;

use Illuminate\Support\Collection;

class Attributes
{
    protected $list;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->list = new Collection($attributes);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->list->get($name, $default);
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function set(array $attributes)
    {
        $this->list = new Collection($attributes);

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->list->all();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->list->has($name);
    }

    /**
     * @param string $name
     */
    public function forget($name)
    {
        $this->list->forget($name);
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function push(array $attributes)
    {
        $this->list = $this->list->merge($attributes);

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function put($name, $value)
    {
        $this->list->put($name, $value);

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function merge(array $attributes)
    {
        $this->set(self::mergeArrayValues($this->list->toArray(), $attributes));

        return $this;
    }

    /**
     * @return array|bool
     */
    public static function mergeArrayValues()
    {
        if (func_num_args() <= 1) {
            throw new \RuntimeException("Must has min two parameters.");
        }

        $arrays = func_get_args();
        $keys = [];

        foreach ($arrays as $array) {
            $keys = array_merge($keys, array_keys($array));
        }

        $keys = array_unique($keys);

        $merged = array_fill_keys($keys, null);

        foreach ($arrays as $array) {
            foreach ($keys as $key) {
                if (array_key_exists($key, $array)) {
                    $merged[$key] = self::mergeValues($merged[$key], $array[$key]);
                }
            }
        }

        return $merged;
    }

    /**
     * @param string $valueOne
     * @param string $valueTwo
     * @return string
     */
    protected static function mergeValues($valueOne, $valueTwo)
    {
        if (is_null($valueOne)) {
            return $valueTwo;
        }
        
        $valueOne = explode(' ', $valueOne);
        $valueTwo = explode(' ', $valueTwo);

        $merged = array_merge($valueOne, $valueTwo);

        return trim(implode(' ', $merged));
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function build($attributes = [])
    {
        $attributes = $this->mergeArrayValues($this->all(), $attributes);

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