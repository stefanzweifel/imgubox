<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use ImguBox\Provider;
use ImguBox\Token;
use ImguBox\User;

class PageTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testItLoadsMarketingView()
    {
        $this->visit('/')->see('Store your Imgur favorites to');
    }

    public function testItLoadsAboutPage()
    {
        $this->visit('about')->see('The project is open-source!');
    }

    public function testItLoadsDashboardView()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user)->visit('/')->see('You can manage your connections');
    }

    public function testItLoadsSettingsView()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)->visit('/settings')->see('Settings');
    }

    public function testItLoadsSettingsViewAndYouCanConnectToImgur()
    {
        $user = factory(User::class)->create();

        $dropbox = factory(Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(Provider::class, 'Dropbox')->create()->id
        ]);


        $this->actingAs($user)->visit('/settings')->see('Connect');
    }

    public function testItLoadsSettingsViewAndYouCanConnectToDropbox()
    {
        $user = factory(User::class)->create();
        $imgur = factory(Token::class)->create([
            'user_id' => $user->id,
            'provider_id' => factory(Provider::class, 'Imgur')->create()->id
        ]);

        $this->actingAs($user)->visit('/settings')->see('Connect');
    }

    public function testItLoadsSettingsViewAndYouCanDeleteTokens()
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
