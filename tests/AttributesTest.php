<?php

namespace Malezha\Menu\Tests;

use Malezha\Menu\Entity\Attributes;

class AttributesTest extends TestCase
{
    public function testGetSetAllForgetPutPush()
    {
        $attributes = new Attributes([
            'class' => 'some-style',
        ]);
        $this->assertEquals('some-style', $attributes->get('class'));
        
        $subset = ['class' => 'new-style', 'id' => 'link'];
        $attributes->set($subset);
        $this->assertEquals('new-style', $attributes->get('class'));
        $this->assertEquals('link', $attributes->get('id'));
        $this->assertArraySubset($subset, $attributes->all());
        
        $attributes->forget('id');
        $this->assertEquals(null, $attributes->get('id'));
        
        $attributes->put('id', 'link');
        $this->assertEquals('link', $attributes->get('id'));
        
        $push = ['data-value' => 'some'];
        $attributes->push($push);
        $this->assertArraySubset(array_merge($subset, $push), $attributes->all());
    }
    
    public function testMerge()
    {
        $attributes = new Attributes([
            'class' => 'some-style',
        ]);
        
        $attributes->merge([
            'class' => 'another-style',
            'id' => 'test',
        ]);
        
        $this->assertArraySubset([
            'class' => 'some-style another-style',
            'id' => 'test',
        ], $attributes->all());
    }

    public function testBuild()
    {
        $attributes = new Attributes([
            'class' => 'some-style another-style',
            'id' => 'test',
        ]);

        $build = $attributes->build();
        
        $this->assertEquals(' class="some-style another-style" id="test"', $build);
    }
}