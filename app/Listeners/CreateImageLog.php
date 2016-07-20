<?php

namespace ImguBox\Listeners;

use ImguBox\Events\FavoriteStored;
use ImguBox\Log;

class CreateImageLog
{
    protected $log;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    /**
     * Handle the event.
     *
     * @param FavoriteStored $event
     *
     * @return void
     */
    public function handle(FavoriteStored $event)
    {
        return $this->log->create([
            'user_id'  => $event->user->id,
            'imgur_id' => $event->image->getId(),
            'is_album' => $this->isAlbum($event->image),
        ]);
    }

    protected function isAlbum($image)
    {
        if (method_exists($image, 'getIsAlbum')) {
            return $isAlbum = $image->getIsAlbum();
        }

        return false;
    }
}
