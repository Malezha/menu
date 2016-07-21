<?php
namespace Malezha\Menu\Element;

use Illuminate\Container\Container;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\ComparativeUrl;
use Malezha\Menu\Contracts\DisplayRule as DisplayRuleInterface;
use Malezha\Menu\Contracts\HasAttributes as HasAttributesInterface;
use Malezha\Menu\Contracts\HasActiveAttributes as HasActiveAttributesInterface;
use Malezha\Menu\Contracts\MenuRender;
use Malezha\Menu\Support\MergeAttributes;
use Malezha\Menu\Traits\DisplayRule;
use Malezha\Menu\Traits\HasActiveAttributes;
use Malezha\Menu\Traits\HasAttributes;

/**
 * Class Link
 * @package Malezha\Menu\Element
 *
 * @property string $title
 * @property string $url
 * @property-read Attributes $attributes
 * @property-read Attributes $linkAttributes
 * @property-read Attributes $activeAttributes
 * @property bool|callable $displayRule
 */
class Link extends AbstractElement implements DisplayRuleInterface, HasAttributesInterface, HasActiveAttributesInterface
{
    use DisplayRule, HasAttributes, HasActiveAttributes;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var Attributes
     */
    protected $linkAttributes;

    /**
     * @var ComparativeUrl
     */
    protected $comparativeUrl;

    /**
     * Link constructor.
     * @param string $title
     * @param string $url
     * @param Attributes $attributes
     * @param Attributes $activeAttributes
     * @param Attributes $linkAttributes
     * @param string $view
     * @param ComparativeUrl $comparativeUrl
     * @param MenuRender $render
     */
    public function __construct($title, $url, Attributes $attributes, Attributes $activeAttributes, 
                                Attributes $linkAttributes, $view, ComparativeUrl $comparativeUrl, MenuRender $render)
    {
        $this->title = $title;
        $this->url = $url;
        $this->attributes = $attributes;
        $this->activeAttributes = $activeAttributes;
        $this->linkAttributes = $linkAttributes;
        $this->view = $view;
        $this->comparativeUrl = $comparativeUrl;
        $this->render = $render;
    }

    /**
     * @inheritdoc
     */
    public function render($view = null)
    {
        return $this->render->make($this->view)
            ->with($this->renderWith())
            ->render();
    }

    /**
     * @inheritDoc
     */
    public function buildAttributes($attributes = [])
    {
        $attributes = $this->comparativeUrl->isEquals($this->url) ?
            (new MergeAttributes($this->activeAttributes->all(), $attributes))->merge() : $attributes;

        return $this->attributes->build($attributes);
    }

    /**
     * @return array
     */
    protected function renderWith()
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'attributes' => $this->buildAttributes(),
            'linkAttributes' => $this->linkAttributes->build(),
            'canDisplay' => $this->canDisplay(),
            'renderView' => null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'title' => $this->title,
            'url' => $this->url,
            'attributes' => $this->attributes->toArray(),
            'activeAttributes' => $this->activeAttributes->toArray(),
            'linkAttributes' => $this->linkAttributes->toArray(),
            'displayRule' => $this->canDisplay(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->comparativeUrl = Container::getInstance()->make(ComparativeUrl::class);

        parent::unserialize($serialized);
    }

    /**
     * @return array
     */
    protected function propertiesForSerialization()
    {
        return array_merge(parent::propertiesForSerialization(), [
            'title' => $this->title,
            'url' => $this->url,
            'attributes' => $this->attributes,
            'activeAttributes' => $this->activeAttributes,
            'linkAttributes' => $this->linkAttributes,
            'displayRule' => $this->serializeRule(),
        ]);
    }
}