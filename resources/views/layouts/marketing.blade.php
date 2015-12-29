@include('layouts._head')

<body class="layouts-marketing">

    @include('layouts._nav')

        <div class="container">

            @yield('content')

        </div>
        @include('layouts._footer', ['class' => 'footer-marketing'])

@include('layouts._tail')