<?php

namespace ImguBox\Tests\Unit\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ImguBox\Provider;
use ImguBox\Tests\Support\FactoryTools;
use ImguBox\Tests\TestCase;

class ProviderTest extends Testcase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function testIsImgurScopeReturnsImgur()
    {
        factory(Provider::class, 'Dropbox')->create();
        $imgurProvider = factory(Provider::class, 'Imgur')->create();

        $query = Provider::isImgur()->get();

        $this->assertEquals(1, $query->count());
        $this->assertEquals($imgurProvider->id, $query->first()->id);
    }

    public function testIsDropboxScopeReturnsDropbox()
    {
        factory(Provider::class, 'Imgur')->create();
        $dropboxProvider = factory(Provider::class, 'Dropbox')->create();

        $query = Provider::isDropbox()->get();

        $this->assertEquals(1, $query->count());
        $this->assertEquals($dropboxProvider->id, $query->first()->id);
    }

    public function testImgurProviderReturnsMultipleTokens()
    {
        $imgurProvider = factory(Provider::class, 'Imgur')->create();
        $user1 = $this->user();
        $user2 = $this->user();

        $this->dropboxToken($user1);

        $this->imgurToken($user1);
        $this->imgurToken($user2);

        $tokens = $imgurProvider->tokens()->get();

        $this->assertEquals(2, $tokens->count());
    }

    public function testDropboxProviderReturnsMultipleTokens()
    {
        $dropboxProvider = factory(Provider::class, 'Dropbox')->create();
        $user1 = $this->user();
        $user2 = $this->user();

        $this->dropboxToken($user1);
        $this->dropboxToken($user2);

        $this->imgurToken($user1);
        $this->imgurToken($user2);

        $tokens = $dropboxProvider->tokens()->get();

        $this->assertEquals(2, $tokens->count());
    }
}
