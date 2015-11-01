<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdatePasswordTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testUserCanUpdatePassword()
    {
        $this->beUser();

        $this->visit('/settings')
            ->type('password1234', 'password')
            ->type('password1234', 'password_confirmation')
            ->press('Update password')
            ->seePageIs('/settings')
            ->see('Your password was successfully updated');
    }

    public function testUserMustProvideInput()
    {
        $this->beUser();

        $this->visit('/settings')
            ->press('Update password')
            ->seePageIs('/settings')
            ->see('The password field is required.');
    }

    public function testUserMustEnterMatchingPasswords()
    {
        $this->beUser();

        $this->visit('/settings')
            ->type('password1234', 'password')
            ->type('1234password', 'password_confirmation')
            ->press('Update password')
            ->seePageIs('/settings')
            ->see('The password confirmation does not match.');
    }
}
