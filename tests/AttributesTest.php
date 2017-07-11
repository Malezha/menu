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
        return $this->app->makeWith(Attributes::class, [
            'attributes' => $this->attributesStub(),
        ]);
    }
    
    protected function serializeStub()
    {
        return 'C:31:"Malezha\Menu\Support\Attributes":56:{a:2:{s:5:"class";s:10:"some-style";s:2:"id";s:4:"test";}}';
    }
    
    protected function attributesStub()
    {
        return [
            'class' => 'some-style',
            'id' => 'test',
        ];
    }
    
    public function testGet()
    {
        $attributes = $this->attributeFactory();
        
        $this->assertEquals('some-style', $attributes->get('class'));
        $this->assertEquals('some-style', $attributes['class']);
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
        unset($attributes['class']);
        
        $this->assertEquals(null, $attributes->get('id'));
        $this->assertEquals(null, $attributes->get('class'));
    }
    
    public function testPut()
    {
        $attributes = $this->attributeFactory();

        $attributes->put('data-test', 'value');
        $attributes['class'] = 'test';

        $this->assertEquals('value', $attributes->get('data-test'));
        $this->assertEquals('test', $attributes->get('class'));
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
        $this->assertTrue(isset($attributes['class']));
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
    
    public function testMergeAttributesEmptyConstructor()
    {
        $this->assertEquals([], (new MergeAttributes())->merge());
    }
    
    public function testToArray()
    {
        $attributes = $this->attributeFactory();
        $this->assertEquals($this->attributesStub(), $attributes->toArray());
    }
}