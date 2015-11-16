<?php

namespace ImguBox\Tests\Integrated;

use ImguBox\Tests\TestCase;
use ImguBox\Tests\Support\FactoryTools;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use ImguBox\User;

class AuthTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function testItLoadsLoginView()
    {
        $this->visit('auth/login')->see('Login');
    }

    public function testYouCanLogin()
    {
        $user = factory(User::class)->create([
            'email' => 'test@foo.com',
            'password' => bcrypt('password1234')
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
        $user = factory(User::class)->create([
            'email' => 'test@foo.com',
            'password' => bcrypt('fooBar'
        )]);

        $this->visit('/auth/login')
             ->type('test@foo.com', 'email')
             ->type('password1234', 'password')
             ->press('Login')
             ->seePageIs('/auth/login')
             ->see('These credentials do not match our records.');
    }

    public function testItLoadsRegisterView()
    {
        $this->visit('auth/register')->see('Register');
    }

    public function testYouCanRegister()
    {
        $this->visit('/auth/register')
             ->type('test@foo.com', 'email')
             ->type('password1234', 'password')
             ->type('password1234', 'password_confirmation')
             ->press('Register')
             ->seePageIs('/home')
             ->see('You can manage your connections')
             ->seeInDatabase('users', ['email' => 'test@foo.com']);
    }

    public function testItLoadsPasswordResetView()
    {
        $this->visit('password/email')->see('Reset password');
    }
}
