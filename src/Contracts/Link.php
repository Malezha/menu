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
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     */
    public function setUrl($url);
}