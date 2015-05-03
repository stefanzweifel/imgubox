<?php namespace ImguBox\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Contracts\Queue\Queue;
use ImguBox\Services\ImgurService;

use ImguBox\Commands\FetchImages;
use ImguBox\Commands\StoreImages;
use ImguBox\User;

class FetchUserFavs extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'imgubox:fetchFavs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fetches User Favs from Imgur (Get the favorited images).';

	/**
	 * User Instance
	 * @var ImguBox\User
	 */
	protected $user;

	/**
	 * Queue Instance
	 * @var Illuminate\Contracts\Queue\Queue
	 */
	protected $queue;

	/**
	 * ImgurService Instance
	 * @var ImguBox\Services\ImgurService
	 */
	protected $imgur;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, Queue $queue, ImgurService $imgur)
	{
		parent::__construct();
		$this->user = $user;
		$this->queue = $queue;
		$this->imgur = $imgur;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info('Start fetching');

		$this->user->hasDropboxToken()->hasImgurToken()->chunk(10, function($users) {

			foreach($users as $user) {

				$this->fetchFavorites($user);

			}

		});

		$this->info('Done.');

	}

	private function fetchFavorites(User $user)
	{
		$logs     = $user->logs;
		$imgurIds = $logs->lists('imgur_id');

		$imgurToken = $user->tokens()->where('provider_id', 1)->first();
		$this->imgur->setToken($imgurToken);
		$difference = $imgurToken->updated_at->diffInSeconds();

		// Imgur acccess_token expires after 3600 seconds
		if ($difference >= 3500) {

			$refreshedToken  = $this->imgur->refreshToken();

			if ($refreshedToken->success === false) {

				$this->error('something went wrong');
				return false;

			}

			$imgurToken->token = $refreshedToken->access_token;
			$imgurToken->save();

		}

		$this->imgur->setUser($user);
		$this->imgur->setToken($imgurToken);
		$favorites = $this->imgur->favorites();

		if (is_array($favorites)) {

		    // Remove models we already processed
		    $favorites = collect($favorites)->reject(function($object) use ($imgurIds) {
		    	return in_array($object->id, $imgurIds);
		    });

			$job = new FetchImages($user, $favorites);

			$this->queue->push($job);

		}
		elseif (property_exists($favorites, 'error')) {

			// Send Email to inform user that connection is broken.

			// Delete ImgurToken.
			$imgurToken->delete();

		}

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			// ['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			// ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
