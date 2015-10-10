@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-6 col-md-offset-3 well">

        {!! Form::open(['url' => 'password/email']) !!}

            <div class="form-group @if ($errors->has('email')) has-error @endif">
                {!! Form::label('email', 'Email') !!}
                {!! Form::email('email', null, ['class' => 'form-control'])!!}
                @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
            </div>

            {!! Form::button('Reset password', ['class' => 'btn btn-success', 'type' => 'submit']) !!}

        {!! Form::close() !!}

    </div>
</div>

@stop