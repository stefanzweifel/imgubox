<?php namespace ImguBox\Services;

use ImguBox\Log;
use ImguBox\Services\DropboxService;

class ImguBoxService {

    protected $folder;

    protected $dropbox;

    /**
     * Log Instance
     * @var ImguBox\Log
     */
    protected $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function setDropbox(DropboxService $dropbox)
    {
        $this->dropbox = $dropbox;
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    public function processImage($image)
    {
        $filename    = pathinfo($image->link, PATHINFO_BASENAME);
        $this->dropbox->uploadFile("/$this->folder/$filename", fopen($image->link,'rb'));

        $this->processAnimated($image);

        Log::create([
            'user_id' => $user->id,
            'imgur_id' => $image->id,
            'is_album' => false
        ]);

    }

    private function processAnimated($image)
    {
        // If GIF, store all types
        if ($image->animated === true) {

            // GIFV
            // $filename    = pathinfo($model->gifv, PATHINFO_BASENAME);
            // \Dropbox::uploadFile("/$folderName/$filename", $writeMode, fopen($model->gifv,'rb'));

            // WEBM
            // $filename    = pathinfo($model->webm, PATHINFO_BASENAME);
            // \Dropbox::uploadFile("/$folderName/$filename", $writeMode, fopen($model->webm,'rb'));

            // MP4
            $filename    = pathinfo($model->mp4, PATHINFO_BASENAME);
            $dropbox->uploadFile("/$this->folder/$filename", fopen($image->mp4,'rb'));

        }
    }

    public function processAlbum()
    {

    }

}