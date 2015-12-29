@section("styles")
    <link rel="stylesheet" href="{{ elixir('css/marketing.css') }}">
@stop

@section("title")ImguBox @stop


@include('layouts._head')

<body class="t-marketing">

    <header>
        <nav class="navbar navbar-default c-navbar">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">ImguBox <span class="logo-small">BETA</span></a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <a href="/auth/login" class="btn btn-success navbar-btn">Sign in</a>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <section class="c-marketing-hero text-center">

            <h1 class="o-product-header">&laquo;Fav and Forget!&raquo;</h1>
            <p class="o-product-text">
                Connect your account with Imgur and Dropbox (more services coming soon). <br>
                We will then download your favorites so you can use them further.
            </p>

            <a href="/auth/register" class="btn btn-success o-btn-cta">Create your account now!</a>

            <div>
                <img src="/images/browser-preview.png" alt="" class="c-browser-preview img-responsive ">
            </div>

        </section>

        <section class="c-feature-grid">

            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="c-feature__item">
                        <h3>Free!</h3>
                        <p>Using ImguBox is free! Forever!</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-xs-6 col-md-offset-2">
                    <div class="c-feature__item">
                        <h3>Secure</h3>
                        <p>ImguBox uses the offical APIs of Imgur and Dropbox. We will never see your account password nor your private data.</p>
                    </div>
                </div>
                <div class="col-md-4 col-xs-6">
                    <div class="c-feature__item">
                        <h3>Easy to setup</h3>
                        <p>In just three easy steps ImguBox is ready for you: Create an account, link Imgur and Dropbox. Done!</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4  col-xs-6 col-md-offset-2">
                    <div class="c-feature__item">
                        <h3>Open Source</h3>
                        <p>The source code of ImguBox is Open Source and available on <a href="https://github.com/stefanzweifel/imgubox">Github</a>. If you’re a developer, feel free to open issues or pull requests.</p>
                    </div>
                </div>
                <div class="col-md-4 col-xs-6">
                    <div class="c-feature__item">
                        <h3>Have full control</h3>
                        <p>Think an image has not been saved correctly? Just hit a button and we download it for you again.</p>
                    </div>
                </div>
            </div>

        </section>
    </div>

    <div class="container">
        <footer class="c-footer">
            <ul class="list-inline">
                <li class="c-footer__list-item">Copyright &copy; {{ date("Y") }}</li>
                <li class="c-footer__list-item"><a href="/about">About</a></li>
                <li class="c-footer__list-item">Follow us <a href="https://twitter.com/imguboxapp" target="blank">@imguboxapp</a></li>
                <li>Yet another sideproject by <a href="https://twitter.com/_stefanzweifel" target="blank">@_stefanzweifel</a></li>
            </ul>
        </footer>
    </div>

@include('layouts._tail')

