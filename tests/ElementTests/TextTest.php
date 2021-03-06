<?php
namespace Malezha\Menu\Tests\ElementTests;

use Malezha\Menu\Element\Text;
use Malezha\Menu\Factory\TextFactory;
use Malezha\Menu\Tests\TestCase;

/**
 * Class TextTest
 * @package Malezha\Menu\Tests\ElementTests
 */
class TextTest extends TestCase
{
    /**
     * @return TextFactory
     */
    protected function factory()
    {
        return new TextFactory($this->app);
    }

    protected function elementRender()
    {
        return "<li data-value=\"Element\">Element</li>\n";
    }
    
    protected function serializeStub()
    {
        return $this->getStub('serialized/text_element.txt');
    }
    
    protected function toArrayStub()
    {
        return [
            'view' => config('menu.elements')[Text::class]['view'],
            'text' => 'To array',
            'attributes' => [
                'class' => 'arrayable',
            ],
            'displayRule' => true
        ];
    }
    
    public function testFactory()
    {
        $factory = $this->factory();
        $factory->text = 'Some text';
        $factory->attributes->put('class', 'text-element');
        $text = $factory->build();

        $this->assertAttributeEquals('Some text', 'text', $text);
        $this->assertEquals('text-element', $text->attributes->get('class'));
    }

    public function testElement()
    {
        $factory = $this->factory();
        $element = $factory->build();

        $element->text = 'Element';
        $element->attributes->put('data-value', $element->text);
        
        $this->assertEquals('Element', $element->text);
        $this->assertEquals($element->text, $element->attributes->get('data-value'));
        $this->assertEquals($this->elementRender(), $element->render());
    }
    
    public function testToArray()
    {
        $factory = $this->factory();
        $factory->text = 'To array';
        $factory->attributes->put('class', 'arrayable');
        $element = $factory->build();
        
        $this->assertEquals($this->toArrayStub(), $element->toArray());
    }
    
    public function testView()
    {
        view()->addLocation(__DIR__ . '/../stub');
        $this->app['config']->prepend('menu.paths', __DIR__ . '/../stub');
        $view = 'text_element';

        $element = $this->factory()->build(['text' => 'Block']);
        $element->setView($view);
        $this->assertAttributeEquals($view, 'view', $element);
        $this->assertEquals($view, $element->getView());
        $this->assertEquals('<div>Block</div>', $element->render());
    }
}