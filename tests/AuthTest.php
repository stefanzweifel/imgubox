<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testItLoadsLoginView()
    {
        $this->visit('auth/login')->see('Login');
    }

    public function testYouCanLogin()
    {
        $user = factory(ImguBox\User::class)->create([
            'email' => 'test@foo.com',
            'password' => bcrypt('password1234'
        )]);

        $this->visit('/auth/login')
             ->type('test@foo.com', 'email')
             ->type('password1234', 'password')
             ->press('Login')
             ->seePageIs('/home')
             ->see('Setup');
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
        $user = factory(ImguBox\User::class)->create([
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
        $this->visit('auth/register')->see('Register');;
    }

    public function testYouCanRegister()
    {
        $this->visit('/auth/register')
             ->type('test@foo.com', 'email')
             ->type('password1234', 'password')
             ->type('password1234', 'password_confirmation')
             ->press('Register')
             ->seePageIs('/home')
             ->see('Setup')
             ->seeInDatabase('users', ['email' => 'test@foo.com']);
    }

    public function testItLoadsPasswordResetView()
    {
        $this->visit('password/email')->see('Reset password');
    }

}
