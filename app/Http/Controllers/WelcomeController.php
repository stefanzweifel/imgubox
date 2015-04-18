<?php namespace ImguBox\Http\Controllers;

use Auth, Cache, App, File;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// $this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		// return \Auth::user()->HasDropboxToken()->get();
		return view('home');
	}

	public function showFavorites()
	{

		if (Auth::check()) {

			$imgurToken   = Auth::user()->tokens()->where('provider_id', 1)->first()->token;
			$dropboxToken = Auth::user()->tokens()->where('provider_id', 2)->first()->token;

			$imgur   = App::make('ImguBox\Services\ImgurService');
			$dropbox = App::make('ImguBox\Services\DropboxService');


			if (Cache::has('imgur_data'))
			{
			    $data = Cache::get('imgur_data');

			    $pictures = $data->where('is_album', false);

			    // return $pictures;

			    foreach($pictures as $pic) {

					$image       = $imgur->image($pic->id);
					$description = $image->description;
					$link        = $image->link;
					$topic       = $image->topic;

					$folderName = str_slug("$pic->id $topic");

					$writeMode = \Dropbox\WriteMode::force();
					\Config::set('dropbox.connections.main.token', $dropboxToken);
					\Dropbox::createFolder("/$folderName");

					\Dropbox::uploadFileFromString("/$folderName/description.md", $writeMode, $description);
					\Dropbox::uploadFile("/$folderName/image.jpg", $writeMode, fopen($link,'rb'));

			    	dd($description, $link, $topic);

			    }

			    return $pictures;

			}
			else {


				$favorites = $imgur->favorites();

				$data  = \Illuminate\Support\Collection::make($favorites);

				Cache::put('imgur_data', $data, 60);

			}

			return $data;
		}

		return 'not logged in';

	}


}
