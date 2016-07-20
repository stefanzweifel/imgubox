<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => env('MANDRILL_KEY'),
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'ImguBox\User',
		'key' => '',
		'secret' => '',
	],

	'imgur' => [
		'client_id'     => env('IMGUR_KEY'),
		'client_secret' => env('IMGUR_SECRET'),
		'redirect'      => env('IMGUR_REDIRECT_URI'),
	],

	'dropbox' => [
		'client_id'     => env('DROPBOX_KEY'),
		'client_secret' => env('DROPBOX_SECRET'),
		'redirect'      => env('DROPBOX_REDIRECT_URI'),
	],

	'envoyer' => [
		'pings' => [
			'fetch_favs' => env('FETCH_FAVS_PING')
		]
	]

];
