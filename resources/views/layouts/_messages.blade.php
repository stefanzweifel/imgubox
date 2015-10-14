@if ($errors->has())
    <div class="alert alert-danger">
        <strong>Something's wrong with your input:</strong><br>
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif

@if (Session::has('error'))
    <div class="alert alert-danger">
        {!! session('error') !!}
    </div>
@endif

@if (Session::has('message'))
    <div class="alert alert-info">
        {!! session('message') !!}
    </div>
@endif

@if (Session::has('success'))
    <div class="alert alert-success">
        {!! session('success') !!}
    </div>
@endif