<?php
namespace Malezha\Menu\Contracts;

/**
 * Interface Element
 * @package Malezha\Menu\Contracts
 */
interface Element
{
    public function getView();
    
    public function setView($view);
    
    public function render($view = null);
}