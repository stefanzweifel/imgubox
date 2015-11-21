<?php

namespace ImguBox\Tests\Integrated\UserSettings;

use ImguBox\Tests\TestCase;
use ImguBox\Tests\Support\FactoryTools;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use ImguBox\Provider;
use ImguBox\Token;
use ImguBox\User;

class ManageTokensTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function testItLoadsSettingsView()
    {
        $this->beUser();
        $this->visit('/settings')->see('Settings');
    }

    public function testYouCanConnectToImgur()
    {
        $user = $this->user();
        factory(Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(Provider::class, 'Dropbox')->create()->id
        ]);

        $this->actingAs($user)->visit('/settings')->see('Connect');
    }

    public function testYouCanConnectToDropbox()
    {
        $user = factory(User::class)->create();
        $this->imgurToken($user);

        $this->actingAs($user)->visit('/settings')->see('Connect');
    }

    public function testYouSeeDeleteButtons()
    {
        $user = factory(User::class)->create();
        $this->imgurToken($user);
        $this->dropboxToken($user);

        $this->actingAs($user)->visit('/settings')->see('Delete');
    }

    public function testYouCanDeleteDropboxToken()
    {
        $user = factory(User::class)->create();
        $this->dropboxToken($user);

        $this->actingAs($user)->visit('/settings')->click('Delete');
        $this->assertEquals(null, $user->dropboxToken->first());
    }

    public function testYouCanDeleteImgurToken()
    {
        $user = factory(User::class)->create();
        $this->imgurToken($user);

        $this->actingAs($user)->visit('/settings')->click('Delete');
        $this->assertEquals(null, $user->imgurToken->first());
    }
}
