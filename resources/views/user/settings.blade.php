@extends('layouts.master')

@section('title') Settings @stop

@section('content')

    <div class="page-header">
        <h2>Settings</h2>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Tokens</div>
        <div class="panel-body">

            <p>If ImguBox should no longer have access to your Imgur or Dropbox account, you can delete the access tokens here. (But don't forget to revoke access in your <a href="https://imgur.com/account/settings/apps">Imgur</a> and <a href="https://www.dropbox.com/account#security">Dropbox</a> account)</p>

        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>Added at</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Imgur</td>
                    <td>
                        @if ($user->imgurToken)
                            {!! $user->imgurToken->created_at->format('d.m.Y \a\t H:i') !!}
                            ({!! $user->imgurToken->created_at->timezoneName !!})
                        @else
                            Not added yet
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($user->imgurTokens()->count() > 0)
                            <a href="/auth/imgur/delete" class="btn btn-warning">Delete Token</a>
                        @else
                            <a href="/auth/imgur" class="btn btn-success">Connect</a>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Dropbox</td>
                    <td>
                        @if ($user->dropboxToken)
                            {!! $user->dropboxToken->created_at->format('d.m.Y \a\t H:i') !!}
                            ({!! $user->dropboxToken->created_at->timezoneName !!})
                        @else
                            Not added yet
                        @endif
                    </td>                    <td class="text-right">
                        @if ($user->dropboxTokens()->count() > 0)
                            <a href="/auth/dropbox/delete" class="btn btn-warning">Delete Token</a>
                        @else
                            <a href="/auth/dropbox" class="btn btn-success">Connect</a>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

    </div>


    <div class="panel panel-default">
        <div class="panel-heading">Reset password</div>
        <div class="panel-body">

            <p>You might reset your password here.</p>

            {!! Form::open(['route' => 'user.password.update']) !!}

                <div class="form-group @if ($errors->has('password')) has-error @endif">
                    {!! Form::label('password', 'Passwort') !!}
                    {!! Form::password('password', ['class' => 'form-control'])!!}
                    @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                </div>

                <div class="form-group @if ($errors->has('password_confirmation')) has-error @endif">
                    {!! Form::label('password_confirmation', 'Passwort Confirmation') !!}
                    {!! Form::password('password_confirmation', ['class' => 'form-control'])!!}
                    @if ($errors->has('password_confirmation')) <p class="help-block">{{ $errors->first('password_confirmation') }}</p> @endif
                </div>
                {!! Form::button('Update password', ['type' => 'submit', 'class' => 'btn btn-success']) !!}

            {!! Form::close() !!}

        </div>
    </div>


    <div class="panel panel-danger">
        <div class="panel-heading">Close Account</div>
        <div class="panel-body">

            <p>Unhappy with the service? You can close you account anytime. Just click the button below.</p>

            {!! Form::open(['route' => ['user.close_account']]) !!}
                {!! Form::button('Close account now', ['type' => 'submit', 'class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}



            <p class="help-block">We would love to here your feedback. Tweet us <a href="https://twitter.com/@imguboxapp">@imguboxapp</a> or email us at <a href="mailto:imgubox@wnx.ch">imgubox@wnx.ch</a></p>

        </div>
    </div>


@stop