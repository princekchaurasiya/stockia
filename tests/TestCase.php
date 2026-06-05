<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->assertUsingIsolatedTestDatabase();
    }

    /**
     * Prevent accidental runs against the live MySQL database.
     * PHPUnit must use sqlite (:memory: or database/testing.sqlite) via phpunit.xml / .env.testing.
     */
    protected function assertUsingIsolatedTestDatabase(): void
    {
        if (! app()->environment('testing')) {
            $this->fail('Tests must run with APP_ENV=testing (see phpunit.xml).');
        }

        $driver = config('database.default');

        if ($driver !== 'sqlite') {
            $this->fail(
                "Tests must use sqlite, not [{$driver}]. "
                .'Do not point PHPUnit at the live database — check phpunit.xml and .env.testing.'
            );
        }

        $database = (string) config('database.connections.sqlite.database');

        $liveDbName = env('DB_DATABASE');
        if ($liveDbName && $database === $liveDbName && $driver === 'mysql') {
            $this->fail("Refusing to run tests against live database [{$liveDbName}].");
        }
    }
}
