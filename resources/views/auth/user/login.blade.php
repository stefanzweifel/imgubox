@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-6 col-md-offset-3 well">

        {!! Form::open(['route' => 'auth.login.handle', 'method' => 'post']) !!}

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


    </div>
</div>

@stop