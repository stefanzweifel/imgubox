<?php namespace ImguBox\Services;

use Illuminate\Contracts\Auth\Guard;
use GuzzleHttp\Client;

use ImguBox\User;

class ImgurService {

    /**
     * Guzzle Instance
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * Guard Instance
     * @var Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    protected $scopes = [
        'base_url' => 'https://api.imgur.com/',
        'version'  => '/3/',
        'account'  => [
            'favorites' => 'account/__USERNAME__/favorites'
        ],
        'gallery' => [
            'image' => 'gallery/image/__ID__',
            'album' => 'gallery/album/__ID__'
        ]
    ];

    public function __construct(Client $client, Guard $auth)
    {
        $this->client = $client;
        $this->auth   = $auth;

        $this->prepareClient($this->auth->user());
    }

    public function favorites()
    {
        $version = array_get($this->scopes, 'version');
        $favorites = array_get($this->scopes, 'account.favorites');
        $favorites = $this->replaceUsername($favorites);

        $response = $this->client->get($version.$favorites);

        $body = $response->getBody();
        return json_decode($body)->data;
    }


    public function image($id)
    {
        $version = array_get($this->scopes, 'version');
        $image = array_get($this->scopes, 'gallery.image');
        $image = $this->replaceId($image, $id);

        $response = $this->client->get($version.$image);

        $body = $response->getBody();
        return json_decode($body)->data;
    }

    public function gallery()
    {

    }

    public function refreshToken()
    {
        // https://api.imgur.com/oauth2/token

    }

    private function prepareClient(User $user)
    {
        $token = $user->tokens()->where('provider_id', 1)->first()->token;

        $client = new $this->client([
            'base_url' => array_get($this->scopes, 'base_url'),
            'defaults' => [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ],
            ]
        ]);

        $this->client = $client;
    }

    private function replaceUsername($string)
    {
        return str_replace(
            '__USERNAME__',
            $this->auth->user()->imgur_username,
            $string
        );
    }

    private function replaceId($string, $id)
    {
        return str_replace(
            '__ID__',
            $id,
            $string
        );
    }

}