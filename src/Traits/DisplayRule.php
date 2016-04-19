<?php

namespace Malezha\Menu\Traits;

trait DisplayRule
{
    /**
     * @var bool
     */
    protected $displayElement = true;

    /**
     * @param mixed $rule
     */
    public function setDisplayRule($rule)
    {
        if (is_callable($rule)) {
            $rule = call_user_func($rule);
        }

        $this->displayElement = (bool) $rule;
    }

    public function canDisplay()
    {
        return $this->displayElement;
    }
}