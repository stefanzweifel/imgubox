<?php

// // Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');


get('about', 'PageController@about');
get('/', 'PageController@marketing');


Route::group(['middleware' => ['auth']], function () {

    get('/home', 'PageController@dashboard');

    Route::group(['prefix' => 'auth'], function () {

        get('imgur', ['as' => 'auth.imgur.redirect', 'uses' => 'OAuthController@redirectToImgur']);
        get('imgur/handle', ['as' => 'auth.imgur.handle', 'uses' => 'OAuthController@handleImgurCallback']);
        get('imgur/delete', ['as' => 'auth.imgur.delete', 'uses' => 'OAuthController@deleteImgur']);

        get('dropbox', ['as' => 'auth.dropbox.redirect', 'uses' => 'OAuthController@redirectToDropbox']);
        get('dropbox/handle', ['as' => 'auth.dropbox.handle', 'uses' => 'OAuthController@handleDropboxCallback']);
        get('dropbox/delete', ['as' => 'auth.dropbox.delete', 'uses' => 'OAuthController@deleteDropbox']);

    });

    get('settings', ['as' => 'user.settings', 'uses' => 'PageController@settings']);

    post('close-account', ['as' => 'user.close_account', 'uses' => 'UsersController@closeAccount']);

});

/**
 * Handle Push-Queues
 */
Route::post('queue/receive', function () {
    return Queue::marshal();
});
