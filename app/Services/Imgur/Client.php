<?php

namespace ImguBox\Services\Imgur;

use ImguBox\Entities\Favorites;
use ImguBox\User;
use Imgur\Client as ImgurClient;
use Imgur\Pager\BasicPager;

class Client extends ImgurClient
{
    /**
     * @var Imgur\Client
     */
    protected $client;

    /**
     * @var ImguBox\User
     */
    protected $user;

    public function __construct(ImgurClient $client)
    {
        parent::__construct();
        $this->setClient($client);
        $this->setupClient();
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        $this->setupToken();
        $this->checkTokenExpiration();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns all favorites of an account
     * @return collection
     */
    public function favorites()
    {
        // The Favorites API doesn't return the full array of favorites
        // with our current API-package. So we currently fetch 5 * 60 favorites

        $favorites = [];
        $page      = 0;
        $maxPages  = 5; // Increase to get more favorites

        while ($page <= $maxPages) {
            $pager = new BasicPager($page);
            $accountFavorites = $this->getClient()->api('account', $pager)->favorites();

            if (!empty($accountFavorites)) {
                $favorites = array_merge($favorites, $accountFavorites);
            }

            $page++;
        }

        return new Favorites($favorites, $this->getUser());
    }

    /**
     * Get Album Images for a given Album Id
     * @param  string $albumId
     * @return Collection
     */
    public function albumImages($albumId)
    {
        $images = $this->getClient()->api('album')->albumImages($albumId);

        return new Favorites($images, $this->getUser());
    }

    /**
     * Setup Imgur Client
     * @return void
     */
    protected function setupClient()
    {
        $this->client->setOption('client_id', config("services.imgur.client_id"));
        $this->client->setOption('client_secret', config("services.imgur.client_secret"));
    }

    /**
     * Set Cliet
     * @param ImgurClient $client
     */
    protected function setClient(ImgurClient $client)
    {
        $this->client = $client;
    }

    /**
     * Setup Imgur Token
     * @return void
     */
    protected function setupToken()
    {
        $token['data'] = [
            'access_token'  => $this->getUser()->imgurToken->token,
            'refresh_token' => $this->getUser()->imgurToken->refresh_token,
            'expires_in'    => 3600 - $this->getUser()->imgurToken->updated_at->diffInSeconds(),
            'created_at'    => $this->getUser()->imgurToken->updated_at->timestamp
        ];

        $this->client->setAccessToken($token);
    }

    /**
     * If token is expired, refresh Token and store credentials
     * @return void
     */
    protected function checkTokenExpiration()
    {
        if ($this->client->checkAccessTokenExpired()) {
            $result = $this->client->refreshToken();

            $this->user->imgurToken->update([
                'token'         => array_get($result, 'access_token'),
                'refresh_token' => array_get($result, 'refresh_token')
            ]);
        }
    }
}
