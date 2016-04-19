<?php

namespace Malezha\Menu\Tests;

use Illuminate\Contracts\Routing\UrlGenerator;
use Malezha\Menu\Entity\Link;

class LinkTest extends TestCase
{
    protected function linkFactory()
    {
        return new Link('Home',
            $this->app->make(UrlGenerator::class)->to('/index'),
            ['class' => 'link']);
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