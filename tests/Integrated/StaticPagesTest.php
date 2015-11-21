<?php

namespace ImguBox\Tests\Integrated;

use ImguBox\Tests\TestCase;
use ImguBox\Tests\Support\FactoryTools;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use ImguBox\User;

class StaticPagesTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

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
        $this->beUser();
        $this->visit('/')->see('You can manage your connections');
    }

    public function testItLoadsDashboardViewAndDisplaysLastLog()
    {
        $user = $this->user();

        $this->imgurToken($user);
        $this->dropboxToken($user);

        $this->logs($user, 5);

        $lastSync = $user->logs()->latest()->first(['created_at'])->created_at->format('d.m.Y H:i:s');

        $this->actingAs($user)->visit("/")->see("Last successfull sync")->see($lastSync);
    }
}
