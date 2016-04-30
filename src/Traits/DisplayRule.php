<?php
namespace Malezha\Menu\Traits;

/**
 * Class DisplayRule
 * @package Malezha\Menu\Traits
 */
trait DisplayRule
{
    /**
     * @var bool|\Closure
     */
    protected $rule = true;

    /**
     * Set boolean or callback, witch return boolean to determine whether to display or not this item.
     * Callback will be called each time rendering item.
     *
     * @param bool|\Closure $rule
     */
    public function setDisplayRule($rule)
    {
        $this->rule = $rule;
    }

    /**
     * The element should be rendered?
     *
     * @return bool
     */
    public function canDisplay()
    {
        if (is_callable($this->rule)) {
            return (bool)call_user_func($this->rule);
        }
        
        return (bool)$this->rule;
    }
}