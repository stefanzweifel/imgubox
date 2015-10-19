<?php

namespace ImguBox\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ImguBox\Jobs\Job;
use ImguBox\Jobs\StoreImgurImages;
use ImguBox\Services\ImgurService;
use ImguBox\User;
use Mail;

class FetchImages extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    use DispatchesJobs;

    protected $user;

    protected $favorites;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(ImgurService $imgur)
    {
        $imgurIds   = $this->user->logs->lists('imgur_id')->all();
        $imgurToken = $this->user->imgurToken;

        $imgur->setUser($this->user);
        $imgur->setToken($imgurToken);
        $difference = $imgurToken->updated_at->diffInSeconds();

        // Imgur acccess_token expires after 3600 seconds
        if ($difference >= 3500) {
            $refreshedToken    = $imgur->refreshToken();

            if (property_exists($refreshedToken, 'success') && $refreshedToken->success === false) {

                return \Log::error("Something wen't wrong while getting a new refresh token.", json_encode($refreshToken));
            }

            $imgurToken->token = $refreshedToken->access_token;
            $imgurToken->save();
        }

        $imgur->setToken($imgurToken);
        $favorites = $imgur->favorites();

        if (is_array($favorites)) {

            // Remove models we already processed
            $favorites = collect($favorites)->reject(function ($object) use ($imgurIds) {
                return in_array($object->id, $imgurIds);
            });

            foreach ($favorites as $favorite) {
                $base64Favorite = base64_encode(serialize($favorite));

                $this->dispatch(
                    (new StoreImgurImages($this->user, $base64Favorite))->onQueue("high")
                );
            }
        } elseif (property_exists($favorites, 'error')) {
            Mail::send('emails.api-error', [], function ($message) {
                $message->to($this->user->email)->subject("ImguBox can no longer sync your Imgur favorites. Action needed.");
            });

            // Delete ImgurToken.
            $imgurToken->delete();
        }
    }
}
