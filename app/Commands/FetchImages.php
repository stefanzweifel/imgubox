<?php namespace ImguBox\Commands;

use ImguBox\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\Queue;

use Cache;
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
		$imgurIds   = $this->user->logs->lists('imgur_id');
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
		    $favorites = collect($favorites)->reject(function($object) use ($imgurIds) {
		    	return in_array($object->id, $imgurIds);
		    });

		    foreach($favorites as $favorite) {

		    	Cache::put("user:{$this->user->id}:favorite:{$favorite->id}", $favorite, 10);

				$job = new StoreImages($this->user->id, $favorite->id);
				$queue->push($job);

				$this->createLog($favorite);
		    }

		}
		elseif (property_exists($favorites, 'error')) {

			// TODO: Send Email to inform user that connection is broken.

			// Delete ImgurToken.
			$imgurToken->delete();

		}

	}

	/**
	 * Create log in database. Prevents duplicated download for given user
	 * @param  object $favorite
	 * @return void
	 */
	private function createLog($favorite)
	{
		if ($favorite->is_album !== false) {

			Log::create([
				'user_id'  => $this->user->id,
				'imgur_id' => $favorite->id,
				'is_album' => true
			]);

		}

	}

}
