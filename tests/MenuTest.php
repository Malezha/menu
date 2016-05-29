<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\Menu;
use Malezha\Menu\Element\Link;
use Malezha\Menu\Factory\LinkFactory;

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
                $factory->setTitle('One')->setUrl('/one');
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
}