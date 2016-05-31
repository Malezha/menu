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
        $builder = $this->app->make(Builder::class, [
            'name' => 'test',
            'activeAttributes' => $this->app->make(Attributes::class, ['attributes' => ['class' => 'active']]),
            'attributes' => $this->app->make(Attributes::class, ['attributes' => ['class' => 'menu']]),
        ]);
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

        return $builder;
    }

    protected function getStub()
    {
        return $file = file_get_contents(__DIR__ . '/stub/menu.html');
    }

    protected function makeTest($render)
    {
        $this->app->instance('menu.render', $render);
        $this->app->alias('menu.render', MenuRender::class);

        $builder = $this->getBuilder();
        $stub = $this->getStub();
        $this->assertAttributeInstanceOf(get_class($render), 'viewFactory', $builder);
        $this->assertEquals($stub, $builder->render());
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
        $this->assertEquals($text, $basic->make($view)->render());
        $this->assertEquals($text, $basic->make($view2)->render());
        
        $blade = new Illuminate($this->app);
        $this->assertEquals($text, $blade->make($view)->render());
        $this->assertEquals($text, $blade->make($view2)->render());
    }
}