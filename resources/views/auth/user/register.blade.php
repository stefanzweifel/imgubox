@extends('layouts.master')

@section('content')


{!! Form::open(['route' => 'auth.register.handle', 'method' => 'post']) !!}

<div class="form-group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::email('email', null, ['class' => 'form-control'])!!}

</div>

<div class="form-group">
    {!! Form::label('password', 'Passwort') !!}
    {!! Form::password('password', ['class' => 'form-control'])!!}
</div>

<div class="form-group">
    {!! Form::label('password_confirmation', 'Passwort Confirmation') !!}
    {!! Form::password('password_confirmation', ['class' => 'form-control'])!!}
</div>


{!! Form::button('Register', ['class' => 'btn btn-success', 'type' => 'submit']) !!}


{!! Form::close() !!}

@stop