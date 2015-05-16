@extends('layouts.marketing')

@section('content')

    <div class="marketing-hero text-center">

        <h3>Store your Imgur favorites to <i class="fa fa-dropbox"></i> Dropbox</h3><br>

        {!! link_to_route('auth.register', 'Register now', [], ['class' => 'btn btn-success btn-lg']) !!}

    </div>

@stop