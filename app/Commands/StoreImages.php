<?php namespace ImguBox\Commands;

use ImguBox\Commands\Command;
use ImguBox\User;
use ImguBox\Log;

use ImguBox\Services\ImgurService;
use ImguBox\Services\DropboxService;
use ImguBox\Services\ImguBoxService;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Container\Container;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use Carbon\Carbon;

class StoreImages extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	protected $user;

	protected $favorite;

	protected $imgurToken;

	protected $dropboxToken;

	protected $imgur;

	protected $dropbox;

	protected $imgubox;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $favorite)
	{
		$this->user     = $user;
		$this->favorite = $favorite;
	}


	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle(Container $app)
	{

		/**
		 * Should go somewhere into Setup
		 */
		$this->imgurToken   = $this->user->tokens()->isImgurToken()->first();
		$this->dropboxToken = $this->user->tokens()->isDropboxToken()->first();

		$imgur   = \App::make('ImguBox\Services\ImgurService');
		$imgur->setUser($this->user);
		$imgur->setToken($this->imgurToken);

		$this->imgur = $imgur;

		$dropbox = \App::make('ImguBox\Services\DropboxService');
		$dropbox->setToken($this->dropboxToken);

		$this->dropbox = $dropbox;

		// $imgubox = \App::make('ImguBox\Services\ImguBoxService');
		// $imgubox->setDropbox($dropbox);

		// $this->imgubox = $imgubox;


		if ($this->favorite->is_album === false) {

			$image       = $imgur->image($this->favorite->id);

			// If no error accoured, proceed
			if (!property_exists($image, 'error')) {

				$title      = $image->title;
				$now        = date("Y-m-d", $this->favorite->datetime);
				$folderName = str_slug("$now -  $title");

				$this->storeImage($folderName, $image);

			}

		}
		else {

			// Handle Album
			$this->storeAlbum();

		}

	}

	/**
	 * Store an Album
	 * @return void
	 */
	private function storeAlbum()
	{
		$album      = $this->imgur->gallery($this->favorite->id);
		$title      = $album->title;
		$now        = date("Y-m-d", $album->datetime);
		$folderName = str_slug("$now - $title");

		$this->dropbox->createFolder("/$folderName");

		$this->storeDescription($folderName, $album);

		foreach($album->images as $image) {

			$this->storeImage($folderName, $image);

		}

		Log::create([
			'user_id' => $this->user->id,
			'imgur_id' => $this->favorite->id,
			'is_album' => true
		]);

	}

	/**
	 * Store Image description to Cloud Storage
	 * @param  string $folderName
	 * @param  object $image
	 * @return void
	 */
	private function storeDescription($folderName, $image)
	{
		if (property_exists($image, 'description')) {

			if (!empty($image->description)) {

				$this->dropbox->uploadDescription("/$folderName/description.txt", $image->description);

			}

		}
	}

	/**
	 * Store an Image
	 * @param  string $folderName
	 * @param  object $image
	 * @return void
	 */
	private function storeImage($folderName, $image)
	{
		$this->storeDescription($folderName, $image);

		$filename    = pathinfo($image->link, PATHINFO_BASENAME);
		$this->dropbox->uploadFile("/$folderName/$filename", fopen($image->link,'rb'));

		// If GIF, store all types
		if ($image->animated === true) {

			// GIFV
			// $filename    = pathinfo($image->gifv, PATHINFO_BASENAME);
			// \Dropbox::uploadFile("/$folderName/$filename", $writeMode, fopen($image->gifv,'rb'));

			// WEBM
			// $filename    = pathinfo($image->webm, PATHINFO_BASENAME);
			// \Dropbox::uploadFile("/$folderName/$filename", $writeMode, fopen($image->webm,'rb'));

			// MP4
			if (property_exists($image, 'mp4')) {
				$filename    = pathinfo($image->mp4, PATHINFO_BASENAME);
				$this->dropbox->uploadFile("/$folderName/$filename", fopen($image->mp4,'rb'));
			}

		}

		Log::create([
			'user_id'  => $this->user->id,
			'imgur_id' => $image->id,
			'is_album' => false
		]);

	}

}
