<?php

/**
 * Authentication Routes.
 */
Route::group([], function () {
    Route::get('auth/login', 'Auth\AuthController@getLogin');
    Route::post('auth/login', 'Auth\AuthController@postLogin');
    Route::get('auth/logout', 'Auth\AuthController@getLogout');

    Route::get('auth/register', 'Auth\AuthController@getRegister');
    Route::post('auth/register', 'Auth\AuthController@postRegister');

    Route::get('password/email', 'Auth\PasswordController@getEmail');
    Route::post('password/email', 'Auth\PasswordController@postEmail');

    Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
    Route::post('password/reset', 'Auth\PasswordController@postReset');
});


Route::get('about', 'PageController@about');
Route::get('/', 'PageController@marketing');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', 'PageController@dashboard');

    Route::get('favorites', ['as' => 'favorites', 'uses' => 'FavoritesController@index']);
    Route::post('favorites/{logs}/purge', ['as' => 'favorites.purge.single', 'uses' => 'FavoritesController@purge']);
    Route::get('favorites/purge-all', ['as' => 'favorites.purge.all', 'uses' => 'FavoritesController@purgeAll']);

    Route::group(['prefix' => 'auth'], function () {
        Route::get('imgur', ['as' => 'auth.imgur.redirect', 'uses' => 'OAuthController@redirectToImgur']);
        Route::get('imgur/handle', ['as' => 'auth.imgur.handle', 'uses' => 'OAuthController@handleImgurCallback']);
        Route::get('imgur/delete', ['as' => 'auth.imgur.delete', 'uses' => 'OAuthController@deleteImgur']);

        Route::get('dropbox', ['as' => 'auth.dropbox.redirect', 'uses' => 'OAuthController@redirectToDropbox']);
        Route::get('dropbox/handle', ['as' => 'auth.dropbox.handle', 'uses' => 'OAuthController@handleDropboxCallback']);
        Route::get('dropbox/delete', ['as' => 'auth.dropbox.delete', 'uses' => 'OAuthController@deleteDropbox']);
    });

    Route::get('settings', ['as' => 'user.settings', 'uses' => 'PageController@settings']);

    Route::post('close-account', ['as' => 'user.close_account', 'uses' => 'UsersController@closeAccount']);
    Route::post('update-password', ['as' => 'user.password.update', 'uses' => 'UsersController@updatePassword']);
});
