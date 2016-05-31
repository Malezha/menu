<?php
namespace Malezha\Menu\Element;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\DisplayRule as DisplayRuleInterface;
use Malezha\Menu\Contracts\HasAttributes as HasAttributesInterface;
use Malezha\Menu\Contracts\MenuRender;
use Malezha\Menu\Traits\DisplayRule;
use Malezha\Menu\Traits\HasAttributes;

/**
 * Class Text
 * @package Malezha\Menu\Element
 * 
 * @property string $text
 * @property 
 * @property-read Attributes $attributes
 */
class Text extends AbstractElement implements DisplayRuleInterface, HasAttributesInterface
{
    use DisplayRule, HasAttributes;
    
    /**
     * @var string
     */
    protected $text;

    /**
     * Text constructor.
     * @param $text
     * @param Attributes $attributes
     * @param $view
     * @param MenuRender $render
     */
    public function __construct($text, Attributes $attributes, $view, MenuRender $render)
    {
        $this->text = $text;
        $this->render = $render;
        $this->view = $view;
        $this->attributes = $attributes;
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
     * @return array
     */
    protected function renderWith()
    {
        return [
            'text' => $this->text,
            'canDisplay' => $this->canDisplay(),
            'attributes' => $this->buildAttributes(),
        ];
    }

    protected function propertiesForSerialization()
    {
        return array_merge(parent::propertiesForSerialization(), [
            'text' => $this->text,
            'attributes' => $this->attributes,
            'rule' => $this->serializeRule(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'text' => $this->text,
            'attributes' => $this->attributes->toArray(),
            'canDisplay' => $this->canDisplay(),
        ]);
    }
}