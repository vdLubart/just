<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Just! use it</title>

        <link href="{{ mix('/css/Just/app.css') }}" rel="stylesheet">
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
        
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
                @include($layout->name.'.navbar')
            @endif

            @foreach($panels as $panel)
                @include(viewPath($layout, $panel))
            @endforeach
        </div>
        @if(\Config::get('isAdmin'))
        <div id="settings" class="settings">
            Loading data...
        </div>
        
        <div id="cropping" class="settings">
            Loading data...
        </div>
        @endif
    </body>
</html>
