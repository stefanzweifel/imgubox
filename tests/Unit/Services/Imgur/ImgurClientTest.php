<?php

namespace ImguBox\Tests\Unit\Service\Imgur;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ImguBox\Entities\Favorites;
use ImguBox\Services\Imgur\Client;
use ImguBox\Tests\Support\FactoryTools;
use ImguBox\Tests\TestCase;
use ImguBox\User;
use Imgur\Client as ImgurClient;
use Mockery;

class ImgurClientTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function test_its_of_type_client()
    {
        $client = app(Client::class);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_it_builds_client()
    {
        $client = app(Client::class);

        $this->assertAttributeNotEmpty('client', $client);
        $this->assertInstanceOf(ImgurClient::class, $client->getClient());
    }

    public function test_it_throws_exception_if_no_user_is_set()
    {
        // $client = app(Client::class);

        // Todo
    }

    public function test_it_setups_user_and_token()
    {
        $client = app(Client::class);
        $user = $this->user();
        $imgurToken = $this->imgurToken($user);

        $client->setUser($user);

        $this->assertInstanceOf(User::class, $client->getUser());
        $this->assertEquals(
            $imgurToken->token,
            $client->getUser()->imgurToken->token
        );
    }

    public function test_it_refreshs_expired_token()
    {

        // Add expires_in column to Token Table
        // updated_at will always be updated when saving!

        $newUpdateTimeStamp = \Carbon\Carbon::yesterday();
        $client = app()->make(Client::class);
        $user = $this->user();
        $imgurToken = $this->imgurToken($user);
        $imgurToken->update([
            'updated_at' => $newUpdateTimeStamp,
        ]);

        $client->setUser($user);

        $this->assertNotEquals(
            $newUpdateTimeStamp,
            $client->getUser()->imgurToken->updated_at
        );
    }

    public function test_it_returns_favorites()
    {
        $user = $this->user();
        $imgurToken = $this->imgurToken($user);

        // Mock Imgur Image Model
        $imageOne = Mockery::mock("Imgur\Api\Model\Image");
        $imageOne->shouldReceive('getId')->andReturn('CBPafr2');
        $imageTwo = Mockery::mock("Imgur\Api\Model\Image");
        $imageTwo->shouldReceive('getId')->andReturn('hN1p2');

        // Mock underlying Api/Account Class
        $apiAccount = Mockery::mock("Api\Account");
        $apiAccount->shouldReceive('favorites')->andReturn([$imageOne, $imageTwo], null);

        // Mock underlying Imgur/Client
        $service = Mockery::mock('Imgur\Client');
        $service->shouldReceive('setOption')->times(2);
        $service->shouldReceive('setAccessToken')->once();
        $service->shouldReceive('checkAccessTokenExpired')->once()->andReturn(false);
        $service->shouldReceive('api')->andReturn($apiAccount);


        $client = new Client($service);
        $client->setUser($user);

        $favorites = $client->favorites();

        $this->assertEquals(2, $favorites->getFavorites()->count());
        $this->assertInstanceOf(Favorites::class, $favorites);
        $this->assertNotEmpty($favorites);
    }

    public function test_it_returns_only_unproccessed_favorites()
    {
        $user = $this->user();
        $imgurToken = $this->imgurToken($user);

        $this->imgurLog($user, 'CBPafr2');

        // Mock Imgur Image Model
        $imageOne = Mockery::mock("Imgur\Api\Model\Image");
        $imageOne->shouldReceive('getId')->andReturn('CBPafr2');

        $imageTwo = Mockery::mock("Imgur\Api\Model\Image");
        $imageTwo->shouldReceive('getId')->andReturn('hN1p2');

        // Mock underlying Api/Account Class
        $apiAccount = Mockery::mock("Api\Account");
        $apiAccount->shouldReceive('favorites')->andReturn([$imageOne, $imageTwo], null);

        // Mock underlying Imgur/Client
        $service = Mockery::mock('Imgur\Client');
        $service->shouldReceive('setOption')->times(2);
        $service->shouldReceive('setAccessToken')->once();
        $service->shouldReceive('checkAccessTokenExpired')->once()->andReturn(false);
        $service->shouldReceive('api')->andReturn($apiAccount);

        $client = new Client($service);
        $client->setUser($user);

        $favorites = $client->favorites();

        $this->assertEquals(1, $favorites->getFavorites()->count());
        $this->assertInstanceOf(Favorites::class, $favorites);
        $this->assertNotEmpty($favorites);
    }

    public function test_it_returns_images_collection_for_an_album_id()
    {
        $user = $this->user();
        $imgurToken = $this->imgurToken($user);

        // Mock Imgur Image Model
        $imageOne = Mockery::mock("Imgur\Api\Model\Image");
        $imageOne->shouldReceive('getId')->andReturn('vP4DQ4s');

        $imageTwo = Mockery::mock("Imgur\Api\Model\Image");
        $imageTwo->shouldReceive('getId')->andReturn('2Sg3KmE');

        // Mock underlying Api/Account Class
        $apiAccount = Mockery::mock("Api\Album");
        $apiAccount->shouldReceive('albumImages')->andReturn([$imageOne, $imageTwo]);

        // Mock underlying Imgur/Client
        $service = Mockery::mock('Imgur\Client');
        $service->shouldReceive('setOption')->times(2);
        $service->shouldReceive('setAccessToken')->once();
        $service->shouldReceive('checkAccessTokenExpired')->once()->andReturn(false);
        $service->shouldReceive('api')->andReturn($apiAccount);

        $client = new Client($service);
        $client->setUser($user);

        $images = $client->albumImages('hN1p2');

        $this->assertEquals(2, $images->getFavorites()->count());
        $this->assertNotEmpty($images);
    }
}
