<?php

namespace Malezha\Menu;

class Link
{
    use HasAttributes;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $title;


    function __construct($title = '', $url = '#', $attributes = [])
    {
        $this->title = $title;
        $this->url = $url;
        $this->attributes = new Attributes($attributes);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \Malezha\Menu\Link
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     * @return \Malezha\Menu\Link
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}