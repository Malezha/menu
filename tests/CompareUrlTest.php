<?php
namespace Malezha\Menu\Tests;

use Illuminate\Routing\UrlGenerator;
use Malezha\Menu\Support\ComparativeUrl;

class CompareUrlTest extends TestCase
{
    protected function makeComparativeUrl($currentUrl)
    {
        $mock = $this->createMock(UrlGenerator::class);
        $mock->method('current')->willReturn(url($currentUrl));
        $mock->method('to')->willReturnCallback('url');

        return new ComparativeUrl($mock, config('menu.skippedPaths'));
    }
    
    public function testSkipped()
    {
        $compare = $this->makeComparativeUrl(url('/'));
        foreach (config('menu.skippedPaths') as $value) {
            $this->assertFalse($compare->isEquals($value));
        }
    }

    public function testEquals()
    {
        $stub = [
            'http://username:password@hostname/path?arg=value#anchor',
            '//www.example.com/path?googleguy=googley',
            'http://usr:pss@example.com:81/mypath/myfile.html?a=b&b[]=2&b[]=3#myfragment',
            url('/'),
            '/',
        ];
        
        foreach ($stub as $value) {
            $compare = $this->makeComparativeUrl($value);
            $this->assertTrue($compare->isEquals($value));
        }
    }
}