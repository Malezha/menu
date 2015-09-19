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
     * @param null|string $title
     * @return \Malezha\Menu\Link|string
     */
    public function title($title = null)
    {
        if (!empty($title)) {
            $this->title = (string) $title;

            return $this;
        }

        return $this->title;
    }

    /**
     * @param null|string $url
     * @return \Malezha\Menu\Link|string
     */
    public function url($url = null)
    {
        if (!empty($url)) {
            $this->url = (string) $url;

            return $this;
        }

        return $this->url;
    }
}