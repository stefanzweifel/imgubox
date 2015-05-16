@include('layouts._head')

<body>

    @include('layouts._nav')

        <div class="container">

            @include('layouts._messages')

            @yield('content')

        </div>

        @include('layouts._footer')

@include('layouts._tail')