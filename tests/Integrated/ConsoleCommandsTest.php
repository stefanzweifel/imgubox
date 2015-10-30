<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConsoleCommandsTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function test_fetch_favs_fires_job()
    {
        $user = factory(ImguBox\User::class)->create();
        $imgur = factory(ImguBox\Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(ImguBox\Provider::class, 'Imgur')->create()->id
        ]);

        $dropbox = factory(ImguBox\Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(ImguBox\Provider::class, 'Dropbox')->create()->id
        ]);

        $this->expectsJobs(ImguBox\Jobs\FetchImages::class);

        $fetchFavs = Artisan::call('imgubox:fetchFavs');
    }
}
