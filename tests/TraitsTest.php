<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Factory\LinkFactory;

/**
 * Class TraitsTest
 * @package Malezha\Menu\Tests
 */
class TraitsTest extends TestCase
{
    public function testGetAttributesCallback()
    {
        $factory = (new LinkFactory($this->app));
        $factory->attributes = $this->app->make(Attributes::class, ['attributes' => [
            'class' => 'color-red',
        ]]);
        $link = $factory->build();
        
        $hasClass = $link->getAttributes(function($attributes) {
            $this->assertInstanceOf(Attributes::class, $attributes);
            return $attributes->has('class');
        });
        
        $this->assertTrue($hasClass);
    }
}