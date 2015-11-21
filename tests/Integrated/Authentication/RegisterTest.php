<?php

namespace ImguBox\Tests\Integrated\Authentication;

use ImguBox\Tests\TestCase;
use ImguBox\Tests\Support\FactoryTools;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use ImguBox\User;

class RegisterTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

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
