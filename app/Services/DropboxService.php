<?php namespace ImguBox\Services;

use Illuminate\Contracts\Encryption\Encrypter;
use GrahamCampbell\Dropbox\DropboxManager;
use Illuminate\Contracts\Config\Repository as Config;
use Dropbox\WriteMode;
use ImguBox\Token;

class DropboxService
{
    /**
     * DropboxManager Instance
     * @var GrahamCampbell\Dropbox\DropboxManager;
     */
    protected $dropbox;

    /**
     * Config Instance
     * @var Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Encrypter
     * @var Illuminate\Contracts\Encryption\
     */
    protected $crypt;

    protected $writeMode;

    /**
     * Token Instance
     * @var ImguBox\Token
     */
    protected $token;

    public function __construct(DropboxManager $dropbox, Config $config, Encrypter $crypt)
    {
        $this->dropbox   = $dropbox;
        $this->config    = $config;
        $this->crypt     = $crypt;
        $this->writeMode = WriteMode::force();
    }

    /**
     * Set Dropbox Token
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
        $this->updateConfig();
    }

    /**
     * Create Folder in Dropbox
     * @param  string $path
     * @return void
     */
    public function createFolder($path)
    {
        return $this->dropbox->createFolder($path);
    }

    /**
     * Upload a file out of a string
     * @param  string $path
     * @param  string $data
     * @return void
     */
    public function uploadDescription($path, $data)
    {
        return $this->dropbox->uploadFileFromString($path, $this->writeMode, $data);
    }

    /**
     * Upload a file out of a resource
     * @param  string $path
     * @param  resource $resource
     * @return void
     */
    public function uploadFile($path, $resource)
    {
        return $this->dropbox->uploadFile($path, $this->writeMode, $resource);
    }

    /**
     * Set Access token for current user
     * @return void
     */
    private function updateConfig()
    {
        return $this->config->set('dropbox.connections.main.token', $this->readToken());
    }

    /**
     * Encrypt Access Token
     * @return string
     */
    private function readToken()
    {
        return $this->crypt->decrypt($this->token->token);
    }
}
