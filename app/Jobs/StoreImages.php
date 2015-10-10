<?php

namespace Imgubox\Jobs;

use Imgubox\Jobs\Job;
use ImguBox\User;
use ImguBox\Log;

use ImguBox\Services\ImgurService;
use ImguBox\Services\DropboxService;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Container\Container;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Carbon\Carbon;
use Cache, App, Slack;

class StoreImages extends Job implements SelfHandling, ShouldQueue {

	use InteractsWithQueue, SerializesModels;

	protected $user;

	protected $favorite;

	protected $imgurToken;

	protected $dropboxToken;

	protected $imgur;

	protected $dropbox;

	/**
	 * Array Key of Image (album only)
	 * @var int
	 */
	protected $key = null;

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
		$images     = $this->cleanUpImages($album->images);

		$this->dropbox->createFolder("/$folderName");
		$this->storeDescription($folderName, $album);

		foreach($images as $key => $image) {

			$this->key = $key;

			$this->storeImage($folderName, $image);
			$this->storeDescription($folderName, $image);

		}

	}

	/**
	 * Remove already processed images from an array
	 * @param  array $images
	 * @return Collection
	 */
	private function cleanUpImages($images)
	{
		$imgurIds   = $this->user->logs->lists('imgur_id')->all();

	    return collect($images)->reject(function($object) use ($imgurIds) {
	    	return in_array($object->id, $imgurIds);
	    });
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

				if (!is_null($this->key)) {
					$filename = "{$this->key} - {$image->id} - description.txt";
				}
				else {
					$filename = "{$image->id} - description.txt";
				}

				$this->dropbox->uploadDescription("/$folderName/$filename", $image->description);

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

		$filename  = $this->getFileName($image, 'link');

		$this->dropbox->uploadFile("/$folderName/$filename", fopen($image->link,'rb'));

		$this->storeGifs($image, $folderName);

		$this->createLog($image);
	}

	private function storeGifs($image, $folderName)
	{
		if ($image->animated === true) {

			// // GIFV
			// $filename = $this->getFileName($image, 'gifv');
			// $this->dropbox->uploadFile("/$folderName/$filename", fopen($image->gifv,'rb'));

			// // WEBM
			// $filename = $this->getFileName($image, 'webm');
			// $this->dropbox->uploadFile("/$folderName/$filename", fopen($image->webm,'rb'));

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
		$filename = pathinfo($image->{$type}, PATHINFO_BASENAME);

		if (!is_null($this->key)) {

			return "{$this->key} - {$filename}";

		}

		return $filename;
	}

	/**
	 * Build foldername for imgur object
	 * @param  mixed  $object
	 * @return string
	 */
	private function getFoldername($object)
	{
		if (property_exists($object, 'title')) {

			if (is_null($object->title)) {

				return $object->id;

			}

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
