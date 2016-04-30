<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\Contracts\Attributes;
use Malezha\Menu\Support\MergeAttributes;

/**
 * Class AttributesTest
 * @package Malezha\Menu\Tests
 */
class AttributesTest extends TestCase
{
    /**
     * @return Attributes
     */
    protected function attributeFactory()
    {
        return $this->app->make(Attributes::class, [
            'attributes' =>[
                'class' => 'some-style',
                'id' => 'test',
            ]
        ]);
    }
    
    public function testGet()
    {
        $attributes = $this->attributeFactory();
        
        $this->assertEquals('some-style', $attributes->get('class'));
    }
    
    public function testSet()
    {
        $attributes = $this->attributeFactory();
        $subset = ['class' => 'new-style', 'id' => 'link'];
        
        $attributes->set($subset);
        
        $this->assertArraySubset($subset, $attributes->all());
    }
    
    public function testForget()
    {
        $attributes = $this->attributeFactory();
        
        $attributes->forget('id');
        
        $this->assertEquals(null, $attributes->get('id'));
    }
    
    public function testPut()
    {
        $attributes = $this->attributeFactory();

        $attributes->put('data-test', 'value');

        $this->assertEquals('value', $attributes->get('data-test'));
    }
    
    public function testPush()
    {
        $attributes = $this->attributeFactory();

        $subset = $attributes->all();
        $push = ['data-value' => 'some'];
        
        $attributes->push($push);

        $this->assertArraySubset(array_merge($subset, $push), $attributes->all());
    }
    
    public function testHas()
    {
        $attributes = $this->attributeFactory();
        
        $this->assertTrue($attributes->has('class'));
    }

    public function testMerge()
    {
        $attributes = $this->attributeFactory();
        
        $attributes->merge([
            'class' => 'another-style',
        ]);
        
        $this->assertArraySubset([
            'class' => 'some-style another-style',
            'id' => 'test',
        ], $attributes->all());
    }

    public function testBuild()
    {
        $attributes = $this->attributeFactory();
        $expected = ' class="some-style active" id="test"';
        $expectedString = ' class="some-style" id="test"';
        
        $this->assertEquals($expected, $attributes->build(['class' => 'active']));
        $this->assertEquals($expectedString, (string) $attributes);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMergeArrayValuesException()
    {
        $merge = new MergeAttributes();
    }
}