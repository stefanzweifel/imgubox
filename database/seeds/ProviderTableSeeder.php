<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;
use ImguBox\Provider;

class ProviderTableSeeder extends Seeder {

    public function run()
    {
        Provider::create([
            'name' => 'Imgur',
            'short_name' => 'imgur',
            'is_storage' => 0
        ]);

        Provider::create([
            'name' => 'Dropbox',
            'short_name' => 'dropbox',
            'is_storage' => 1
        ]);

    }

}