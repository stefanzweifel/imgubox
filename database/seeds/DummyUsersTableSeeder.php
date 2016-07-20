<?php

use Illuminate\Database\Seeder;
use ImguBox\User;

class DummyUsersTableSeeder extends Seeder
{
    public function run()
    {
        factory(User::class)->create();
    }
}
