<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Element\Link;
use Malezha\Menu\Element\SubMenu;
use Malezha\Menu\Factory\LinkFactory;
use Malezha\Menu\Factory\SubMenuFactory;

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

        /** @var Link $item */
        $item = $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Home';
            $factory->url = '/';
        });

        $this->assertAttributeEquals(['index' => $item], 'items', $builder);
        $this->assertAttributeEquals(['index' => 0], 'indexes', $builder);
        $this->assertInstanceOf(Link::class, $item);
        $this->assertAttributeEquals('Home', 'title', $item);
        $this->assertAttributeEquals('/', 'url', $item);
    }

    public function testCreateIfExists()
    {
        $this->expectException(\RuntimeException::class);
        
        $builder = $this->builderFactory();

        $builder->create('index', Link::class);
        $builder->create('index', SubMenu::class); // Duplicate
    }
    
    public function testGet()
    {
        $builder = $this->builderFactory();
        
        $item = $builder->create('test', Link::class);
        
        $this->assertEquals($item, $builder->get('test'));
        $this->assertEquals(null, $builder->get('notFound'));
    }

    public function testGetByIndex()
    {
        $builder = $this->builderFactory();

        $item = $builder->create('test', Link::class);

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
        $item = $builder->create('test', Link::class);
        $this->assertEquals(['test' => $item], $builder->all());
    }
    
    public function testForget()
    {
        $builder = $this->builderFactory();

        $builder->create('test', Link::class);
        $this->assertTrue($builder->has('test'));
        $builder->forget('test');
        $this->assertFalse($builder->has('test'));
    }
    
    public function testActiveAttributes()
    {
        $builder = $this->builderFactory();
        $activeAttributes = $builder->getActiveAttributes();
        
        $this->assertInstanceOf(Attributes::class, $activeAttributes);

        $result = $builder->getActiveAttributes(function(Attributes $attributes) {
            $this->assertInstanceOf(Attributes::class, $attributes);
            
            return $attributes->get('class');
        });
        
        $this->assertEquals('active', $result);
    }

    public function testRender()
    {
        $builder = $this->builderFactory();

        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index Page';
            $factory->url = url('/');
            $factory->linkAttributes->push(['class' => 'menu-link']);
        });
        
        $builder->create('orders', SubMenu::class, function(SubMenuFactory $factory) {
            $factory->attributes->push(['class' => 'child-menu']);
            $factory->title = 'Orders';
            $factory->url = 'javascript:;';
            
            $factory->builder->create('all', Link::class, function(LinkFactory $factory) {
                $factory->title = 'All';
                $factory->url = url('/orders/all');
            });
            $factory->builder->create('type_1', Link::class, function(LinkFactory $factory) {
                $factory->title = 'Type 1';
                $factory->url = url('/orders/1');
                $factory->linkAttributes->push(['class' => 'text-color-red']);
            });
            $factory->builder->create('type_2', Link::class, function(LinkFactory $factory) {
                $factory->title = 'Type 2';
                $factory->url = url('/orders/2');
                $factory->linkAttributes->push(['data-attribute' => 'value']);
            });
        });
        
        $html = $builder->render();
        $file = file_get_contents(__DIR__ . '/stub/menu.html');
        
        $this->assertEquals($file, $html);
    }
    
    public function testDisplayRules()
    {
        $builder = $this->builderFactory();

        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index Page';
            $factory->url = url('/');
        });
        $builder->create('login', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Login';
            $factory->url = url('/login');
            $factory->displayRule = function() {
                return true;
            };
        });
        $builder->create('admin', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Admin';
            $factory->url = url('/admin');
            $factory->displayRule = false;
        });
        $builder->create('logout', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Logout';
            $factory->url = url('/logout');
            $factory->displayRule = null;
        });

        $html = $builder->render();
        $file = file_get_contents(__DIR__ . '/stub/display_rules.html');

        $this->assertEquals($file, $html);
    }

    public function testAnotherViewRender()
    {
        view()->addLocation(__DIR__ . '/stub');
        $this->app['config']->prepend('menu.paths', __DIR__ . '/stub');
        
        $builder = $this->builderFactory();
        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index Page';
            $factory->url = url('/');
        });
        $builder->create('group', SubMenu::class, function(SubMenuFactory $factory) {
            $factory->builder->create('one', Link::class, function(LinkFactory $factory) {
                $factory->title = 'One';
                $factory->url = url('/one');
            });
        });

        $html = $builder->render('another');
        $file = file_get_contents(__DIR__ . '/stub/another_menu.html');
        $this->assertEquals($file, $html);

        $builder->get('group')->getBuilder()->setView('another');
        $html = $builder->render();
        $file = file_get_contents(__DIR__ . '/stub/another_sub_menu.html');
        $this->assertEquals($file, $html);

        $builder->setView('another');
        $builder->get('group')->getBuilder()->setView('menu::view');
        $html = $builder->render();
        $file = file_get_contents(__DIR__ . '/stub/another_set_view_menu.html');
        $this->assertEquals($file, $html);
    }
    
    public function testInsert()
    {
        $builder = $this->builderFactory();
        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index Page';
            $factory->url = url('/');
        });
        $builder->create('logout', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Logout';
            $factory->url = url('logout');
        });
        
        $builder->insertAfter('index', function (Builder $builder) {
            $builder->create('users', Link::class, function(LinkFactory $factory) {
                $factory->title = 'Users';
                $factory->url = url('users');
            });
        });
        
        $builder->insertBefore('users', function (Builder $builder) {
            $builder->create('profile', Link::class, function(LinkFactory $factory) {
                $factory->title = 'Profile';
                $factory->url = url('profile');
            });
        });

        $html = $builder->render('another');
        $file = file_get_contents(__DIR__ . '/stub/insert.html');
        $this->assertEquals($file, $html);
    }
}