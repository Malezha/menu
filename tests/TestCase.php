<?php

namespace Malezha\Menu\Tests;

use Malezha\Menu\MenuFacade;
use Malezha\Menu\MenuServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

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
}