<?php

namespace ImguBox\Tests\Integrated\Authentication;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ImguBox\Tests\Support\FactoryTools;
use ImguBox\Tests\TestCase;
use ImguBox\User;

class LoginTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function testItLoadsLoginView()
    {
        $this->visit('auth/login')->see('Login');
    }

    public function testYouCanLogin()
    {
        factory(User::class)->create([
            'email'    => 'test@foo.com',
            'password' => bcrypt('password1234'),
        ]);

        $this->visit('/auth/login')
             ->type('test@foo.com', 'email')
             ->type('password1234', 'password')
             ->press('Login')
             ->seePageIs('/home')
             ->see('You can manage your connections');
    }

    public function testYouCanNotLoginIfUserDoesntExist()
    {
        $this->visit('/auth/login')
             ->type('test@foo.com', 'email')
             ->type('password1234', 'password')
             ->press('Login')
             ->seePageIs('/auth/login')
             ->see('These credentials do not match our records.');
    }

    public function testYouCAnNotLoginIfPasswordDoesntMatch()
    {
        factory(User::class)->create([
            'email'    => 'test@foo.com',
            'password' => bcrypt('fooBar'
        ), ]);

        $this->visit('/auth/login')
             ->type('test@foo.com', 'email')
             ->type('password1234', 'password')
             ->press('Login')
             ->seePageIs('/auth/login')
             ->see('These credentials do not match our records.');
    }
}
