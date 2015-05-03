<?php namespace ImguBox\Http\Controllers;

use ImguBox\Http\Requests;
use ImguBox\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;

class PageController extends Controller {

	/**
	 * Show Dashboard
	 * @return view
	 */
	public function dashboard()
	{
		return view('dashboard');
	}

	/**
	 * Show Marketing Page
	 * @return view|redirect
	 */
	public function marketing()
	{
		if (Auth::check()) {
			return redirect('home');
		}
		return view('marketing');
	}

	public function settings()
	{
		return view('user.settings');
	}

}
