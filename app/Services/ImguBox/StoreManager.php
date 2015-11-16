<?php

namespace ImguBox\Services\ImguBox;

use ImguBox\Contracts\StorageProvider;
use ImguBox\Events\FavoriteStored;
use ImguBox\Services\Imgur\Client as ImgurClient;
use ImguBox\User;
use Imgur\Api\Model\Image;
use Imgur\Api\Model\Album;

class StoreManager
{
    /**
     * Instance of Storage Provider
     * @var ImguBox\Services\Dropbox\StorageInterface
     */
    protected $storageProvider;

    /**
     * @var ImguBox\Services\Imgur\Client
     */
    protected $imgurClient;

    /**
     * User Implementation
     * @var ImguBox\User;
     */
    protected $user;

    /**
     * Paht to folder, where favorite is stored
     * @var string
     */
    protected $folderName;

    /**
     * Set User
     * @param ImguBox\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return ImguBox\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return ImguBox\Contracts\StorageProvider
     */
    public function getProvider()
    {
        return $this->storageProvider;
    }

    /**
     * Set Storage Provider
     * @param StorageProvider $storageProvider
     */
    public function setProvider(StorageProvider $storageProvider)
    {
        $this->storageProvider = $storageProvider;
    }

    public function setImgurClient(ImgurClient $client)
    {
        $this->imgurClient = $client;
    }

    public function getImgurClient()
    {
        return $this->imgurClient;
    }

    /**
     * Create Foldername
     * @param mixed $favorite
     */
    protected function setFolderName($favorite)
    {
        if (property_exists($favorite, "title")) {
            if (is_null($favorite->getTitle())) {
                $this->folderName =  $favorite->getId();
            }
        }

        $this->folderName =  str_slug("{$favorite->getTitle()} {$favorite->getId()}");
    }

    /**
     * @return string
     */
    protected function getFolderName()
    {
        return "/{$this->folderName}";
    }

    /**
     * Create Folder in Storage Provider
     * @return void
     */
    protected function createFolder()
    {
        $this->storageProvider->folder("/{$this->getFoldername()}");
    }

    /**
     * Store Imgur Album
     * @param  Album $favorite
     * @return void
     */
    public function storeAlbum(Album $favorite)
    {
        $this->isProviderSet();
        $this->isUserSet();

        $this->setFolderName($favorite);

        $client = $this->getImgurClient();

        $images = $client->albumImages($favorite->getId());

        if ($images->getFavorites()->count() > 0) {

            $this->createFolder();

            foreach($images->getFavorites() as $key => $image) {
                $this->storeSingleImage($image, $key);
            }

            event(new \ImguBox\Events\AlbumStored);

            event(new FavoriteStored($favorite, $this->getUser()));
        }
    }

    /**
     * Store a single image
     * @param  Image $favorite
     * @return void
     */
    public function storeImage(Image $favorite)
    {
        $this->isProviderSet();
        $this->isUserSet();

        $this->setFolderName($favorite);

        $this->createFolder();
        $this->storeSingleImage($favorite);
    }

    /**
     * Store an actual Image
     * @param  Image $image
     * @param  integer $key   Keep sorting order in albums
     * @return void
     */
    protected function storeSingleImage(Image $image, $key = null)
    {
        $filename = $this->getFilename($image, $key);

        if ($this->hasDescription($image))
        {
            $this->storeDescription($image, $key);
        }

        if ($image->getAnimated())
        {
            $this->storeAnimated($image, $filename);
        }
        else {
            $this->storeSimpleImage($image, $filename);
        }

        event(new FavoriteStored($image, $this->getUser()));
    }


    /**
     * Determine Filename of a given Image
     * @param  Image $favorite
     * @param  integer $key     Array Index, used for sorting files
     * @return string
     */
    protected function getFilename(Image $favorite, $key = null)
    {
        $filename = pathinfo($favorite->getLink(), PATHINFO_BASENAME);

        if (!is_null($key)) {
            return "{$key} - {$filename}";
        }

        return $filename;
    }

    /**
     * Store Image Description
     * @param  Image $image
     * @return void
     */
    protected function storeDescription(Image $image, $key = null)
    {
        if (is_null($key)) {
            $filename = "{$image->getId()} - description.txt";
        }
        else {
            $filename = "$key - {$image->getId()} - description.txt";
        }

        $this->storageProvider->description(
            $this->getFolderName(),
            $filename,
            $image->getDescription()
        );

        event(new \ImguBox\Events\DescriptionStored);
    }

    /**
     * Store Animated GIFs
     * @param  Image $image
     * @param  string $filename
     * @return void
     */
    protected function storeAnimated(Image $image, $filename)
    {
        // Store GIF version
        $this->storeSimpleImage($image, $filename);

        // Store MP4 version
        $this->storageProvider->file(
            $this->getFolderName(),
            $filename,
            fopen(str_replace(".gif", ".mp4", $image->getLink()), "rb")
        );

        event(new \ImguBox\Events\AnimatedStored);
    }

    /**
     * Store a Single Image
     * @param  Image $image
     * @param  string $filename
     * @return void
     */
    protected function storeSimpleImage(Image $image, $filename)
    {
        $this->storageProvider->file(
            $this->getFolderName(),
            $filename,
            fopen($image->getLink(), "rb")
        );
    }


    /**
     * Determine if an Image has a description
     * @param  Image  $image
     * @return boolean
     */
    protected function hasDescription(Image $image)
    {
        return !empty($image->getDescription());
    }

    /**
     * Determin if Storage Provider is set
     * @return boolean
     */
    protected function isProviderSet()
    {
        if (is_null($this->getProvider()))
        {
            throw new \Exception("No StorageProvider defined.");
        }
    }

    /**
     * Determin if User is set
     * @return boolean
     */
    protected function isUserSet()
    {
        if (is_null($this->getUser()))
        {
            throw new \Exception("No User defined.");
        }
    }

}