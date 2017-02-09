<?php
namespace Malezha\Menu\Tests\ElementTests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Factory\LinkFactory;
use Malezha\Menu\Tests\TestCase;

/**
 * Class LinkTest
 * @package Malezha\Menu\Tests\ElementTests
 */
class LinkTest extends TestCase
{
    /**
     * @return LinkFactory
     */
    protected function factory()
    {
        return new LinkFactory($this->app);
    }
    
    protected function elementRender()
    {
        return "<li class=\"link\"><a href=\"/news\">News</a></li>\n";
    }

    protected function serializeStub()
    {
        return $this->getStub('serialized/link_element.txt');
    }
    
    public function testFactory()
    {
        $factory = $this->factory();
        $factory->title = 'Title';
        
        $this->assertEquals('Title', $factory->title);
        $this->assertInstanceOf(Attributes::class, $factory->attributes);
        
        $link = $factory->build(['title' => 'New title']);
        $this->assertEquals('New title', $link->title);
    }
    
    public function testElement()
    {
        $factory = $this->factory();
        $factory->title = 'News';
        $factory->url = '/news';
        $link = $factory->build();
        $link->attributes->put('class', 'link');
        
        $html = $link->render();
        $this->assertEquals($this->elementRender(), $html);
    }
}