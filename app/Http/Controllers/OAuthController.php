<?php namespace ImguBox\Http\Controllers;

use ImguBox\Http\Requests;
use ImguBox\Http\Controllers\Controller;
use ImguBox\Token;
use ImguBox\Services\ImgurService;

use Illuminate\Http\Request;

use Socialize, Auth, App, Crypt;

class OAuthController extends Controller {

    /**
     * @var ImguBox\Services\ImgurService
     */
    protected $imgur;

    /**
     * @var ImguBox\Token
     */
    protected $token;

    public function __construct(ImgurService $imgur, Token $token)
    {
        $this->imgur = $imgur;
        $this->token = $token;
    }

    /**
     * Redirect to Imgur OAuth
     * @return redirect
     */
    public function redirectToImgur()
    {
        return Socialize::with('imgur')->redirect();
    }

    public function handleImgurCallback(Request $request)
    {
        $response = $this->imgur->getAccessToken($request->get('code'));

        // Update imgur_username
        $authUser = Auth::user();
        $authUser->imgur_username = $response->account_username;
        $authUser->save();

        // Delete all other Imgur Tokens of this user
        $previousTokens = $authUser->imgurTokens()->get();
        foreach ($previousTokens as $token) {
            $token->delete();
        }

        $token = $this->token->firstOrCreate([
            'token'         => Crypt::encrypt($response->access_token),
            'refresh_token' => Crypt::encrypt($response->refresh_token),
            'user_id'       => $authUser->id,
            'provider_id'   => 1
        ]);

        return redirect('home')->withSuccess("Connection between ImguBox and Imgur successfully established.");
    }

    /**
     * Redirect to Dropbox OAuth
     * @return redirect
     */
    public function redirectToDropbox()
    {
        return Socialize::with('dropbox')->redirect();
    }

    public function handleDropboxCallback()
    {
        $user = Socialize::with('dropbox')->user();

        $previousTokens = Auth::user()->dropboxTokens()->get();
        foreach ($previousTokens as $token) {
            $token->delete();
        }

        $token = $this->token->create([
            'token'       => Crypt::encrypt($user->token),
            'user_id'     => Auth::id(),
            'provider_id' => 2
        ]);;

        return redirect('home')->withSuccess("Connection between ImguBox and Dropbox successfully established.");;
    }

    /**
     * Delete all active Dropbox Tokens
     * @return redirect
     */
    public function deleteDropbox()
    {
        $tokens = Auth::user()->dropboxTokens()->get();

        foreach($tokens as $token) {
            $token->delete();
        }

        return redirect()->back();
    }

    /**
     * Delete all active Imgur Tokens
     * @return redirect
     */
    public function deleteImgur()
    {
        $tokens = Auth::user()->imgurTokens()->get();

        foreach($tokens as $token) {
            $token->delete();
        }

        return redirect()->back();
    }

}
