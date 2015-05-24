@extends('layouts.master')

@section('content')

    <div class="alert alert-info" role="alert">
        <p><b>ImguBox</b> is still in it's early days. If you find  bugs or think something should be improved, tweet us <a href="https://twitter.com/@imguboxapp">@imguboxapp</a>.</p>
    </div>

    <div class="page-header">
        <h2>Setup</h2>
    </div>

    @if (Auth::user()->imgurToken && Auth::user()->dropboxToken)

        @if (Auth::user()->logs->count() <= 0)

        <div class="alert alert-success" role="alert">
            <p>Accounts are setup. Your Imgur favorites should show up in your Dropbox in the upcoming hours.</p>
        </div>

        @else

            <div class="alert alert-info" role="alert">
                <p>Last successfull sync: <b>{!! Auth::user()->logs()->latest()->first()->created_at->format('d.m.Y H:i:s') !!} ({!! Auth::user()->logs()->latest()->first()->created_at->timezoneName !!})</b>.</p>
            </div>

        @endif

    @endif

    <p>You can manage your connections in your {!! link_to_route('user.settings', 'Settings page') !!}.</p>

    <div class="row">
        <div class="col-md-6">

           @if (Auth::user()->imgurToken )
                <div class="panel panel-success">
            @else
               <div class="panel panel-default">
            @endif


                <div class="panel-heading">
                    <h3 class="panel-title">Imgur</h3>
                </div>

                <div class="panel-body">

                    <p>Connect ImguBox with Imgur so we can see which images and albums you have favorited.</p>

                    @if (Auth::user()->imgurToken )
                        <a href="#" class="disabled btn btn-success">Authorized</a>
                    @else
                        {!! link_to_route('auth.imgur.redirect', 'Authorize', [], ['class' => 'btn btn-success']) !!}
                    @endif

                </div>
            </div>

        </div>

        <div class="col-md-6">

           @if (Auth::user()->dropboxToken )
                <div class="panel panel-success">
            @else
               <div class="panel panel-default">
            @endif

                <div class="panel-heading">
                    <h3 class="panel-title">Dropbox</h3>
                </div>

                <div class="panel-body">

                    <p>Connect ImguBox with Dropbox so we know in which Dropbox we should put your images. (Images will appear in your <code>App/ImguBox</code> folder)</p>

                    @if (Auth::user()->dropboxToken )
                        <a href="#" class="disabled btn btn-success">Authorized</a>
                    @else
                        {!! link_to_route('auth.dropbox.redirect', 'Authorize', [], ['class' => 'btn btn-success']) !!}
                    @endif
                </div>
            </div>

        </div>

    </div>

@stop