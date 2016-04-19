<?php

namespace Malezha\Menu\Tests;

use Illuminate\Support\Collection;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Entity\Attributes;
use Malezha\Menu\Entity\Item;

class BuilderTest extends TestCase
{
    /**
     * @return Builder
     */
    protected function builderFactory()
    {
        return $this->app->make(Builder::class, ['name' => 'test', 'activeAttributes' => ['class' => 'active']]);
    }
    
    public function testConstructor()
    {
        $builder = $this->builderFactory();

        $this->assertAttributeEquals($this->app, 'container', $builder);
        $this->assertAttributeEquals('test', 'name', $builder);
        $this->assertAttributeEquals(Builder::UL, 'type', $builder);
        $this->assertAttributeInstanceOf(Attributes::class, 'attributes', $builder);
        $this->assertAttributeInstanceOf(Collection::class, 'items', $builder);
        $this->assertAttributeInstanceOf(Attributes::class, 'activeAttributes', $builder);
    }

    public function testAdd()
    {
        $builder = $this->builderFactory();

        $item = $builder->add('index', 'Index', url('/'), ['class' => 'main-menu'], ['class' => 'link'],
            function (Item $item) {
                $this->assertAttributeEquals('Index', 'title', $item->getLink());
                $item->getLink()->setTitle('Home');
            });

        $this->assertAttributeEquals($builder, 'builder', $item);
        $this->assertAttributeInstanceOf(Attributes::class, 'attributes', $item);

        $link = $item->getLink();
        $this->assertAttributeEquals('Home', 'title', $link);
        $this->assertAttributeEquals(url('/'), 'url', $link);
    }
    
    public function testGet()
    {
        $builder = $this->builderFactory();
        
        $item = $builder->add('test', 'Test', '/test');
        
        $this->assertEquals($item, $builder->get('test'));
    }
    
    public function testHas()
    {
        $builder = $this->builderFactory();

        $this->assertFalse($builder->has('test'));
    }
    
    public function testType()
    {
        $builder = $this->builderFactory();
        
        $this->assertEquals(Builder::UL, $builder->getType());
        $builder->setType(Builder::OL);
        $this->assertAttributeEquals(Builder::OL, 'type', $builder);
    }
    
    public function testAll()
    {
        $builder = $this->builderFactory();
        
        $this->assertEquals([], $builder->all());
        $item = $builder->add('test', 'Test', '/test');
        $this->assertEquals(['test' => $item], $builder->all());
    }
    
    public function testForget()
    {
        $builder = $this->builderFactory();

        $builder->add('test', 'Test', '/test');
        $this->assertTrue($builder->has('test'));
        $builder->forget('test');
        $this->assertFalse($builder->has('test'));
    }
    
    public function testActiveAttributes()
    {
        $builder = $this->builderFactory();
        $activeAttributes = $builder->activeAttributes();
        
        $this->assertInstanceOf(Attributes::class, $activeAttributes);

        $result = $builder->activeAttributes(function ($attributes) {
            $this->assertInstanceOf(Attributes::class, $attributes);
            
            return $attributes->get('class');
        });
        
        $this->assertEquals('active', $result);
    }
    
    public function testGroup()
    {
        $builder = $this->builderFactory();
        
        $group = $builder->group('test', function (Item $item) use ($builder) {
            $this->assertAttributeEquals($builder, 'builder', $item);
        }, function (Builder $menu) use ($builder) {
            $this->assertEquals($builder->activeAttributes()->all(), $menu->activeAttributes()->all());
        });

        $this->assertEquals($group, $builder->get('test'));
    }

    public function testRender()
    {
        $builder = $this->builderFactory();

        $index = $builder->add('index', 'Index Page', '/');
        $index->getLink()->getAttributes()->push(['class' => 'menu-link']);

        $builder->group('orders', function ($item) {
            $item->getAttributes()->push(['class' => 'child-menu']);
            
            $link = $item->getLink();
            $link->setTitle('Orders');
            $link->setUrl('javascript:;');

        }, function ($menu) {
            $menu->add('all', 'All', '/orders/all');
            $menu->add('type_1', 'Type 1', '/orders/1', [], ['class' => 'text-color-red']);

            $menu->add('type_2', 'Type 2', '/orders/2', [], [], function ($item) {
                $item->getLink()->getAttributes()->push(['data-attribute' => 'value']);
            });
        });
        
        $html = $builder->render();
        $file = file_get_contents(__DIR__ . '/stub/menu.html');
        
        $this->assertEquals($file, $html);
    }
}