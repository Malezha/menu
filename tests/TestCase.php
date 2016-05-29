<?php
namespace Malezha\Menu\Tests;

use Malezha\Menu\MenuFacade;
use Malezha\Menu\MenuServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Class TestCase
 * @package Malezha\Menu\Tests
 */
class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [MenuServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['Menu' => MenuFacade::class];
    }

    public function expectException($exception)
    {
        if (version_compare(\PHPUnit_Runner_Version::id(), '5.2', '>=')) {
            parent::expectException($exception);
        } else {
            $this->setExpectedException($exception);
        }
    }
}