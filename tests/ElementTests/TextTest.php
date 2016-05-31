<?php
namespace Malezha\Menu\Tests\ElementTests;

use Malezha\Menu\Factory\TextFactory;
use Malezha\Menu\Tests\TestCase;

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
}