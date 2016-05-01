<?php
namespace Malezha\Menu\Support;

class MergeAttributes
{
    /**
     * @var array
     */
    protected $arrays = [];

    /**
     * MergeAttributes constructor.
     */
    public function __construct()
    {
        $this->arrays = func_get_args();
    }
    
    /**
     * Merge array values as html attributes
     *
     * @return array
     */
    public function merge()
    {
        $arrays = $this->arrays;
        $keys = [];

        foreach ($arrays as $array) {
            $keys = array_merge($keys, array_keys($array));
        }

        $keys = array_unique($keys);

        $merged = array_fill_keys($keys, null);

        foreach ($arrays as $array) {
            foreach ($keys as $key) {
                if (array_key_exists($key, $array)) {
                    $merged[$key] = $this->mergeValues($merged[$key], $array[$key]);
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
    protected function mergeValues($valueOne, $valueTwo)
    {
        if (is_null($valueOne)) {
            return $valueTwo;
        }
        
        $valueOne = explode(' ', $valueOne);
        $valueTwo = explode(' ', $valueTwo);

        $merged = array_merge($valueOne, $valueTwo);

        return trim(implode(' ', $merged));
    }
}