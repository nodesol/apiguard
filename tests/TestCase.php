<?php

namespace Nodesol\Apiguard\Tests;

use Nodesol\Apiguard\ApiguardServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app): array
    {
        return [ApiguardServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('apiguard.user_model', TestUser::class);
        $app['config']->set('apiguard.cache_store', 'array');
        $app['config']->set('cache.default', 'array');
    }
}
