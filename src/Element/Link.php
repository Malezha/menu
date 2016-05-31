<?php
namespace Malezha\Menu\Element;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\DisplayRule as DisplayRuleInterface;
use Malezha\Menu\Contracts\HasAttributes as HasAttributesInterface;
use Malezha\Menu\Contracts\HasActiveAttributes as HasActiveAttributesInterface;
use Malezha\Menu\Contracts\MenuRender;
use Malezha\Menu\Support\MergeAttributes;
use Malezha\Menu\Traits\DisplayRule;
use Malezha\Menu\Traits\HasActiveAttributes;
use Malezha\Menu\Traits\HasAttributes;
use Opis\Closure\SerializableClosure;

/**
 * Class Link
 * @package Malezha\Menu\Element
 *
 * @property string $title
 * @property string $url
 * @property-read Attributes $attributes
 * @property-read Attributes $linkAttributes
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
     * @var string
     */
    protected $currentUrl;

    /**
     * Link constructor.
     * @param string $title
     * @param string $url
     * @param Attributes $attributes
     * @param Attributes $activeAttributes
     * @param Attributes $linkAttributes
     * @param string $view
     * @param string $currentUrl
     * @param MenuRender $render
     */
    public function __construct($title, $url, Attributes $attributes, Attributes $activeAttributes, 
                                Attributes $linkAttributes, $view, $currentUrl, MenuRender $render)
    {
        $this->title = $title;
        $this->url = $url;
        $this->attributes = $attributes;
        $this->activeAttributes = $activeAttributes;
        $this->linkAttributes = $linkAttributes;
        $this->view = $view;
        $this->currentUrl = $currentUrl;
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
        $attributes = $this->isUrlEqual($this->url, $this->currentUrl) ?
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
     * Check is two url equal
     *
     * @param string $first
     * @param string $second
     * @return bool
     */
    protected function isUrlEqual($first, $second)
    {
        $uriForTrim = [
            '#',
            '/index',
            '/',
        ];

        foreach ($uriForTrim as $trim) {
            $first = rtrim($first, $trim);
            $second = rtrim($second, $trim);
        }

        return $first == $second;
    }
    
    protected function wakeupCurrentUrl()
    {
        $app = Container::getInstance();
        $this->currentUrl = $app->make(Request::class)->url();
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
            'canDisplay' => $this->canDisplay(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->wakeupCurrentUrl();

        parent::unserialize($serialized);
    }

    protected function propertiesForSerialization()
    {
        return array_merge(parent::propertiesForSerialization(), [
            'title' => $this->title,
            'url' => $this->url,
            'attributes' => $this->attributes,
            'activeAttributes' => $this->activeAttributes,
            'linkAttributes' => $this->linkAttributes,
            'rule' => $this->serializeRule(),
        ]);
    }
}