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

    /**
     * Serialize rule
     * 
     * @return string
     */
    protected function serializeRule()
    {
        $displayRule = $this->displayRule;

        if ($this->displayRule instanceof \Closure) {
            $displayRule = new SerializableClosure($this->displayRule);
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

        $this->displayRule = $rule;
    }
}