<?php
namespace Malezha\Menu\Traits;
use Opis\Closure\SerializableClosure;

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
            return (bool) call_user_func($this->rule);
        }
        
        return (bool) $this->rule;
    }

    /**
     * Serialize rule
     * 
     * @return string
     */
    protected function serializeRule()
    {
        $displayRule = $this->rule;

        if ($this->rule instanceof \Closure) {
            $displayRule = new SerializableClosure($this->rule);
        }
        
        return serialize($displayRule);
    }

    /**
     * @param string $rule
     */
    protected function unserializeRule($rule)
    {
        $rule = unserialize($rule);

        if ($rule instanceof SerializableClosure) {
            $rule = $rule->getClosure();
        }

        $this->rule = $rule;
    }
}