@include('layouts._head')

<body>

    @include('layouts._nav')

        <div class="container">

            @yield('content')

        </div>

        @include('layouts._footer')

@include('layouts._tail')