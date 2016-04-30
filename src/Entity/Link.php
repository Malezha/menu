<?php
namespace Malezha\Menu\Entity;

use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Contracts\Link as LinkContract;
use Malezha\Menu\Traits\HasAttributes;

class Link implements LinkContract
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

    /**
     * Link constructor.
     * 
     * @param string $title
     * @param string $url
     * @param AttributesContract $attributes
     */
    public function __construct($title = '', $url = '#', AttributesContract $attributes)
    {
        $this->setTitle($title);
        $this->setUrl($url);
        $this->attributes = $attributes;
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
            $this->url = (string) $url;
        }
    }
}