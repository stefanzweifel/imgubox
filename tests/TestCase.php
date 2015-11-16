<?php

namespace ImguBox\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;
use ImguBox\User;

class TestCase extends LaravelTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        /**
         * Set Sqlite Database to Memory
         */
        $app['config']->set('database.connections.sqlite.database', ':memory:');

        return $app;
    }

    /**
     * Act as a User
     * @return void
     */
    protected function beUser()
    {
        $this->user = factory(User::class)->create();

        $this->be($this->user);
    }

}
