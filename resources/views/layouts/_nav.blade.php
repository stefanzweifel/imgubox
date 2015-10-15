<nav class="navbar navbar-default navbar-branded">
    <div class="container">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">ImguBox <span class="logo-small">BETA</span></a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            @if (auth()->check())

                <ul class="nav navbar-nav">
                    <li><a href="/home">Dashboard</a></li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/about">About</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ auth()->user()->email }}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                        <li><a href="/settings">Settings</a></li>
                        <li class="divider"></li>
                        <li><a href="/auth/logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>

            @else

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/about">About</a></li>
                    <li><a href="/auth/login">Sign in</a></li>
                </ul>



            @endif

        </div>
    </div>
</nav>