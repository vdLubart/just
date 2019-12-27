<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
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
        <script src="/js/ckeditor/ckeditor.js"></script>
        <script src="/js/cropper.js"></script>
        <link href="/css/cropper.css" rel="stylesheet">
        <script src="/js/runCropper.js"></script>
        <script src="/js/jquery.form.js"></script>
        <script src="/js/settings.js"></script>
        <script src="/js/dragula.min.js"></script>
        <link href="/css/dragula.css" rel="stylesheet">
        @endif
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
                <settings ref="settings"></settings>
<!--
                <div id="cropping" class="settings">
                    <div class="loading">
                        Loading data...
                    </div>
                </div>
-->
            @endif
        </div>

        @if(\Config::get('isAdmin'))
            <script>
                window.settingsTranslations = {!! collect(cache('settings-translations')) !!};
            </script>

            <script src="{{ mix('/js/Just/adminApp.js') }}"></script>
        @endif
    </body>
</html>
