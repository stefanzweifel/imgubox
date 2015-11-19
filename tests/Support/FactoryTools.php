<?php

namespace ImguBox\Tests\Support;

use ImguBox\Log;
use ImguBox\Provider;
use ImguBox\Token;
use ImguBox\User;
use Faker\Generator as Faker;

trait FactoryTools
{
    public function user()
    {
        return factory(User::class)->create();
    }

    public function imgurToken(User $user)
    {
        return factory(Token::class)->create([
            'user_id'     => $user->id,
            'provider_id' => $this->imgurProvider()->id
        ]);
    }

    public function dropboxToken(User $user)
    {
        return factory(Token::class)->create([
            'user_id'     => $user->id,
            'provider_id' => $this->dropboxProvider()->id
        ]);
    }

    public function imgurLog(User $user, $imgurId)
    {
        return factory(Log::class)->create([
            'user_id'  => $user->id,
            'imgur_id' => $imgurId
        ]);
    }

    public function imgurProvider()
    {
        return factory(Provider::class, 'Imgur')->create();
    }

    public function dropboxProvider()
    {
        return factory(Provider::class, 'Dropbox')->create();
    }


    public function setupUsers($count = 1)
    {
        for ($i=1; $i <= $count; $i++) {
            $user = $this->user();
            $this->imgurToken($user);
            $this->dropboxToken($user);
        }
    }

    public function image()
    {
        return new \Imgur\Api\Model\Image($this->imageParameters());
    }

    public function album()
    {
        $faker = app(Faker::class);

        $parameters = [
            "id"           => str_random(5),
            "title"        => $faker->word(10),
            "description"  => $faker->sentence(),
            "cover"        => "",
            "account_url"  => "",
            "privacy"      => "",
            "layout"       => "",
            "views"        => $faker->randomNumber(),
            "images_count" => $faker->randomNumber(),
            "link__"         => "http://i.imgur.com/".str_random(5),
            "link"         => "http://i.imgur.com/CBPafr2.jpg",
            "datetime"     => $faker->dateTime(),
            "images" => [
                $this->imageParameters(),
                $this->imageParameters()
            ]
        ];

        return new \Imgur\Api\Model\Album($parameters);
    }

    protected function imageParameters()
    {
        $faker = app(Faker::class);

        return [
            "id"          => str_random(5),
            "title"       => $faker->word(10),
            "description" => $faker->sentence(),
            "animated"    => false,
            "width"       => $faker->randomNumber(),
            "height"      => $faker->randomNumber(),
            "size"        => $faker->randomNumber(),
            "views"       => $faker->randomNumber(),
            "bandwidth"   => $faker->randomNumber(),
            "section"     => $faker->word(),
            "link__"        => "http://i.imgur.com/".str_random(5),
            "link"        => "http://i.imgur.com/CBPafr2.jpg",
            "datetime"    => $faker->dateTime()
        ];
    }

    public function gif()
    {
        return new \Imgur\Api\Model\Image($this->gifParameters());
    }

    public function gifParameters()
    {
        $faker = app(Faker::class);

        return [
            "id"          => str_random(5),
            "title"       => $faker->word(10),
            "description" => $faker->sentence(),
            "animated"    => true,
            "width"       => $faker->randomNumber(),
            "height"      => $faker->randomNumber(),
            "size"        => $faker->randomNumber(),
            "views"       => $faker->randomNumber(),
            "bandwidth"   => $faker->randomNumber(),
            "section"     => $faker->word(),
            "link"        => "http://i.imgur.com/D4eQpCp.gif",
            "mp4"         => "http://i.imgur.com/D4eQpCp.mp4",
            "datetime"    => $faker->dateTime()
        ];
    }
}
