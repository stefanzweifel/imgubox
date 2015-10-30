<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use ImguBox\Provider;
use ImguBox\Token;
use ImguBox\User;

class UserSettingsTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testItLoadsSettingsView()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)->visit('/settings')->see('Settings');
    }

    public function testYouCanConnectToImgur()
    {
        $user = factory(User::class)->create();

        $dropbox = factory(Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(Provider::class, 'Dropbox')->create()->id
        ]);


        $this->actingAs($user)->visit('/settings')->see('Connect');
    }

    public function testYouCanConnectToDropbox()
    {
        $user = factory(User::class)->create();
        $imgur = factory(Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(Provider::class, 'Imgur')->create()->id
        ]);

        $this->actingAs($user)->visit('/settings')->see('Connect');
    }

    public function testYouSeeDeleteButtons()
    {
        $user = factory(User::class)->create();
        $imgur = factory(Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(Provider::class, 'Imgur')->create()->id
        ]);

        $dropbox = factory(Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(Provider::class, 'Dropbox')->create()->id
        ]);

        $this->actingAs($user)->visit('/settings')->see('Delete');
    }

    public function testYouCanDeleteDropboxToken()
    {
        $user = factory(User::class)->create();

        $dropbox = factory(Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(Provider::class, 'Dropbox')->create()->id
        ]);

        $this->actingAs($user)->visit('/settings')->click('Delete');

        $this->assertEquals(null, $user->dropboxToken->first());
    }

    public function testYouCanDeleteImgurToken()
    {
        $user = factory(User::class)->create();

        $dropbox = factory(Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(Provider::class, 'Imgur')->create()->id
        ]);

        $this->actingAs($user)->visit('/settings')->click('Delete');

        $this->assertEquals(null, $user->imgurToken->first());
    }


}
