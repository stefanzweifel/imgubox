<?php

namespace ImguBox\Http\Controllers;

use ImguBox\Http\Requests;
use ImguBox\Http\Controllers\Controller;
use ImguBox\Token;
use ImguBox\Services\ImgurService;
use Illuminate\Http\Request;
use Socialize;

class OAuthController extends Controller
{
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

        auth()->user()->update([
            'imgur_username' => $response->account_username
        ]);

        auth()->user()->imgurToken()->delete();

        $token = $this->token->firstOrCreate([
            'token'         => $response->access_token,
            'refresh_token' => $response->refresh_token,
            'user_id'       => auth()->id(),
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

        auth()->user()->dropboxToken()->delete();

        $token = $this->token->create([
            'token'       => $user->token,
            'user_id'     => auth()->id(),
            'provider_id' => 2
        ]);
        ;

        return redirect('home')->withSuccess("Connection between ImguBox and Dropbox successfully established.");
        ;
    }

    /**
     * Delete all active Dropbox Tokens
     * @return redirect
     */
    public function deleteDropbox()
    {
        auth()->user()->dropboxToken()->delete();

        return redirect()->back();
    }

    /**
     * Delete all active Imgur Tokens
     * @return redirect
     */
    public function deleteImgur()
    {
        auth()->user()->imgurToken()->delete();

        return redirect()->back();
    }
}
