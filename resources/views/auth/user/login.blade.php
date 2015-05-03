@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-6 col-md-offset-3 well">

        {!! Form::open(['route' => 'auth.login.handle', 'method' => 'post']) !!}

        <div class="form-group">
            {!! Form::label('email', 'Email') !!}
            {!! Form::email('email', null, ['class' => 'form-control'])!!}

        </div>

        <div class="form-group">
            {!! Form::label('password', 'Passwort') !!}
            {!! Form::password('password', ['class' => 'form-control'])!!}
        </div>

        {!! Form::button('Login', ['class' => 'btn btn-success', 'type' => 'submit']) !!}


        {!! Form::close() !!}


    </div>
</div>

@stop