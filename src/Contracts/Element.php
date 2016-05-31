<?php
namespace Malezha\Menu\Contracts;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface Element
 * @package Malezha\Menu\Contracts
 */
interface Element extends Arrayable, \Serializable
{
    public function getView();
    
    public function setView($view);
    
    public function render($view = null);
}