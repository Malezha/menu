<?php

namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Link;

class LinkTest extends TestCase
{
    protected function linkFactory()
    {
        return $this->app->make(Link::class, [
            'title' => 'Home',
            'url' => url('/index'),
            'attributes' => $this->app->make(Attributes::class, ['attributes' => ['class' => 'link']]),
        ]);
    }

    public function testTitle()
    {
        $link = $this->linkFactory();

        $this->assertEquals('Home', $link->getTitle());

        $link->setTitle('Index');
        $this->assertAttributeEquals('Index', 'title', $link);
    }
    
    public function testUrl()
    {
        $link = $this->linkFactory();

        $this->assertEquals('http://localhost/index', $link->getUrl());
        
        $link->setUrl('/home');
        $this->assertAttributeEquals('/home', 'url', $link);
    }
}