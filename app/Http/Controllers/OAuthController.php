<?php namespace ImguBox\Http\Controllers;

use ImguBox\Http\Requests;
use ImguBox\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Socialize, Auth;

use ImguBox\Token;

class OAuthController extends Controller {

    public function redirectToImgur()
    {
        return Socialize::with('imgur')->redirect();
    }

    public function handleImgurCallback()
    {
        $user = Socialize::with('imgur')->user();

        // Update imgur_username
        $authUser = Auth::user();
        $authUser->imgur_username = $user->nickname;
        $authUser->save();

        $token = Token::create([
            'token'       => $user->token,
            'user_id'     => $authUser->id,
            'provider_id' => 1
        ]);

        return $token;
    }

    public function redirectToDropbox()
    {
        return Socialize::with('dropbox')->redirect();
    }

    public function handleDropboxCallback()
    {
        $user = Socialize::with('dropbox')->user();

        $token = Token::create([
            'token'       => $user->token,
            'user_id'     => \Auth::id(),
            'provider_id' => 2
        ]);;

        return $token;
    }

}
