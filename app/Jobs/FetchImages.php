<?php

namespace ImguBox\Jobs;

use Cache;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ImguBox\Jobs\Job;
use ImguBox\Log;
use ImguBox\User;
use Mail;

class FetchImages extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;

    protected $favorites;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->user = User::findOrFail($userId);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(Container $app, Queue $queue)
    {
        $imgurIds   = $this->user->logs->lists('imgur_id')->all();
        $imgurToken = $this->user->imgurToken;

        // Setup Imgur Service
        $imgur   = $app->make('ImguBox\Services\ImgurService');
        $imgur->setUser($this->user);
        $imgur->setToken($imgurToken);
        $difference = $imgurToken->updated_at->diffInSeconds();

        // Imgur acccess_token expires after 3600 seconds
        if ($difference >= 3500) {
            $refreshedToken    = $imgur->refreshToken();

            if (property_exists($refreshedToken, 'success') && $refreshedToken->success === false) {
                return $this->error('something went wrong');
            }

            $imgurToken->token = \Crypt::encrypt($refreshedToken->access_token);
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
                Cache::put("user:{$this->user->id}:favorite:{$favorite->id}", $favorite, 10);

                $job = new StoreImages($this->user->id, $favorite->id);
                $queue->later(rand(1, 900), $job);
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
