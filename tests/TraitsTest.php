<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Contracts\Link;

/**
 * Class TraitsTest
 * @package Malezha\Menu\Tests
 */
class TraitsTest extends TestCase
{
    public function testGetAttributesCallback()
    {
        /** @var Link $link */
        $link = $this->app->make(Link::class, [
            'attributes' => $this->app->make(Attributes::class, ['attributes' => [
                'class' => 'color-red',
            ]])
        ]);
        
        $hasClass = $link->getAttributes(function($attributes) {
            $this->assertInstanceOf(Attributes::class, $attributes);
            return $attributes->has('class');
        });
        
        $this->assertTrue($hasClass);
    }
}