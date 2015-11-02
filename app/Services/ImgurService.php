<?php namespace ImguBox\Services;

use GuzzleHttp\Client;
use ImguBox\User;
use ImguBox\Token;

class ImgurService
{
    /**
     * Guzzle Instance
     * @var GuzzleHttp\Client
     */
    protected $client;


    /**
     * User Instance
     * @var ImguBox\User
     */
    protected $user;

    /**
     * Token Instance
     * @var ImguBox\Token
     */
    protected $token;

    /**
     * Available API Endpoints
     * @var array
     */
    protected $scopes = [
        'base_url' => 'https://api.imgur.com/',
        'version'  => '/3/',
        'account'  => [
            'favorites' => 'account/__USERNAME__/favorites'
        ],
        'gallery' => [
            'image' => 'image/__ID__',
            'album' => 'album/__ID__'
        ]
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Set User
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Set Token
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
        $this->prepareClient($token);
    }

    /**
     * Get Users Favorites
     * @return object
     */
    public function favorites()
    {
        $version   = array_get($this->scopes, 'version');
        $favorites = array_get($this->scopes, 'account.favorites');
        $favorites = $this->replaceUsername($favorites);

        return $this->client->get($version.$favorites, ["exceptions" => false]);
    }

    /**
     * Get Image Model
     * @param  string $id
     * @return object
     */
    public function image($id)
    {
        $version  = array_get($this->scopes, 'version');
        $image    = array_get($this->scopes, 'gallery.image');
        $image    = $this->replaceId($image, $id);

        return $this->client->get($version.$image, ["exceptions" => false]);
    }

    /**
     * Get Gallery Model
     * @param  string $id
     * @return object
     */
    public function gallery($id)
    {
        $version  = array_get($this->scopes, 'version');
        $image    = array_get($this->scopes, 'gallery.album');
        $image    = $this->replaceId($image, $id);

        return $this->client->get($version.$image, ["exceptions" => false]);
    }

    /**
     * Refresh an access_token
     * @param  User   $user
     * @return object
     */
    public function refreshToken()
    {
        $response = $this->client->post('oauth2/token', [
            'body' => [
                'refresh_token' => $this->token->refresh_token,
                'client_id'     => env('IMGUR_KEY'),
                'client_secret' => env('IMGUR_SECRET'),
                'grant_type'    => 'refresh_token'
            ],
            'exceptions' => false
        ]);

        return json_decode($response->getBody());
    }

    /**
     * Request access and refresh_token from Imgur
     * @return object
     */
    public function getAccessToken($code)
    {
        $client = new $this->client([
            'base_url' => array_get($this->scopes, 'base_url'),
        ]);

        $response = $client->post('oauth2/token', [
            'body' => [
                'grant_type'    => 'authorization_code',
                'client_id'     => env('IMGUR_KEY'),
                'client_secret' => env('IMGUR_SECRET'),
                'code'          => $code
            ],
            'exceptions' => false
        ]);

        $body = $response->getBody();
        return json_decode($body);
    }

    /**
     * Setup Client
     * Set necessary headers
     * @param  Token  $token
     * @return GuzzleHttp\Client
     */
    private function prepareClient(Token $token)
    {
        $client = new $this->client([
            'base_url' => array_get($this->scopes, 'base_url'),
            'defaults' => [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token->token
                ],
            ]
        ]);

        $this->client = $client;
    }

    /**
     * Replace string with given Imgur Username
     * @param  string $string
     * @return string
     */
    private function replaceUsername($string)
    {
        return str_replace(
            '__USERNAME__',
            $this->user->imgur_username,
            $string
        );
    }

    /**
     * Replace string with given Imgur Model-ID
     * @param  string $string
     * @param  string $id
     * @return string
     */
    private function replaceId($string, $id)
    {
        return str_replace(
            '__ID__',
            $id,
            $string
        );
    }
}
