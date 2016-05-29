<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\Item;

/**
 * Class BuilderTest
 * @package Malezha\Menu\Tests
 */
class BuilderTest extends TestCase
{
    /**
     * @return Builder
     */
    protected function builderFactory()
    {
        return $this->app->make(Builder::class, [
            'name' => 'test', 
            'activeAttributes' => $this->app->make(Attributes::class, ['attributes' => ['class' => 'active']]),
            'attributes' => $this->app->make(Attributes::class, ['attributes' => ['class' => 'menu']]),
        ]);
    }
    
    public function testConstructor()
    {
        $builder = $this->builderFactory();

        $this->assertAttributeEquals($this->app, 'app', $builder);
        $this->assertAttributeEquals('test', 'name', $builder);
        $this->assertAttributeEquals(Builder::UL, 'type', $builder);
        $this->assertAttributeInstanceOf(Attributes::class, 'attributes', $builder);
        $this->assertAttributeInternalType('array', 'items', $builder);
        $this->assertAttributeInstanceOf(Attributes::class, 'activeAttributes', $builder);
    }

    public function testCreate()
    {
        $builder = $this->builderFactory();

        $item = $builder->create('index', 'Index', '/', ['class' => 'main-menu'], ['class' => 'link'],
            function(Item $item) {
                $this->assertAttributeEquals('Index', 'title', $item->getLink());
                $item->getLink()->setTitle('Home');
            });

        $this->assertAttributeEquals($builder, 'builder', $item);
        $this->assertAttributeInstanceOf(Attributes::class, 'attributes', $item);

        $link = $item->getLink();
        $this->assertAttributeEquals('Home', 'title', $link);
        $this->assertAttributeEquals('/', 'url', $link);
    }

    public function testCreateIfExists()
    {
        $this->expectException(\RuntimeException::class);
        
        $builder = $this->builderFactory();

        $builder->create('index', 'Index', '/', ['class' => 'main-menu'], ['class' => 'link']);
        $builder->create('index', 'Index', '/', ['class' => 'main-menu'], ['class' => 'link']); // Duplicate
    }
    
    public function testGet()
    {
        $builder = $this->builderFactory();
        
        $item = $builder->create('test', 'Test', '/test');
        
        $this->assertEquals($item, $builder->get('test'));
        $this->assertEquals(null, $builder->get('notFound'));
    }

    public function testGetByIndex()
    {
        $builder = $this->builderFactory();

        $item = $builder->create('test', 'Test', '/test');

        $this->assertEquals($item, $builder->getByIndex(0));
        $this->assertEquals(null, $builder->getByIndex(1));
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
        $item = $builder->create('test', 'Test', '/test');
        $this->assertEquals(['test' => $item], $builder->all());
    }
    
    public function testForget()
    {
        $builder = $this->builderFactory();

        $builder->create('test', 'Test', '/test');
        $this->assertTrue($builder->has('test'));
        $builder->forget('test');
        $this->assertFalse($builder->has('test'));
    }
    
    public function testActiveAttributes()
    {
        $builder = $this->builderFactory();
        $activeAttributes = $builder->activeAttributes();
        
        $this->assertInstanceOf(Attributes::class, $activeAttributes);

        $result = $builder->activeAttributes(function(Attributes $attributes) {
            $this->assertInstanceOf(Attributes::class, $attributes);
            
            return $attributes->get('class');
        });
        
        $this->assertEquals('active', $result);
    }
    
    public function testSubMenu()
    {
        $builder = $this->builderFactory();
        
        $group = $builder->submenu('test', function(Item $item) use ($builder) {
            $this->assertAttributeEquals($builder, 'builder', $item);
        }, function(Builder $menu) use ($builder) {
            $this->assertEquals($builder->activeAttributes()->all(), $menu->activeAttributes()->all());
        });

        $this->assertEquals($group, $builder->get('test'));
    }

    public function testRender()
    {
        $builder = $this->builderFactory();

        $index = $builder->create('index', 'Index Page', url('/'));
        $index->getLink()->getAttributes()->push(['class' => 'menu-link']);

        $builder->submenu('orders', function(Item $item) {
            $item->getAttributes()->push(['class' => 'child-menu']);
            
            $link = $item->getLink();
            $link->setTitle('Orders');
            $link->setUrl('javascript:;');

        }, function(Builder $menu) {
            $menu->create('all', 'All', url('/orders/all'));
            $menu->create('type_1', 'Type 1', url('/orders/1'), [], ['class' => 'text-color-red']);

            $menu->create('type_2', 'Type 2', url('/orders/2'), [], [], function(Item $item) {
                $item->getLink()->getAttributes()->push(['data-attribute' => 'value']);
            });
        });
        
        $html = $builder->render();
        $file = file_get_contents(__DIR__ . '/stub/menu.html');
        
        $this->assertEquals($file, $html);
    }
    
    public function testDisplayRules()
    {
        $builder = $this->builderFactory();

        $builder->create('index', 'Index Page', url('/'));
        $builder->create('login', 'Login', url('/login'))->setDisplayRule(function() {
            return true;
        });
        $builder->create('admin', 'Admin', url('/admin'))->setDisplayRule(false);
        $builder->create('logout', 'Logout', url('/logout'))->setDisplayRule(null);

        $html = $builder->render();
        $file = file_get_contents(__DIR__ . '/stub/display_rules.html');

        $this->assertEquals($file, $html);
    }

    public function testAnotherViewRender()
    {
        view()->addLocation(__DIR__ . '/stub');
        $this->app['config']->prepend('menu.paths', __DIR__ . '/stub');
        
        $builder = $this->builderFactory();
        $builder->create('index', 'Index Page', url('/'));
        $builder->submenu('group', function(Item $item){}, function(Builder $menu) {
            $menu->create('one', 'One', url('/one'));
        });

        $html = $builder->render('another');
        $file = file_get_contents(__DIR__ . '/stub/another_menu.html');
        $this->assertEquals($file, $html);

        $builder->get('group')->getMenu()->setView('another');
        $html = $builder->render();
        $file = file_get_contents(__DIR__ . '/stub/another_sub_menu.html');
        $this->assertEquals($file, $html);

        $builder->setView('another');
        $builder->get('group')->getMenu()->setView('menu::view');
        $html = $builder->render();
        $file = file_get_contents(__DIR__ . '/stub/another_set_view_menu.html');
        $this->assertEquals($file, $html);
    }
}