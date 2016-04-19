<?php

namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Entity\Item;
use Malezha\Menu\Entity\Link;

class ItemTest extends TestCase
{
    protected function itemFactory()
    {
        $builder = $this->app->make(Builder::class, ['name' => 'test']);
        
        return new Item($builder, 'index', [], 'Index', '/index', ['class' => 'link']);
    }

    public function testLink()
    {
        $item = $this->itemFactory();

        $this->assertInstanceOf(Link::class, $item->getLink());
    }
    
    public function testBuildAttributes()
    {
        $item = $this->itemFactory();
        
        $this->assertInternalType('string', $item->buildAttributes());
    }
    
    public function testIsUrlEqual()
    {
        $item = $this->itemFactory();
        
        $first = url('/index');
        $second = url('/');
        
        $this->assertTrue($item->isUrlEqual($first, $second));
    }
}