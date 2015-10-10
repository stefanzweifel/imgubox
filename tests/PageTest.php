<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
        $user = factory(ImguBox\User::class)->create();
        $this->actingAs($user)->visit('/')->see('Setup');
    }

    public function testItLoadsSettingsView()
    {
        $user = factory(ImguBox\User::class)->create();
        $this->actingAs($user)->visit('/settings')->see('Settings');
    }
}
