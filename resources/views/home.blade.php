@extends('layouts.master')

@section('content')

    @if (Auth::check())

        @if (Auth::user()->hasImgurToken()->first() )

            <p>Imgur Setup: Done</p>

        @else

            {!! link_to_route('auth.imgur.redirect', 'Connect with Imgur') !!}

        @endif

        @if (Auth::user()->hasDropboxToken()->first() )

            <p>Dropbox Setup: Done</p>

        @else

            {!! link_to_route('auth.dropbox.redirect', 'Connect with dropbox') !!}

        @endif


    @else

        {!! link_to_route('auth.login', 'Login') !!}
        {!! link_to_route('auth.register', 'Register') !!}

    @endif

@stop