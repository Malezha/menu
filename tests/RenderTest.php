<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\Item;
use Malezha\Menu\Contracts\MenuRender;
use Malezha\Menu\Render\Basic;
use Malezha\Menu\Render\Blade;

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
        $this->makeTest(new Blade($this->app));

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
        
        $blade = new Blade($this->app);
        $this->assertEquals($text, $blade->make($view)->render());
        $this->assertEquals($text, $blade->make($view2)->render());
    }
}