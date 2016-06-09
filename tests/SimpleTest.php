<?php
/**
 * Created by PhpStorm.
 * User: malezha
 * Date: 09.06.16
 * Time: 17:16
 */

namespace Malezha\Menu\Tests;


use Illuminate\Contracts\Routing\UrlGenerator;

class SimpleTest extends TestCase
{
    public function testUrl()
    {
        /** @var UrlGenerator $generator */
        $generator = $this->app->make(UrlGenerator::class);
        $this->assertEquals('#', $generator->to('#'));
        //$this->assertEquals('javascript:;', $generator->to('javascript:;'));
    }
}