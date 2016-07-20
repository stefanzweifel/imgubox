<?php

namespace ImguBox\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ImguBox\Services\Imgur\Client;
use ImguBox\User;

class FetchImages extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    use DispatchesJobs;

    protected $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(Client $imgurClient)
    {
        $imgurClient->setUser($this->user);

        foreach ($imgurClient->favorites()->getFavorites() as $favorite) {
            $base64Favorite = base64_encode(serialize($favorite));

            $this->dispatch(
                (new StoreImgurImages($this->user, $base64Favorite))->onQueue('high')
            );
        }
    }
}
