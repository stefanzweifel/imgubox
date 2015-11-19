<?php

namespace ImguBox\Services\Dropbox;

use Dropbox\WriteMode;
use GrahamCampbell\Dropbox\DropboxManager;
use Illuminate\Contracts\Config\Repository as Config;
use ImguBox\Contracts\StorageProvider;
use ImguBox\Token;

class Client implements StorageProvider
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

    protected $writeMode;

    public function __construct(DropboxManager $dropbox, Config $config)
    {
        $this->dropbox   = $dropbox;
        $this->config    = $config;
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

    public function getToken()
    {
        return $this->token;
    }

    /**
     * Upload a file out of a string
     * @param  string $path
     * @param  string $data
     * @return void
     */
    public function description($path, $filename, $data)
    {
        return $this->dropbox->uploadFileFromString("$path/$filename", $this->writeMode, $data);
    }

    /**
     * Create Folder in Dropbox
     * @param  string $path
     * @return void
     */
    public function folder($path)
    {
        return $this->dropbox->createFolder($path);
    }

    /**
     * Upload a file out of a resource
     * @param  string $path
     * @param  resource $resource
     * @return void
     */
    public function file($path, $filename, $resource)
    {
        return $this->dropbox->uploadFile("$path/$filename", $this->writeMode, $resource);
    }

    /**
     * Set Access token for current user
     * @return void
     */
    protected function updateConfig()
    {
        return $this->config->set('dropbox.connections.main.token', $this->token->token);
    }
}
