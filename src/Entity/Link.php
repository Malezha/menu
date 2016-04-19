<?php

namespace Malezha\Menu\Entity;

use Malezha\Menu\Traits\HasAttributes;

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


    public function __construct($title = '', $url = '#', $attributes = [])
    {
        $this->setTitle($title);
        $this->setUrl($url);
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
     */
    public function setTitle($title)
    {
        if (!empty($title)) {
            $this->title = (string) $title;
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        if (!empty($url)) {
            if ($url != '#') {
                $url = url($url);
            }

            $this->url = (string) $url;
        }
    }
}