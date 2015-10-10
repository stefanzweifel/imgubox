@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-6 col-md-offset-3 well">

        {!! Form::open(['url' => 'password/reset']) !!}

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {!! Form::label('email', 'Email') !!}
                {!! Form::email('email', null, ['class' => 'form-control'])!!}
                @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
            </div>

            <div class="form-group @if ($errors->has('password')) has-error @endif">
                {!! Form::label('password', 'Password') !!}
                {!! Form::password('password', ['class' => 'form-control'])!!}
                @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
            </div>

            <div class="form-group @if ($errors->has('password')) has-error @endif">
                {!! Form::label('password_confirmation', 'Confirm Password') !!}
                {!! Form::password('password_confirmation', ['class' => 'form-control'])!!}
                @if ($errors->has('password_confirmation')) <p class="help-block">{{ $errors->first('password_confirmation') }}</p> @endif
            </div>

            {!! Form::button('Reset password', ['class' => 'btn btn-success', 'type' => 'submit']) !!}

        {!! Form::close() !!}

    </div>
</div>

@stop