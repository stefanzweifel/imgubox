<?php namespace ImguBox\Commands;

use ImguBox\Commands\Command;
use ImguBox\User;
use ImguBox\Log;

use ImguBox\Services\ImgurService;
use ImguBox\Services\DropboxService;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Container\Container;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

use Carbon\Carbon;
use Cache, App, Slack;

class StoreImages extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	protected $user;

	protected $favorite;

	protected $imgurToken;

	protected $dropboxToken;

	protected $imgur;

	protected $dropbox;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($userId, $favoriteId)
	{
		$this->user     = User::findOrFail($userId);
		$this->favorite = Cache::get("user:{$userId}:favorite:{$favoriteId}");
	}


	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle(Container $app)
	{
		$this->imgurToken   = $this->user->imgurToken;
		$this->dropboxToken = $this->user->dropboxToken;

		$imgur   = App::make('ImguBox\Services\ImgurService');
		$imgur->setUser($this->user);
		$imgur->setToken($this->imgurToken);

		$this->imgur = $imgur;

		$dropbox = App::make('ImguBox\Services\DropboxService');
		$dropbox->setToken($this->dropboxToken);

		$this->dropbox = $dropbox;

		if ($this->favorite->is_album === false) {

			$image       = $imgur->image($this->favorite->id);

			// If no error accoured, proceed
			if (!property_exists($image, 'error')) {

				$folderName = $this->getFoldername($image);

				$this->storeImage($folderName, $image);

				$this->createLog($image);

			}
			else {

				// Handle Error here
				// Slack::send("An error accoured:" . json_encode($image));

			}

		}
		else {

			// Handle Album
			$this->storeAlbum();
			$this->createLog($this->favorite);

		}

	}

	/**
	 * Store an Album
	 * @return void
	 */
	private function storeAlbum()
	{
		$album      = $this->imgur->gallery($this->favorite->id);
		$folderName = $this->getFoldername($album);

		$this->dropbox->createFolder("/$folderName");

		$this->storeDescription($folderName, $album);

		foreach($album->images as $key =>  $image) {

			$this->storeImage($folderName, $image, $key);
			$this->storeDescription($folderName, $image);

		}

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

				$this->dropbox->uploadDescription("/$folderName/{$image->id} - description.txt", $image->description);

			}

		}
	}

	/**
	 * Store an Image
	 * @param  string $folderName
	 * @param  object $image
	 * @return void
	 */
	private function storeImage($folderName, $image, $key = null)
	{
		$this->storeDescription($folderName, $image);

		$filename  = $this->getFileName($image, 'link');

		if (!is_null($key)) {
			$filename = "{$key} - {$filename}";
		}

		$this->dropbox->uploadFile("/$folderName/$filename", fopen($image->link,'rb'));

		$this->storeGifs($image, $folderName);

		$this->createLog($image);
	}

	private function storeGifs($image, $folderName)
	{
		if ($image->animated === true) {

			// GIFV
			$filename = $this->getFileName($image, 'gifv');
			$this->dropbox->uploadFile("/$folderName/$filename", fopen($image->gifv,'rb'));

			// WEBM
			$filename = $this->getFileName($image, 'webm');
			$this->dropbox->uploadFile("/$folderName/$filename", fopen($image->webm,'rb'));

			// MP4
			if (property_exists($image, 'mp4')) {
				$filename = $this->getFileName($image, 'mp4');
				$this->dropbox->uploadFile("/$folderName/$filename", fopen($image->mp4,'rb'));
			}

		}
	}

	/**
	 * Return Filename
	 * @param  object $image
	 * @param  string $type
	 * @return string
	 */
	private function getFileName($image, $type)
	{
		return pathinfo($image->{$type}, PATHINFO_BASENAME);
	}

	/**
	 * Build foldername for imgur object
	 * @param  mixed  $object
	 * @return string
	 */
	private function getFoldername($object)
	{
		if (is_null($object->title)) {

			return $object->id;

		}

		return str_slug("{$object->title} {$object->id}");
	}

	/**
	 * Create new entry in logs table
	 * @param  object $object
	 * @return ImguBox\Log
	 */
	private function createLog($object)
	{
		$isAlbum = false;

		if (property_exists($object, 'is_album')) {

			$isAlbum = $object->is_album;

		}

		return Log::create([
			'user_id'  => $this->user->id,
			'imgur_id' => $object->id,
			'is_album' => $isAlbum
		]);
	}

}
