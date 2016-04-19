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
}