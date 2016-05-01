<?php
namespace Malezha\Menu\Tests;

use Illuminate\Contracts\Container\Container;
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
        $index = $builder->add('index', 'Index Page', url('/'));
        $index->getLink()->getAttributes()->push(['class' => 'menu-link']);

        $builder->group('orders', function(Item $item) {
            $item->getAttributes()->push(['class' => 'child-menu']);

            $link = $item->getLink();
            $link->setTitle('Orders');
            $link->setUrl('javascript:;');
        }, function(Builder $menu) {
            $menu->add('all', 'All', url('/orders/all'));
            $menu->add('type_1', 'Type 1', url('/orders/1'), [], ['class' => 'text-color-red']);

            $menu->add('type_2', 'Type 2', url('/orders/2'), [], [], function(Item $item) {
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
}