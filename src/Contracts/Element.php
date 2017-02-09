<?php
namespace Malezha\Menu\Contracts;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface Element
 * @package Malezha\Menu\Contracts
 */
interface Element extends Arrayable
{
    /**
     * Get element view
     *
     * @return string
     */
    public function getView();

    /**
     * Set element for view
     *
     * @param string $view
     * @return void
     */
    public function setView($view);

    /**
     * Render element to string
     *
     * @param string $view
     * @return string
     */
    public function render($view = null);
}