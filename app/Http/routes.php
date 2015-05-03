<?php


Route::group(['prefix' => 'auth'], function() {

    get('login', ['as' => 'auth.login', 'uses' => 'AuthController@login']);
    post('login', ['as' => 'auth.login.handle', 'uses' => 'AuthController@loginHandle']);

    get('register', ['as' => 'auth.register', 'uses' => 'AuthController@register']);
    post('register', ['as' => 'auth.register.handle', 'uses' => 'AuthController@registerHandle']);

});

Route::get('/home', 'PageController@dashboard');

Route::get('/', 'PageController@marketing');


Route::group(['middleware' => ['auth']], function(){


    Route::group(['prefix' => 'auth'], function() {

        get('imgur', ['as' => 'auth.imgur.redirect', 'uses' => 'OAuthController@redirectToImgur']);
        get('imgur/handle', ['as' => 'auth.imgur.handle', 'uses' => 'OAuthController@handleImgurCallback']);
        get('imgur/delete', ['as' => 'auth.imgur.delete', 'uses' => 'OAuthController@deleteImgur']);

        get('dropbox', ['as' => 'auth.dropbox.redirect', 'uses' => 'OAuthController@redirectToDropbox']);
        get('dropbox/handle', ['as' => 'auth.dropbox.handle', 'uses' => 'OAuthController@handleDropboxCallback']);
        get('dropbox/delete', ['as' => 'auth.dropbox.delete', 'uses' => 'OAuthController@deleteDropbox']);

        get('logout', ['as' => 'auth.logout', 'uses' => 'AuthController@logout']);


    });

    get('settings', ['as' => 'user.settings', 'uses' => 'PageController@settings']);

});