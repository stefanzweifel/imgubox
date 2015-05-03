@extends('layouts.master')

@section('content')

    <div class="page-header">
        <h1>Settings</h1>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">Tokens</div>
        <div class="panel-body">

            <table class="table">
                <tr>
                    <td>Imgur</td>
                    <td class="text-right">
                        @if (Auth::user()->imgurTokens()->count() > 0)
                            <a href="/auth/imgur/delete" class="btn btn link">Delete Token</a>
                        @else
                            <a href="/auth/imgur" class="btn btn-success">Connect</a>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Dropbox</td>
                    <td class="text-right">
                        @if (Auth::user()->dropboxTokens()->count() > 0)
                            <a href="/auth/dropbox/delete" class="btn btn link">Delete Token</a>
                        @else
                            <a href="/auth/dropbox" class="btn btn-success">Connect</a>
                        @endif
                    </td>
                </tr>
            </table>


        </div>
    </div>


@stop