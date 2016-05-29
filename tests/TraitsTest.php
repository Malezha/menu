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
        $link = (new LinkFactory($this->app))->setAttributes($this->app->make(Attributes::class, ['attributes' => [
            'class' => 'color-red',
        ]]))->build();
        
        $hasClass = $link->getAttributes(function($attributes) {
            $this->assertInstanceOf(Attributes::class, $attributes);
            return $attributes->has('class');
        });
        
        $this->assertTrue($hasClass);
    }
}