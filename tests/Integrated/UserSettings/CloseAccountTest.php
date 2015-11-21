<?php

namespace ImguBox\Tests\Integrated\UserSettings;

use ImguBox\Tests\TestCase;
use ImguBox\Tests\Support\FactoryTools;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ImguBox\User;

class CloseAccountTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function testYouCanCloseAccount()
    {
        $user = factory(User::class)->create([
            'email' => 'test@foo.com'
        ]);
        $this->dropboxToken($user);
        $this->imgurToken($user);

        $this->actingAs($user)->visit('/settings')->press('Close account now');

        $this->assertTrue($user->trashed());
    }

    public function testYouCanCloseAccountWithImgurToken()
    {
        $user = factory(User::class)->create([
            'email' => 'test@foo.com'
        ]);
        $this->imgurToken($user);

        $this->actingAs($user)->visit('/settings')->press('Close account now');

        $this->assertTrue($user->trashed());
    }

    public function testYouCanCloseAccountWithDropboxToken()
    {
        $user = factory(User::class)->create([
            'email' => 'test@foo.com'
        ]);
        $this->dropboxToken($user);

        $this->actingAs($user)->visit('/settings')->press('Close account now');

        $this->assertTrue($user->trashed());
    }

    public function testYouCanCloseAccountWithoutTokens()
    {
        $user = factory(User::class)->create([
            'email' => 'test@foo.com'
        ]);

        $this->actingAs($user)->visit('/settings')->press('Close account now');

        $this->assertTrue($user->trashed());
    }
}
