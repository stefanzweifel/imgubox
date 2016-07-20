<?php

namespace ImguBox\Tests\Integrated\Authentication;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ImguBox\Tests\Support\FactoryTools;
use ImguBox\Tests\TestCase;

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
