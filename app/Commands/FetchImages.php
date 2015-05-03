<?php namespace ImguBox\Commands;

use ImguBox\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\Queue;

use ImguBox\User;
use ImguBox\Log;
use ImguBox\Service\ImguBoxService;

class FetchImages extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	protected $user;

	protected $favorites;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $favorites)
	{
		$this->user = $user;
		$this->favorites = $favorites;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle(Container $app, Queue $queue)
	{
		$logs     = $this->user->logs;
		$imgurIds = $logs->lists('imgur_id');

		$imgurToken = $this->user->tokens()->where('provider_id', 1)->first();

		// Setup Imgur Service
		// $now = Carbon::now();
		$imgur   = $app->make('ImguBox\Services\ImgurService');
		$imgur->setUser($this->user);
		$imgur->setToken($imgurToken);
		$difference = $imgurToken->updated_at->diffInSeconds();

		// Imgur acccess_token expires after 3600 seconds
		if ($difference >= 3500) {

			$refreshedToken    = $imgur->refreshToken();
			$imgurToken->token = $refreshedToken->access_token;
			$imgurToken->save();

		}

		$imgur->setToken($imgurToken);
		$favorites = $imgur->favorites();


	    // Remove models we already processed
	    $favorites = collect($favorites)->reject(function($object) use ($imgurIds) {
	    	return in_array($object->id, $imgurIds);
	    });

	    foreach($favorites as $favorite) {

			$job = new StoreImages($this->user, $favorite);
			$queue->push($job);

	    }

	}

}
