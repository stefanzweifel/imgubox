@extends('layouts.marketing')

@section('content')

    <div class="marketing-hero text-center">

        <h3>Store your Imgur favorites to <i class="fa fa-dropbox"></i> Dropbox</h3><br>

        @if ($userCount < 95)

            {!! link_to_route('auth.register', 'Register now', [], ['class' => 'btn btn-success btn-lg']) !!}

        @else

            <a href="#" class="btn btn-success btn-lg disabled">Registration currently closed</a>

            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <br>
                    <div class="alert alert-info text-left">
                        <p>Looks like ImguBox is growing to fast!</p>
                        <p>Because we don't have "production" status at Dropbox, registration temporary disabled.</p>

                        <p>Get updates here: <a href="https://twitter.com/imguboxapp">@imguboxapp</a></p>
                    </div>

                </div>
            </div>


        @endif

    </div>

@stop