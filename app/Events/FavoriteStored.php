<?php

namespace ImguBox\Events;

use Illuminate\Queue\SerializesModels;
use ImguBox\User;

class FavoriteStored extends Event
{
    use SerializesModels;

    /**
     * Imgur Image or Imgur Albom.
     *
     * @var object
     */
    public $image;

    /**
     * User.
     *
     * @var ImguBox\User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($image, User $user)
    {
        $this->image = $image;
        $this->user = $user;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
