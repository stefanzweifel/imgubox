@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-6 col-md-offset-3 well">

        {!! Form::open(['url' => 'auth/login']) !!}

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {!! Form::label('email', 'Email') !!}
                {!! Form::email('email', null, ['class' => 'form-control'])!!}
                @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
            </div>

            <div class="form-group @if ($errors->has('password')) has-error @endif">
                {!! Form::label('password', 'Passwort') !!}
                {!! Form::password('password', ['class' => 'form-control'])!!}
                @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
            </div>

            {!! Form::button('Login', ['class' => 'btn btn-success', 'type' => 'submit']) !!}

        {!! Form::close() !!}

        <hr>

        <p>
            <a href="/auth/register">Sign up </a> or
            <a href="/password/email">reset password</a>.
        </p>
    </div>
</div>

@stop