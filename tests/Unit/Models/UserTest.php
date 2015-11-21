<?php

namespace ImguBox\Tests\Unit\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ImguBox\Tests\Support\FactoryTools;
use ImguBox\Tests\TestCase;
use ImguBox\User;

class UserTest extends Testcase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function testItReturnsAssociatedLogs()
    {
        $user = $this->user();

        $this->logs($user, 5);

        $this->assertEquals(5, $user->logs()->count());
    }

    public function testCanFetchFavoritesReturnsTrue()
    {
        $user = $this->user();
        $this->dropboxToken($user);
        $this->imgurToken($user);

        $this->assertTrue($user->canFetchFavorites());
    }

    public function testCanFetchFavoritesReturnsFalse()
    {
        $user = $this->user();
        $this->dropboxToken($user);

        $this->assertFalse($user->canFetchFavorites());
    }



}
