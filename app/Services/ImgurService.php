<?php namespace ImguBox\Services;

use GuzzleHttp\Client;

class ImgurService
{
    /**
     * Guzzle Instance
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * Available API Endpoints
     * @var array
     */
    protected $scopes = [
        "base_url" => "https://api.imgur.com"
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Request access and refresh_token from Imgur
     * @return object
     */
    public function getAccessToken($code)
    {
        $client = new $this->client([
            "base_url" => array_get($this->scopes, "base_url"),
        ]);

        $response = $client->post("oauth2/token", [
            "body" => [
                "grant_type"    => "authorization_code",
                "client_id"     => env("IMGUR_KEY"),
                "client_secret" => env("IMGUR_SECRET"),
                "code"          => $code
            ],
            "exceptions" => false
        ]);

        $body = $response->getBody();
        return json_decode($body);
    }

}
