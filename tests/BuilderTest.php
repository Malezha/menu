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

        $item = $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->setTitle('Home')->setUrl('/');
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
            $factory->setTitle('Index Page')
                ->setUrl(url('/'))
                ->getLinkAttributes()->push(['class' => 'menu-link']);
        });
        
        $builder->create('orders', SubMenu::class, function(SubMenuFactory $factory) {
            $factory->getAttributes()->push(['class' => 'child-menu']);
            $factory->setTitle('Orders')->setUrl('javascript:;');
            
            $subBuilder = $factory->getBuilder();
            $subBuilder->create('all', Link::class, function(LinkFactory $factory) {
                $factory->setTitle('All')->setUrl(url('/orders/all'));
            });
            $subBuilder->create('type_1', Link::class, function(LinkFactory $factory) {
                $factory->setTitle('Type 1')->setUrl(url('/orders/1'))
                    ->getLinkAttributes()->push(['class' => 'text-color-red']);
            });
            $subBuilder->create('type_2', Link::class, function(LinkFactory $factory) {
                $factory->setTitle('Type 2')->setUrl(url('/orders/2'))
                    ->getLinkAttributes()->push(['data-attribute' => 'value']);
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
            $factory->setTitle('Index Page')->setUrl(url('/'));
        });
        $builder->create('login', Link::class, function(LinkFactory $factory) {
            $factory->setTitle('Login')->setUrl(url('/login'));
        })->setDisplayRule(function() {
            return true;
        });
        $builder->create('admin', Link::class, function(LinkFactory $factory) {
            $factory->setTitle('Admin')->setUrl(url('/admin'));
        })->setDisplayRule(false);
        $builder->create('logout', Link::class, function(LinkFactory $factory) {
            $factory->setTitle('Logout')->setUrl(url('/logout'));
        })->setDisplayRule(null);

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
            $factory->setTitle('Index Page')->setUrl(url('/'));
        });
        $builder->create('group', SubMenu::class, function(SubMenuFactory $factory) {
            $factory->getBuilder()->create('one', Link::class, function(LinkFactory $factory) {
                $factory->setTitle('One')->setUrl(url('/one'));
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
            $factory->setTitle('Index Page')->setUrl(url('/'));
        });
        $builder->create('logout', Link::class, function(LinkFactory $factory) {
            $factory->setTitle('logout')->setUrl(url('logout'));
        });
        
        $builder->insertAfter('index', function (Builder $builder) {
            $builder->create('users', Link::class, function(LinkFactory $factory) {
                $factory->setTitle('Users')->setUrl(url('users'));
            });
        });
        
        $builder->insertBefore('users', function (Builder $builder) {
            $builder->create('profile', Link::class, function(LinkFactory $factory) {
                $factory->setTitle('Profile')->setUrl(url('profile'));
            });
        });

        $html = $builder->render('another');
        $file = file_get_contents(__DIR__ . '/stub/insert.html');
        $this->assertEquals($file, $html);
    }
}