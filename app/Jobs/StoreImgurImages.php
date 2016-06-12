<?php

namespace ImguBox\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ImguBox\Contracts\StorageProvider;
use ImguBox\Services\ImguBox\StoreManager;
use ImguBox\Services\Imgur\Client as ImgurClient;
use ImguBox\User;

class StoreImgurImages extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * User.
     *
     * @var ImguBox\User
     */
    protected $user;

    /**
     * Imgur Favorite.
     *
     * @var mixed
     */
    protected $favorite;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $base64Favorite)
    {
        $this->user = $user;
        $this->favorite = unserialize(base64_decode($base64Favorite));
    }

    public function handle(StorageProvider $storageClient, StoreManager $manager, ImgurClient $imgurClient)
    {
        $imgurClient->setUser($this->user);

        $storageClient->setToken($this->user->dropboxToken);

        $manager->setProvider($storageClient);
        $manager->setUser($this->user);
        $manager->setImgurClient($imgurClient);

        if ($this->favorite->getIsAlbum()) {
            $manager->storeAlbum($this->favorite);
        } else {
            $manager->storeImage($this->favorite);
        }
    }
}
