<?php

namespace ImguBox\Http\Controllers;

use Illuminate\Http\Request;
use ImguBox\Http\Controllers\Controller;
use ImguBox\Http\Requests;
use ImguBox\Http\Requests\UpdatePasswordRequest;

class UsersController extends Controller
{
    /**
     * Close User Account
     * Delete all connected tokens
     * @param  Request $request
     * @return redirect
     */
    public function closeAccount(Request $request)
    {
        foreach (auth()->user()->tokens as $token) {
            $token->delete();
        }
        
        $user = auth()->user();
        
        auth()->logout();
        $user->delete();

        return redirect('/');
    }

    /**
     * Update Password of current User
     * @param  UpdatePasswordRequest $request
     * @return redirect
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        return redirect()->back()->withSuccess("Your password was successfully updated");
    }
}
