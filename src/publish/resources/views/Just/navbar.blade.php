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
                <li><a href="{{ route('login') }}">@lang('Login')</a></li>
                @else
                @if(\Auth::user()->role == "master")
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-paint-brush"></i>
                        @lang('navbar.layouts.top') <span class="caret"></span>
                    </a>
                    
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <slink href="/settings/layout/0">
                                <i class="fa fa-plus"></i>
                                @lang('navbar.layouts.create')
                            </slink>
                        </li>
                        <li>
                            <slink href="/settings/layout/list">
                                <i class="fa fa-list"></i>
                                @lang('navbar.layouts.list')
                            </slink>
                        </li>
                        <li>
                            <slink href="/settings/layout/{{ $layout->id }}">
                                <i class="fa fa-cogs"></i>
                                @lang('navbar.layouts.settings')
                            </slink>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-sitemap"></i>
                        @lang('navbar.pages.top') <span class="caret"></span>
                    </a>
                    
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="/settings/page/create"
                               onclick="event.preventDefault();
                                                    openSettings('page', 0);">
                                <i class="fa fa-plus"></i>
                                @lang('navbar.pages.create')
                            </a>
                        </li>
                        <li>
                            <a href="/settings/page/list"
                               onclick="event.preventDefault();
                                                    openList('page');">
                                <i class="fa fa-list"></i>
                                @lang('navbar.pages.list')
                            </a>
                        </li>
                        <li>
                            <a href="/settings/page/settings"
                               onclick="event.preventDefault();
                                                    openSettings('page', {{ $page->id }});">
                                <i class="fa fa-cogs"></i>
                                @lang('navbar.pages.settings')
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-th-list"></i>
                        @lang('navbar.categories.top') <span class="caret"></span>
                    </a>
                    
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="/admin/category/create"
                               onclick="event.preventDefault();
                                                    openSettings('category', 0);">
                                <i class="fa fa-plus"></i>
                               @lang('navbar.categories.create')
                            </a>
                        </li>
                        <li>
                            <a href="/admin/category/list"
                               onclick="event.preventDefault();
                                                    openList('category');">
                                <i class="fa fa-list"></i>
                                @lang('navbar.categories.list')
                            </a>
                        </li>
                    </ul>
                </li>
                @if(\Auth::user()->role == "master")
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-puzzle-piece"></i>
                        @lang('navbar.addons.top') <span class="caret"></span>
                    </a>
                    
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="#"
                               onclick="event.preventDefault();
                                                    openSettings('addon', 0);">
                                <i class="fa fa-plus"></i>
                                @lang('navbar.addons.add')
                            </a>
                        </li>
                        <li>
                            <a href="#"
                               onclick="event.preventDefault();
                                                    openList('addon');">
                                <i class="fa fa-list"></i>
                                @lang('navbar.addons.list')
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-users"></i>
                        @lang('navbar.users.top') <span class="caret"></span>
                    </a>
                    
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="#"
                               onclick="event.preventDefault();
                                                    openSettings('user', 0);">
                                <i class="fa fa-plus"></i>
                                @lang('navbar.users.add')
                            </a>
                        </li>
                        <li>
                            <a href="#"
                               onclick="event.preventDefault();
                                                    openList('user');">
                                <i class="fa fa-list"></i>
                                @lang('navbar.users.list')
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
                                @lang('navbar.user.changePassword')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out-alt"></i>
                                @lang('navbar.user.logout')
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