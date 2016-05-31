<?php
namespace Malezha\Menu\Contracts;

/**
 * Interface HasBuilder
 * @package Malezha\Menu\Contracts
 */
interface HasBuilder
{
    /**
     * Get builder
     * 
     * @return Builder
     */
    public function getBuilder();
}