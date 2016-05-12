<?php
namespace Malezha\Menu\Entity;

use Malezha\Menu\Contracts\Attributes as AttributesContract;
use Malezha\Menu\Contracts\Link as LinkContract;
use Malezha\Menu\Traits\HasAttributes;

/**
 * Class Link
 * @package Malezha\Menu\Entity
 */
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
     * @inheritDoc
     */
    public function __construct($title = '', $url = '#', AttributesContract $attributes)
    {
        $this->setTitle($title);
        $this->setUrl($url);
        $this->attributes = $attributes;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        if (!empty($title)) {
            $this->title = (string) $title;
        }
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        if (!empty($url)) {
            $this->url = (string) $url;
        }
    }
}