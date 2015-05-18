@extends('layouts.master')

@section('content')

    <div class="alert alert-info" role="alert">
        <p><b>ImguBox</b> is still in it's early days. If you find  bugs or think something should be improved, tweet us <a href="https://twitter.com/@imguboxapp">@imguboxapp</a>.</p>
    </div>

    @if (Auth::user()->hasImgurToken()->first() && Auth::user()->hasDropboxToken()->first())

        @if (Auth::user()->logs->count() <= 0)

        <div class="alert alert-success" role="alert">
            <p>Accounts are setup. Your Imgur favorites should show up in your Dropbox in the upcoming hours.</p>
        </div>

        @else

            <div class="alert alert-info" role="alert">
                <p>Last successfull sync: <b>{!! Auth::user()->logs()->latest()->first()->created_at->format('d.m.Y H:i:s') !!}</b>.</p>
                <p>We synced <b>{{ Auth::user()->logs->count() }}</b> images for you so far.</p>
            </div>

        @endif

    @endif

    <div class="page-header">
        <h2>Setup</h2>
    </div>

    <div class="row">
        <div class="col-md-6">

           @if (Auth::user()->hasImgurToken()->first() )
                <div class="panel panel-success">
            @else
               <div class="panel panel-default">
            @endif


                <div class="panel-heading">
                    <h3 class="panel-title">Imgur</h3>
                </div>

                <div class="panel-body">

                    <p>Connect with Imgur so we can grab your favorites for you.</p>

                    @if (Auth::user()->hasImgurToken()->first() )
                        <a href="#" class="disabled btn btn-success">Already connected</a>
                    @else
                        {!! link_to_route('auth.imgur.redirect', 'Connect', [], ['class' => 'btn btn-success']) !!}
                    @endif

                </div>
            </div>

        </div>

        <div class="col-md-6">

           @if (Auth::user()->hasDropboxToken()->first() )
                <div class="panel panel-success">
            @else
               <div class="panel panel-default">
            @endif

                <div class="panel-heading">
                    <h3 class="panel-title">Dropbox</h3>
                </div>

                <div class="panel-body">

                    <p>Choose Dropbox-Account where we should store your favorites.</p>

                    @if (Auth::user()->hasDropboxToken()->first() )
                        <a href="#" class="disabled btn btn-success">Already connected</a>
                    @else
                        {!! link_to_route('auth.dropbox.redirect', 'Connect', [], ['class' => 'btn btn-success']) !!}
                    @endif
                </div>
            </div>

        </div>

    </div>

@stop