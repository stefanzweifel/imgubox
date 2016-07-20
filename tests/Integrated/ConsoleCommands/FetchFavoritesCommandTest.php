<?php

namespace ImguBox\Tests\Integrated\ConsoleCommands;

use Artisan;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ImguBox\Jobs\FetchImages;
use ImguBox\Tests\Support\FactoryTools;
use ImguBox\Tests\TestCase;

class FetchFavoritesCommandTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function test_fetch_favs_fires_job()
    {
        $this->setupUsers();

        $this->expectsJobs(FetchImages::class);

        Artisan::call('imgubox:fetchFavs');
    }
}
