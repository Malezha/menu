<?php
namespace Malezha\Menu\Contracts;

/**
 * Interface DisplayRule
 * @package Malezha\Menu\Contracts
 */
interface DisplayRule
{
    /**
     * Set boolean or callback, witch return boolean to determine whether to display or not this item.
     * Callback will be called each time rendering item.
     *
     * @param bool|\Closure $rule
     */
    public function setDisplayRule($rule);

    /**
     * The element should be rendered?
     *
     * @return bool
     */
    public function canDisplay();
}