<?php
namespace Malezha\Menu\Traits;

/**
 * Class DisplayRule
 * @package Malezha\Menu\Traits
 */
trait DisplayRule
{
    /**
     * @var bool|callable
     */
    protected $displayRule = true;

    /**
     * Set boolean or callback, witch return boolean to determine whether to display or not this item.
     * Callback will be called each time rendering item.
     *
     * @param bool|callable $rule
     */
    public function setDisplayRule($rule)
    {
        $this->displayRule = $rule;
    }

    /**
     * The element should be rendered?
     *
     * @return bool
     */
    public function canDisplay()
    {
        if (is_callable($this->displayRule)) {
            return (bool) call_user_func($this->displayRule);
        }
        
        return (bool) $this->displayRule;
    }
}