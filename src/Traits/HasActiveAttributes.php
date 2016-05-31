<?php
namespace Malezha\Menu\Traits;

use Malezha\Menu\Contracts\Attributes;

/**
 * Class HasActiveAttributes
 * @package Malezha\Menu\Traits
 */
trait HasActiveAttributes
{
    /**
     * @var Attributes
     */
    protected $activeAttributes;

    /**
     * Get attributes object.
     * If send \Closure option as parameter then returned callback result.
     *
     * @param callable|null $callback
     * @return Attributes|mixed
     */
    public function getActiveAttributes($callback = null)
    {
        if (is_callable($callback)) {
            return call_user_func($callback, $this->activeAttributes);
        }

        return $this->activeAttributes;
    }
}