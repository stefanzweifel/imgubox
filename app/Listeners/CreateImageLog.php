<?php

namespace ImguBox\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use ImguBox\Events\ImgurImageStored;
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
     * @param  ImgurImageStored  $event
     * @return void
     */
    public function handle(ImgurImageStored $event)
    {
        return $this->log->create([
            'user_id'  => $event->user->id,
            'imgur_id' => $event->image->id,
            'is_album' => object_get($event->image, 'is_album', false)
        ]);
    }
}
