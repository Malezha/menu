<?php

namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\Item;
use Malezha\Menu\Contracts\Link;

class ItemTest extends TestCase
{
    /**
     * @return Item
     */
    protected function itemFactory()
    {
        $builder = $this->app->make(Builder::class, [
            'name' => 'test',
            'activeAttributes' => ['class' => 'active'],
        ]);
        $link = $this->app->make(Link::class, [
            'title' => 'Index',
            'url' => '/index',
            'attributes' => $this->app->make(Attributes::class, ['attributes' => ['class' => 'link']]),
        ]);
        $item = $this->app->make(Item::class, [
            'builder' => $builder,
            'attributes' => $this->app->make(Attributes::class, ['attributes' => []]),
            'link' => $link,
            'request' => $this->app->make('request'),
        ]);
        
        return $item;
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
    
    public function testIsActiveUrl()
    {
        $item = $this->itemFactory();
        
        $this->assertEquals(' class="active"', $item->buildAttributes());
    }
}