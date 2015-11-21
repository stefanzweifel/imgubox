@extends('layouts.master')

@section('content')

    <p class="lead">Your Favorites. You can manually purge the log of a single favorite or purge all logs. We will then redownload your favorites.</p>


    <div class="panel panel-default">
        <div class="panel-heading">Redownload everything!</div>
        <div class="panel-body">

            <p>Should we redownload every single image again?</p>

            {!! link_to_route("favorites.purge.all", "Yes, redownload everything again.", [], ["class" => "btn btn-danger js-purge-all"]) !!}

        </div>
    </div>

    @if ($favorites->count() > 0)

        <div class="panel panel-default">
            <div class="panel-heading">Favorites</div>
            <div class="panel-body">

                <p>Instead of redownloading everything again, you can also select which image we should redownload.</p>

            </div>

            <table class="table table-striped table-bordered js-favorites-table">
                <thead>
                    <tr>
                        <th>Link on Imgur</th>
                        <th>Stored at</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($favorites as $favorite)
                        <tr id="row-{{ $favorite->id }}">
                            <td>
                                <a href="https://imgur.com/{{ $favorite->imgur_id }}" target="blank">https://imgur.com/{{ $favorite->imgur_id }}</a>
                            </td>
                            <td>{{ $favorite->created_at->format('d.m.Y H:i:s') }}</td>
                            <td>
                                {!! Form::open(["route" => ["favorites.purge.single", $favorite->id]]) !!}
                                    {!! Form::submit("Redownload", [
                                        "class" => "btn btn-sm btn-default js-purge-single",
                                        "id" => "redownload-{$favorite->id}"
                                    ]) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($favorites->count() >= 25)
            <div class="panel-footer">
                {!! $favorites->render() !!}
            </div>
            @endif

        </div>


    @else

        <div class="alert alert-info" role="alert">
            <h4 class="text-uppercase">No favorites (yet)</h4>
            <p>We haven't synced your favorites yet or you just purged your log. Give us some minutes till we fetch your favorites again.</p>
        </div>

    @endif

@stop

@section("scripts")

    <script>

        $(".js-favorites-table").on('click', '.js-purge-single', function(event) {
            ga('send', {
              hitType: 'event',
              eventCategory: 'Favorites',
              eventAction: 'delete',
              eventLabel: 'Purge single favorites'
            });
        });

        $(".js-purge-all").on('click', function(event) {
            ga('send', {
              hitType: 'event',
              eventCategory: 'Favorites',
              eventAction: 'delete-all',
              eventLabel: 'Purge all favorites'
            });
        });


    </script>

@stop