<?php

namespace ImguBox\Tests\Unit;

use ImguBox\Tests\TestCase;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Subscriber\Mock;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use ImguBox\Services\ImgurService;
use ImguBox\User;

class GuzzleImgurTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    protected $mockPath = "./tests/mock/responses";

    protected function getClient()
    {
        return app(ImgurService::class);
    }

    protected function mockClient($mockFile)
    {
        $client = $this->getClient();

        // Add mocked response to Client
        $mock = new Mock(["$this->mockPath/$mockFile"]);
        $client->getClient()->getEmitter()->attach($mock);

        return $client;
    }

    protected function getBody(Response $response)
    {
        return json_decode($response->getBody());
    }

    public function testImgurAuthenticationFails()
    {
        // Implement Test
    }

    public function testImgurReturnsRefreshToken()
    {
        $client = $this->mockClient("imgur_refresh_token_success.txt");

        $response = $client->image("CBPafr2");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("bearer", $this->getBody($response)->token_type);
        $this->assertEquals("3600", $this->getBody($response)->expires_in);
    }

    public function testImgurReturnsFavoritesArray()
    {
        $user   = factory(User::class)->create();
        $client = $this->mockClient("imgur_favorites.txt");
        $client->setUser($user);

        $response = $client->favorites();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->getBody($response)->success);
    }

    public function testImgurReturnsImageObject()
    {
        $client = $this->mockClient("imgur_image_success.txt");

        $response = $client->image("CBPafr2");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->getBody($response)->success);
    }

    public function testImgurFailsToFindImageObject()
    {
        $client = $this->mockClient("imgur_image_error.txt");

        $response = $client->image("CBPafr2");

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(false, $this->getBody($response)->success);
    }

    public function testImgurReturnsAlbumObject()
    {
        $client = $this->mockClient("imgur_album_success.txt");

        $response = $client->gallery("hN1p2");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->getBody($response)->success);
        $this->assertEquals("hN1p2", $this->getBody($response)->data->id);
    }

    public function testImgurFailsToFindAlbumObject()
    {
        $client = $this->mockClient("imgur_album_error.txt");

        $response = $client->gallery("hN1p2o");

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(false, $this->getBody($response)->success);
    }

}