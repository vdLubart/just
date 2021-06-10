<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-eval' https://www.google.com http://www.google.com https://www.gstatic.com" -->

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Just! use it - {{ $page->title }}</title>

        <meta name="description" content="{{ $page->description }}">
        <meta name="keywords" content="{{ $page->keywords }}">
        <meta name="author" content="{{ $page->author }}">
        <meta name="copyright" content="{{ $page->copyright }}">

        <link href="{{ mix('/css/Just/app.css') }}" rel="stylesheet">

        <script src="{{ mix('/js/Just/app.js') }}"></script>
        @if(\Config::get('isAdmin'))
            <script src="/js/settings.js"></script>
            <script src="/js/dragula.min.js"></script>
            <link href="/css/dragula.css" rel="stylesheet">
        @endif

        @php
        $scripts = [];
        foreach($panels as $panel){
            foreach($panel->blocks() as $panelBlock){
                $scripts += $panelBlock->item()->neededJavaScripts();
            }
        }

        foreach(array_unique($scripts) as $script){
            echo "<script src='".$script."'></script>";
        }
        @endphp
    </head>
    <body>
    <div id="app">
        @if(\Config::get('isAdmin'))
            @include(viewPath($layout, 'navbar'))
        @endif

        @foreach($panels as $panel)
            @include(viewPath($layout, $panel))
        @endforeach

        @if(\Config::get('isAdmin'))
            <div id="settings">
                <settings ref="settings"></settings>
            </div>
        @endif
    </div>

    @if(\Config::get('isAdmin'))
        <script>
            window.settingsTranslations = {!! collect(cache('settings-translations')) !!};
        </script>

        <script src="{{ mix('/js/Just/adminApp.js') }}"></script>
    @endif

    @foreach($page->blockScripts() as $script)
        {!! "<script src='".$script."'></script>" !!}
    @endforeach
    </body>
</html>
