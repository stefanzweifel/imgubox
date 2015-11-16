<?php

namespace ImguBox\Contracts;

use ImguBox\Token;

interface StorageProvider
{

    public function setToken(Token $token);

    /**
     * Not Really used!
     * @return [type] [description]
     */
    public function getToken();

    public function folder($path);

    public function file($path, $filename, $resource);

    public function description($path, $filename, $data);
}