<?php

namespace LaraOrVite\Framework\Tests;

use LaraOrVite\Framework\FrontendServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            FrontendServiceProvider::class,
        ];
    }
}