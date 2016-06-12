<?php

use Illuminate\Database\Seeder;
use Laracasts\TestDummy\Factory as TestDummy;

class DummyUsersTableSeeder extends Seeder
{
    public function run()
    {
        TestDummy::times(5)->create('ImguBox\User');
    }
}
