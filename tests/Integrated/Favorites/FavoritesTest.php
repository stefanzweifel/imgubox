<?php

namespace ImguBox\Tests\Integrated\UserSettings;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ImguBox\Jobs\DeleteFavorites;
use ImguBox\Log;
use ImguBox\Tests\Support\FactoryTools;
use ImguBox\Tests\TestCase;
use ImguBox\User;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function testItListsUserFavorites()
    {
        $user = $this->user();
        $logs = $this->logs($user, 10);

        $this->actingAs($user)->visit('favorites')->see($logs->first()->imgur_id);
    }

    public function testItShowsMessageIfNoFavoritesAreAvailable()
    {
        $user = $this->user();

        $this->actingAs($user)->visit('favorites')->see("We haven't synced your favorites yet or you just purged your log.");
    }

    public function testItPurgesSingleFavorite()
    {
        $user = $this->user();
        $log = $this->logs($user);

        $this->actingAs($user)
                ->visit('favorites')
                ->see("Instead")
                ->press("redownload-{$log->id}")
                ->seePageIs('/favorites')
                ->dontSee($log->imgur_id);

        $log = Log::whereId($log->id)->withTrashed()->first();

        $this->assertTrue($log->trashed());
    }

    public function testItPurgesAllFavorites()
    {
        $this->expectsJobs(DeleteFavorites::class);

        $user = $this->user();
        $this->logs($user, 10);

        $this->actingAs($user)
                ->visit('favorites')
                ->see("Instead")
                ->click("Yes, redownload everything")
                ->seePageIs('/favorites');
    }

}