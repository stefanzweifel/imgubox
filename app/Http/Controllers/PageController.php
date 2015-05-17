<?php namespace ImguBox\Http\Controllers;

use ImguBox\Http\Requests;
use ImguBox\Http\Controllers\Controller;

use Illuminate\Http\Request;

use ImguBox\User;

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

		$userCount = User::count();

		return view('marketing', compact('userCount'));
	}

	/**
	 * Show Settings Page
	 * @return view
	 */
	public function settings()
	{
		$user = Auth::user();

		return view('user.settings', compact('user'));
	}

	/**
	 * Show About page
	 * @return view
	 */
	public function about()
	{
		return view('about');
	}

}
