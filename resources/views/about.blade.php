@extends('layouts.master')

@section('title') About @stop

@section('content')

    <div class="page-header">
        <h2>About</h2>
    </div>

    <p>ImguBox is a simple service which connects <a href="https://imgur.com">Imgur</a> with <a href="https://dropbox.com">Dropbox</a>. It's only purpose is to store Imgur favorites within your Dropbox account. It serves like a bridge between those services (It's similiar to <a href="https://ifttt.com">IFTTT</a>).</p>

    <p>After sign up, you have to connect both accounts to ImguBox. ImguBox will then receive access tokens from both services, wich will then be stored encrypted in our database. Favorites are beeing "synced" every 30 minutes.</p>

    <h3>Open Source</h3>
    <p>The project is open-source! You can inspect the source code at <a href="https://github.com/stefanzweifel/imgubox">Github</a>.</p>

    <h3>Privacy</h3>
    <p>ImguBox doesn't store <b>any</b> of the synced images on its server.</p>
    <p>We use Google Analytics for usage analysis.</p>

    <h3>Questions / Maintainer</h3>
    <p>If you have any questions, you can email us at <a href="mailto:imgubox@wnx.ch">imgubox@wnx.ch</a>. If you're more the twitter person, we also have a Twitter-Account: <a href="https://twitter.com/imguboxapp">@imguboxapp</a>.</p>

    <p>The project is mainted by Stefan Zweifel. A fullstack developer from Switzerland. <a href="https://stefanzweifel.io">More info</a>.</p>

@stop
