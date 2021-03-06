<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Element\Link;
use Malezha\Menu\Element\SubMenu;
use Malezha\Menu\Element\Text;
use Malezha\Menu\Factory\LinkFactory;
use Malezha\Menu\Factory\SubMenuFactory;
use Malezha\Menu\Factory\TextFactory;

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
        return $this->app->makeWith(Builder::class, [
            'activeAttributes' => $this->app->makeWith(Attributes::class, ['attributes' => ['class' => 'active']]),
            'attributes' => $this->app->makeWith(Attributes::class, ['attributes' => ['class' => 'menu']]),
        ]);
    }
    
    protected function serializeStub()
    {
        return $this->getStub('serialized/builder.txt');
    }
    
    protected function toArrayStub()
    {
        return [
            'type' => 'ul',
            'view' => 'menu::view',
            'attributes' => [
                'class' => 'menu',
            ],
            'activeAttributes' => [
                'class' => 'active',
            ],
            'elements' => [
                'index' => [
                    'view' => 'menu::elements.link',
                    'title' => 'Index',
                    'url' => 'http://localhost',
                    'attributes' => [],
                    'activeAttributes' => [
                        'class' => 'active',
                    ],
                    'linkAttributes' => [],
                    'displayRule' => true,
                    'type' => 'link',
                ],
                'submenu' => [
                    'view' => 'menu::elements.submenu',
                    'title' => '',
                    'url' => '#',
                    'attributes' => [],
                    'activeAttributes' => [
                        'class' => 'active',
                    ],
                    'linkAttributes' => [],
                    'displayRule' => true,
                    'builder' => [
                        'type' => 'ul',
                        'view' => 'menu::view',
                        'attributes' => [],
                        'activeAttributes' => [],
                        'elements' => [
                            'item_1' => [
                                'view' => 'menu::elements.link',
                                'title' => 'Item 1',
                                'url' => 'http://localhost/item/1',
                                'attributes' => [],
                                'activeAttributes' => [],
                                'linkAttributes' => [],
                                'displayRule' => true,
                                'type' => 'link',
                            ],
                            'item_2' => [
                                'view' => 'menu::elements.link',
                                'title' => 'Item 2',
                                'url' => 'http://localhost/item/2',
                                'attributes' => [],
                                'activeAttributes' => [],
                                'linkAttributes' => [],
                                'displayRule' => true,
                                'type' => 'link',
                            ],
                            'item_3' => [
                                'view' => 'menu::elements.link',
                                'title' => 'Item 3',
                                'url' => 'http://localhost/item/3',
                                'attributes' => [],
                                'activeAttributes' => [],
                                'linkAttributes' => [],
                                'displayRule' => true,
                                'type' => 'link',
                            ],
                        ],
                    ],
                    'type' => 'submenu',
                ],
            ],
        ];
    }
    
    public function testConstructor()
    {
        $builder = $this->builderFactory();

        $this->assertAttributeEquals($this->app, 'app', $builder);
        $this->assertAttributeEquals(Builder::UL, 'type', $builder);
        $this->assertAttributeInstanceOf(Attributes::class, 'attributes', $builder);
        $this->assertAttributeInternalType('array', 'elements', $builder);
        $this->assertAttributeInstanceOf(Attributes::class, 'activeAttributes', $builder);
    }

    public function testCreate()
    {
        $builder = $this->builderFactory();

        /** @var Link $item */
        $item = $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Home';
            $factory->url = '/';

            return $factory->build();
        });

        $this->assertInstanceOf(Link::class, $item);
        $this->assertAttributeEquals('Home', 'title', $item);
        $this->assertAttributeEquals('/', 'url', $item);
    }

    public function testCreateIfExists()
    {
        $this->expectException(\RuntimeException::class);
        
        $builder = $this->builderFactory();

        $builder->create('index', Link::class, function (LinkFactory $factory) {
            return $factory->build();
        });

        $builder->create('index', SubMenu::class, function (SubMenuFactory $factory) {
            return $factory->build();
        }); // Duplicate
    }
    
    public function testGet()
    {
        $builder = $this->builderFactory();
        
        $item = $builder->create('test', Link::class, function (LinkFactory $factory) {
            return $factory->build();
        });
        
        $this->assertEquals($item, $builder->get('test'));
        $this->assertEquals(null, $builder->get('notFound'));
        $this->assertEquals($item, $builder['test']);
        $this->assertEquals(null, $builder['notFound']);
    }

    public function testGetByIndex()
    {
        $builder = $this->builderFactory();

        $item = $builder->create('test', Link::class, function (LinkFactory $factory) {
            return $factory->build();
        });

        $this->assertEquals($item, $builder->getByIndex(0));
        $this->assertEquals(null, $builder->getByIndex(1));
        $this->assertEquals($item, $builder[0]);
        $this->assertEquals(null, $builder[1]);
    }
    
    public function testHas()
    {
        $builder = $this->builderFactory();

        $this->assertFalse($builder->has('test'));
        $this->assertFalse(isset($builder['test']));
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

        $item = $builder->create('test', Link::class, function (LinkFactory $factory) {
            return $factory->build();
        });

        $this->assertEquals(['test' => $item], $builder->all());
    }
    
    public function testForget()
    {
        $builder = $this->builderFactory();

        $builder->create('test', Link::class, function (LinkFactory $factory) {
            return $factory->build();
        });
        $builder->create('another', Text::class, function (TextFactory $factory) {
            return $factory->build();
        });
        
        $this->assertTrue($builder->has('test'));
        $builder->forget('test');
        $this->assertFalse($builder->has('test'));
        
        $this->assertTrue($builder->has('another'));
        unset($builder['another']);
        $this->assertFalse($builder->has('another'));
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
        
        $html = $builder->render();
        
        $this->assertEquals($this->getStub('menu.html'), $html);
    }
    
    public function testDisplayRules()
    {
        $builder = $this->builderFactory();

        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index Page';
            $factory->url = url('/');

            return $factory->build();
        });

        $builder->create('login', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Login';
            $factory->url = url('/login');
            $factory->displayRule = function() {
                return true;
            };

            return $factory->build();
        });

        $builder->create('admin', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Admin';
            $factory->url = url('/admin');
            $factory->displayRule = false;

            return $factory->build();
        });

        $builder->create('logout', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Logout';
            $factory->url = url('/logout');
            $factory->displayRule = null;

            return $factory->build();
        });

        $html = $builder->render();

        $this->assertEquals($this->getStub('display_rules.html'), $html);
    }

    public function testAnotherViewRender()
    {
        view()->addLocation(__DIR__ . '/stub');
        $this->app['config']->prepend('menu.paths', __DIR__ . '/stub');
        
        $builder = $this->builderFactory();
        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index Page';
            $factory->url = url('/');

            return $factory->build();
        });

        $builder->create('group', SubMenu::class, function(SubMenuFactory $factory) {
            $factory->builder->create('one', Link::class, function(LinkFactory $factory) {
                $factory->title = 'One';
                $factory->url = url('/one');

                return $factory->build();
            });

            return $factory->build();
        });
        
        $this->assertEquals($this->getStub('another_menu.html'), $builder->render('another'));

        $builder->get('group')->getBuilder()->setView('another');
        $this->assertEquals($this->getStub('another_sub_menu.html'), $builder->render());

        $builder->setView('another');
        $builder->get('group')->getBuilder()->setView('menu::view');
        $this->assertEquals($this->getStub('another_set_view_menu.html'), $builder->render());
    }
    
    public function testInsert()
    {
        $builder = $this->builderFactory();

        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index Page';
            $factory->url = url('/');

            return $factory->build();
        });

        $builder->create('logout', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Logout';
            $factory->url = url('logout');

            return $factory->build();
        });
        
        $builder->insertAfter('index', function (Builder $builder) {
            $builder->create('users', Link::class, function(LinkFactory $factory) {
                $factory->title = 'Users';
                $factory->url = url('users');

                return $factory->build();
            });
        });
        
        $builder->insertBefore('users', function (Builder $builder) {
            $builder->create('profile', Link::class, function(LinkFactory $factory) {
                $factory->title = 'Profile';
                $factory->url = url('profile');

                return $factory->build();
            });
        });

        $this->assertEquals($this->getStub('insert.html'), $builder->render('another'));
    }

    public function testInsertExceptionHasNot()
    {
        $this->expectException(\RuntimeException::class);

        $builder = $this->builderFactory();
        $builder->insertBefore('not_exist', function (Builder $builder) {});
    }

    public function testInsertExceptionDuplicate()
    {
        $this->expectException(\RuntimeException::class);

        $builder = $this->builderFactory();

        $builder->create('home', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Home';
            $factory->url = '/';

            return $factory->build();
        });

        $builder->create('some', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Some';
            $factory->url = '/';

            return $factory->build();
        });

        $builder->insertAfter('home', function (Builder $builder) {
            $builder->create('some', Text::class, function(TextFactory $factory) {
                $factory->text = 'Duplicate some';

                return $factory->build();
            });
        });
    }

    public function testArray()
    {
        $builder = $this->builderFactory();
        $builder->create('index', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Index';
            $factory->url = url('/');

            return $factory->build();
        });

        $builder->create('submenu', SubMenu::class, function(SubMenuFactory $factory) {
            for ($i = 1; $i <= 3; ++$i) {
                $factory->builder->create('item_' . $i, Link::class, function(LinkFactory $factory) use ($i) {
                    $factory->title = 'Item ' . $i;
                    $factory->url = url('/item', ['id' => $i]);

                    return $factory->build();
                });
            }

            return $factory->build();
        });
        
        $this->assertEquals($this->toArrayStub(), $builder->toArray());

        $builderFormArray = $builder->fromArray($builder->toArray());
        $this->assertEquals($builder->render(), $builderFormArray->render());
    }
    
    public function testSet()
    {
        $builder = $this->builderFactory();
        $element = (new TextFactory($this->app))->build([
            'text' => 'Text',
        ]);
        $notElement = [];
        
        $builder['element'] = $element;
        $builder['notElement'] = $notElement;
        
        $this->assertTrue($builder->has('element'));
        $this->assertFalse($builder->has('notElement'));
    }

    public function testCreateCallbackException()
    {
        $this->expectException(\RuntimeException::class);

        $builder = $this->builderFactory();
        $builder->create('test', Link::class, function(LinkFactory $factory) {
            return 'string';
        });
    }

    public function testCreateTypeWrongException()
    {
        $this->expectException(\RuntimeException::class);

        $builder = $this->builderFactory();
        $builder->create('test', \Exception::class);
    }
}