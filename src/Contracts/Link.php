<?php
namespace Malezha\Menu\Contracts;

/**
 * Interface Link
 * @package Malezha\Menu\Contracts
 */
interface Link extends HasAttributes
{
    /**
     * Link constructor.
     *
     * @param string $title
     * @param string $url
     * @param Attributes $attributes
     */
    public function __construct($title = '', $url = '#', Attributes $attributes);

    /**
     * Get title text
     * 
     * @return string
     */
    public function getTitle();

    /**
     * Set title text
     * 
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Get URL
     * 
     * @return string
     */
    public function getUrl();

    /**
     * Set URL
     * 
     * @param string $url
     */
    public function setUrl($url);
}