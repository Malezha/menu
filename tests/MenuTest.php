<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\Menu;
use Malezha\Menu\Element\Link;
use Malezha\Menu\Element\SubMenu;
use Malezha\Menu\Element\Text;
use Malezha\Menu\Factory\LinkFactory;
use Malezha\Menu\Factory\SubMenuFactory;
use Malezha\Menu\Factory\TextFactory;
use Malezha\Menu\Support\Attributes;

/**
 * Class MenuTest
 * @package Malezha\Menu\Tests
 */
class MenuTest extends TestCase
{
    /**
     * @return Menu
     */
    protected function menuFactory()
    {
        /** @var Menu $menu */
        $menu = $this->app->make(Menu::class, ['container' => $this->app]);
        $menu->make('test', function(Builder $builder) {
            $builder->create('one', Link::class, function(LinkFactory $factory) {
                $factory->title = 'One';
                $factory->url = '/one';
            });
        });
        
        return $menu;
    }
    
    public function testFacade()
    {
        $this->assertInstanceOf(Menu::class, \Menu::getFacadeRoot());
    }
    
    public function testGet()
    {
        $menu = $this->menuFactory();
        $builder = $menu->get('test');
        $this->assertInstanceOf(Builder::class, $builder);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetException()
    {
        $menu = \Menu::get('test');
    }
    
    public function testHas()
    {
        $menu = $this->menuFactory();
        $this->assertFalse($menu->has('another'));
        $this->assertTrue($menu->has('test'));
    }
    
    public function testForget()
    {
        $menu = $this->menuFactory();
        $menu->forget('test');
        $this->assertAttributeEquals([], 'menuList', $menu);
    }
    
    public function testRender()
    {
        $menu = $this->menuFactory();
        $file = file_get_contents(__DIR__ . '/stub/facade_render.html');
        $this->assertEquals($file, $menu->render('test'));
    }
    
    public function testFromArray()
    {
        /** @var Builder $builder */
        $builder = $this->app->make(Builder::class, [
            'attributes' => new Attributes(['class' => 'menu']),
            'activeAttributes' => new Attributes(['class' => 'active']),
        ]);
        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index';
            $factory->url = '/';
        });
        $builder->create('deliver_1', Text::class, function(TextFactory $factory) {
            $factory->text = null;
            $factory->attributes->put('class', 'deliver');
        });
        $builder->create('settings', SubMenu::class, function(SubMenuFactory $factory) {
            $factory->title = 'Index';
            $factory->builder->create('some', Link::class, function(LinkFactory $factory) {
                $factory->title = 'Some setting';
                $factory->url = '/settings/some';
            });
        });
        $builder->create('deliver_2', Text::class, function(TextFactory $factory) {
            $factory->text = null;
            $factory->attributes->put('class', 'deliver');
        });
        $builder->create('logout', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Logout';
            $factory->url = '/logout';
        });
        
        /** @var Menu $menu */
        $menu = $this->app->make(Menu::class);
        $menu->fromArray('from_array', $builder->toArray());
        
        $this->assertEquals($builder->render(), $menu->get('from_array')->render());
        $this->assertEquals($builder->toArray(), $menu->toArray('from_array'));
    }
}