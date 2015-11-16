<?php

namespace ImguBox\Tests\Integrated;

use ImguBox\Tests\TestCase;
use ImguBox\Tests\Support\FactoryTools;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
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
        $this->beUser();
        $this->visit('/')->see('You can manage your connections');
    }

}
