<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <strong>Just!</strong> use it
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                &nbsp;
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                <li><a href="{{ route('login') }}">Login</a></li>
                @else
                @if(\Auth::user()->role == "master")
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-table"></i>
                        Layouts <span class="caret"></span>
                    </a>
                    
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="/admin/layout/create"
                               onclick="event.preventDefault();
                                                    openLayoutSettings(0);">
                                <i class="fa fa-plus"></i>
                                Create layout
                            </a>
                        </li>
                        <li>
                            <a href="/admin/layout/list"
                               onclick="event.preventDefault();
                                                    openLayoutList();">
                                <i class="fa fa-list"></i>
                                Layout list
                            </a>
                        </li>
                        <li>
                            <a href="/admin/layout/settings"
                               onclick="event.preventDefault();
                                                    openLayoutSettings({{ $layout->id }});">
                                <i class="fa fa-cogs"></i>
                                Layout settings
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-file-text"></i>
                        Pages <span class="caret"></span>
                    </a>
                    
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="/admin/page/create"
                               onclick="event.preventDefault();
                                                    openPageSettings(0);">
                                <i class="fa fa-plus"></i>
                                Create page
                            </a>
                        </li>
                        <li>
                            <a href="/admin/page/list"
                               onclick="event.preventDefault();
                                                    openPageList();">
                                <i class="fa fa-list"></i>
                                Page list
                            </a>
                        </li>
                        <li>
                            <a href="/admin/page/settings"
                               onclick="event.preventDefault();
                                                    openPageSettings({{ $page->id }});">
                                <i class="fa fa-cogs"></i>
                                Page settings
                            </a>
                        </li>
                    </ul>
                </li>
                @if(\Auth::user()->role == "master")
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-puzzle-piece"></i>
                        Addons <span class="caret"></span>
                    </a>
                    
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="#"
                               onclick="event.preventDefault();
                                                    openAddonSettings(0);">
                                <i class="fa fa-plus"></i>
                                Add addon
                            </a>
                        </li>
                        <li>
                            <a href="#"
                               onclick="event.preventDefault();
                                                    openAddonList();">
                                <i class="fa fa-list"></i>
                                Addon list
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-user-circle"></i>
                        {{ Auth::user()->name }}:{{ \Auth::user()->role }} <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="#"
                               onclick="event.preventDefault();
                                                    openChangePassword();">
                                <i class="fa fa-lock"></i>
                                Change Password
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i>
                                Logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>