<?php namespace ImguBox\Http\Controllers;

use ImguBox\Http\Requests;
use ImguBox\Http\Controllers\Controller;

use Illuminate\Http\Request;

use ImguBox\User;

use Hash, Auth;

class AuthController extends Controller {

	public function login()
    {
        return view('auth.user.login');
    }

    public function register()
    {
        return view('auth.user.register');
    }

    public function loginHandle(Request $request)
    {
        $user = User::where('email', $request->get('email'))->firstOrFail();

        if (Hash::check($request->get('password'), $user->password))
        {
            Auth::login($user, 1);
            return redirect('/');
        }

        return 'nope';

    }

    public function registerHandle(Request $request)
    {
        $password = Hash::make($request->get('password'));

        $user = User::create([
            'email' => $request->get('email'),
            'password' => $password
        ]);

        return redirect()->route('auth.login');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

}
