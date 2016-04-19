<?php

namespace Malezha\Menu\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [\Malezha\Menu\MenuServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['Menu' => \Malezha\Menu\MenuFacade::class];
    }
}