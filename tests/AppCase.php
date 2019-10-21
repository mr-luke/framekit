<?php

namespace Tests;

use Orchestra\Testbench\TestCase;

/**
 * Feature AppCase for package.
 *
 * @author    Łukasz Sitnicki (mr-luke)
 * @link      http://github.com/mr-luke/framekit
 * @license   MIT
 */
class AppCase extends TestCase
{
    /**
     * DB configuration.
     */
    const DB_HOST = 'mysql';
    const DB_NAME = 'dev';
    const DB_USER = 'dev';
    const DB_PASS = 'dev';
    const DB_PREFIX = '';

    /**
     * Setup TestCase.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:refresh', [
            '--database' => 'mysql',
            '--realpath' => realpath(__DIR__.'/../database/migrations'),
        ]);
    }

    /**
     * Get application timezone.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return string|null
     */
    protected function getApplicationTimezone($app)
    {
        return 'Europe/Warsaw';
    }

    /**
     * Seting enviroment for Test.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['path.base'] = __DIR__.'/..';
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', static::DB_HOST),
            'database'  => env('DB_NAME', static::DB_NAME),
            'username'  => env('DB_USER', static::DB_USER),
            'password'  => env('DB_PASS', static::DB_PASS),
            'prefix'    => env('DB_PREFIX', static::DB_PREFIX),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict'    => true,
        ]);
    }

    /**
     * Return array of providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            \Framekit\Providers\FramekitServiceProvider::class,
        ];
    }
}
