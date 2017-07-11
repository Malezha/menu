<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\MenuRender;
use Malezha\Menu\Element\Link;
use Malezha\Menu\Element\SubMenu;
use Malezha\Menu\Factory\LinkFactory;
use Malezha\Menu\Factory\SubMenuFactory;
use Malezha\Menu\Render\Basic;
use Malezha\Menu\Render\Illuminate;

class RenderTest extends TestCase
{
    protected function getBuilder()
    {
        /** @var Builder $builder */
        $builder = $this->app->makeWith(Builder::class, [
            'activeAttributes' => $this->app->makeWith(Attributes::class, ['attributes' => ['class' => 'active']]),
            'attributes' => $this->app->makeWith(Attributes::class, ['attributes' => ['class' => 'menu']]),
        ]);

        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index Page';
            $factory->url = url('/');
            $factory->linkAttributes->push(['class' => 'menu-link']);

            return $factory->build();
        });

        $builder->create('orders', SubMenu::class, function(SubMenuFactory $factory) {
            $factory->attributes->push(['class' => 'child-menu']);
            $factory->title = 'Orders';
            $factory->url = 'javascript:;';

            $factory->builder->create('all', Link::class, function(LinkFactory $factory) {
                $factory->title = 'All';
                $factory->url = url('/orders/all');

                return $factory->build();
            });

            $factory->builder->create('type_1', Link::class, function(LinkFactory $factory) {
                $factory->title = 'Type 1';
                $factory->url = url('/orders/1');
                $factory->linkAttributes->push(['class' => 'text-color-red']);

                return $factory->build();
            });

            $factory->builder->create('type_2', Link::class, function(LinkFactory $factory) {
                $factory->title = 'Type 2';
                $factory->url = url('/orders/2');
                $factory->linkAttributes->push(['data-attribute' => 'value']);

                return $factory->build();
            });

            return $factory->build();
        });

        return $builder;
    }
    
    protected function makeTest($render)
    {
        $this->app->instance('menu.render', $render);
        $this->app->alias('menu.render', MenuRender::class);

        $builder = $this->getBuilder();
        $this->assertAttributeInstanceOf(get_class($render), 'viewFactory', $builder);
        $this->assertEquals($this->getStub('menu.html'), $builder->render());
    }

    public function testBladeRender()
    {
        $this->makeTest(new Illuminate($this->app));

    }

    public function testBasicRender()
    {
        $this->makeTest(new Basic($this->app));
    }

    public function testDirectoryPath()
    {
        $path = __DIR__ . '/stub';
        $view = 'directory.view';
        $view2 = 'directory/view';
        $text = 'Hello, Menu builder!';
        
        $this->app['config']->prepend('menu.paths', $path);
        $this->app['view']->addLocation($path);
        
        $basic = new Basic($this->app);
        $this->assertEquals($text, $basic->make($view)->with('menu', 'Menu')->render());
        $this->assertEquals($text, $basic->make($view2)->with('menu', 'Menu')->render());
        
        $blade = new Illuminate($this->app);
        $this->assertEquals($text, $blade->make($view)->with('menu', 'Menu')->render());
        $this->assertEquals($text, $blade->make($view2)->with('menu', 'Menu')->render());
    }

    public function testMakeExceptionBasic()
    {
        $this->expectException(\Exception::class);

        $basic = new Basic($this->app);
        $basic->make('view_not_found');
    }

    public function testMakeExceptionIlluminate()
    {
        $this->expectException(\Exception::class);

        $blade = new Illuminate($this->app);
        $blade->make('view_not_found');
    }
}