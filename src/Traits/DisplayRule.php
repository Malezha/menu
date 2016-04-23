<?php
namespace Malezha\Menu\Traits;

trait DisplayRule
{
    protected $rule = true;

    /**
     * @param mixed $rule
     */
    public function setDisplayRule($rule)
    {
        $this->rule = $rule;
    }

    /**
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