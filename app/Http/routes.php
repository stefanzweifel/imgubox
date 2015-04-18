<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix' => 'auth'], function() {

    get('imgur', ['as' => 'auth.imgur.redirect', 'uses' => 'OAuthController@redirectToImgur']);
    get('imgur/handle', ['as' => 'auth.imgur.handle', 'uses' => 'OAuthController@handleImgurCallback']);

    get('dropbox', ['as' => 'auth.dropbox.redirect', 'uses' => 'OAuthController@redirectToDropbox']);
    get('dropbox/handle', ['as' => 'auth.dropbox.handle', 'uses' => 'OAuthController@handleDropboxCallback']);

    get('login', ['as' => 'auth.login', 'uses' => 'AuthController@login']);
    post('login', ['as' => 'auth.login.handle', 'uses' => 'AuthController@loginHandle']);

    get('register', ['as' => 'auth.register', 'uses' => 'AuthController@register']);
    post('register', ['as' => 'auth.register.handle', 'uses' => 'AuthController@registerHandle']);

    get('logout', ['as' => 'auth.logout', 'uses' => 'AuthController@logout']);

});

get('favorites', ['as' => 'app.imgur.favorites', 'uses' => 'WelcomeController@showFavorites']);

get('home', ['as' => 'home', 'uses' => 'WelcomeController@index']);

Route::get('/', 'WelcomeController@index');
