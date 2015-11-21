<?php

namespace ImguBox\Tests\Unit\Service\ImguBox;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use ImguBox\Events\AlbumStored;
use ImguBox\Events\AnimatedStored;
use ImguBox\Events\DescriptionStored;
use ImguBox\Events\FavoriteStored;
use ImguBox\Services\Dropbox\Client as DropboxClient;
use ImguBox\Services\ImguBox\StoreManager;
use ImguBox\Tests\Support\FactoryTools;
use ImguBox\Tests\TestCase;
use ImguBox\User;
use Mockery;

class StoreManagerTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function test_its_of_type_storemanager()
    {
        $manager = app(StoreManager::class);

        $this->assertInstanceOf(StoreManager::class, $manager);
    }

    public function test_it_sets_provider()
    {
        $dropbox = Mockery::mock(DropboxClient::class);

        $manager = app(StoreManager::class);
        $manager->setProvider($dropbox);

        $this->assertNotEmpty($manager->getProvider());
    }

    /**
     * @expectedException Exception
     */
    public function test_it_throws_exception_if_no_provider_is_given()
    {
        $image = $this->image();

        $manager = app(StoreManager::class);
        $manager->storeImage($image);

        $this->setExpectedException('Exception');
    }

    public function test_it_stores_a_single_image()
    {
        $this->expectsEvents(FavoriteStored::class);

        $user         = $this->user();
        $dropboxToken = $this->dropboxToken($user);

        $dropbox = $this->dropbox();
        $dropbox->shouldReceive('setToken');
        $dropbox->shouldReceive('folder');
        $dropbox->shouldReceive('file');
        $dropbox->shouldReceive('description');
        $dropbox->setToken($dropboxToken);

        $manager = app(StoreManager::class);
        $manager->setProvider($dropbox);
        $manager->setUser($user);

        $image = $this->image();

        $manager->storeImage($image);
    }

    public function test_it_stores_album()
    {
        $this->expectsEvents(AlbumStored::class);

        $user         = $this->user();
        $dropboxToken = $this->dropboxToken($user);

        $albumImages = new \ImguBox\Entities\Favorites([$this->image(), $this->image()], $user);

        $imgurClient = Mockery::mock(\ImguBox\Services\Imgur\Client::class);
        $imgurClient->shouldReceive("getUser")->andReturn($user);
        $imgurClient->shouldReceive("albumImages")->andReturn($albumImages);

        $dropbox = $this->dropbox();
        $dropbox->shouldReceive('setToken')->shouldReceive('folder');
        $dropbox->shouldReceive('file')->times(2);
        $dropbox->shouldReceive('description');
        $dropbox->setToken($dropboxToken);

        $manager = app(StoreManager::class);
        $manager->setProvider($dropbox);
        $manager->setUser($user);
        $manager->setImgurClient($imgurClient);

        $image = $this->album();

        $manager->storeAlbum($image);
    }

    public function test_it_stores_animated()
    {
        $this->expectsEvents(AnimatedStored::class);

        $user         = $this->user();
        $dropboxToken = $this->dropboxToken($user);

        $dropbox = $this->dropbox();
        $dropbox->shouldReceive('setToken');
        $dropbox->shouldReceive('folder');
        $dropbox->shouldReceive('file');
        $dropbox->shouldReceive('description');
        $dropbox->setToken($dropboxToken);

        $manager = app(StoreManager::class);
        $manager->setProvider($dropbox);
        $manager->setUser($user);

        $gif = $this->gif();

        $manager->storeImage($gif);
    }

    public function test_it_stores_description()
    {
        $this->expectsEvents(DescriptionStored::class);

        $user         = $this->user();
        $dropboxToken = $this->dropboxToken($user);

        $dropbox = $this->dropbox();
        $dropbox->shouldReceive('setToken');
        $dropbox->shouldReceive('folder');
        $dropbox->shouldReceive('file');
        $dropbox->shouldReceive('description');
        $dropbox->setToken($dropboxToken);

        $manager = app(StoreManager::class);
        $manager->setProvider($dropbox);
        $manager->setUser($user);

        $image = $this->image();

        $manager->storeImage($image);
    }


    // ----

    protected function dropbox()
    {
        return Mockery::mock(DropboxClient::class);
    }
}
