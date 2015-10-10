<?php namespace ImguBox\Http\Controllers;

use ImguBox\Http\Requests;
use ImguBox\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ImguBox\Http\Requests\RegisterRequest;
use ImguBox\Http\Requests\LoginRequest;
use ImguBox\User;
use Hash;
use Auth;

class AuthController extends Controller
{
    /**
     * Show Login View
     * @return view
     */
    public function login()
    {
        return view('auth.user.login');
    }

    /**
     * Show Register View
     * @return view
     */
    public function register()
    {
        if (User::count() > 95) {
            return redirect('/');
        }
        return view('auth.user.register');
    }

    /**
     * Handle login request
     * @param  LoginRequest $request
     * @return redirect
     */
    public function loginHandle(LoginRequest $request)
    {
        $user = User::where('email', $request->get('email'))->first();

        if (!$user) {
            return redirect()->back()->withInput()->withError('No account found.');
        }

        if (Hash::check($request->get('password'), $user->password)) {
            Auth::login($user, 1);
            return redirect('/');
        }

        return redirect()->back()->withInput()->withError('Email or password invalid.');
    }

    /**
     * Handle register request
     * @param  RegisterRequest $request
     * @return redirect
     */
    public function registerHandle(RegisterRequest $request)
    {
        $password = Hash::make($request->get('password'));

        $user = User::create([
            'email'    => $request->get('email'),
            'password' => $password
        ]);

        return redirect()->route('auth.login');
    }

    /**
     * Logout current user
     * @return redirect
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
