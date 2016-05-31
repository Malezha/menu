<?php
namespace Malezha\Menu\Element;

use Illuminate\Container\Container;
use Malezha\Menu\Contracts\DisplayRule;
use Malezha\Menu\Contracts\Element;
use Malezha\Menu\Contracts\MenuRender;
use Serafim\Properties\Properties;

/**
 * Class AbstractElement
 * @package Malezha\Menu\Element
 */
abstract class AbstractElement implements Element
{
    use Properties;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var MenuRender
     */
    protected $render;

    /**
     * @inheritdoc
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @inheritdoc
     */
    public function setView($view)
    {
        if ($this->render->exists($view)) {
            $this->view = $view;
        }
    }

    protected function propertiesForSerialization()
    {
        return [
            'view' => $this->view,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'view' => $this->view,
        ];
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize($this->propertiesForSerialization());
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        if ($this instanceof DisplayRule
            && method_exists($this, 'unserializeRule')
            && array_key_exists('rule', $data)
        ) {
            $this->unserializeRule($data['rule']);
            unset($data['rule']);
        }

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $app = Container::getInstance();
        $this->render = $app->make(MenuRender::class);
    }
}