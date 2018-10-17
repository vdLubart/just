<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Just! use it</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet">
        <link href="/font/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">
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
        
        <script src="/js/jquery-1.11.1.min.js"></script>
        <script src="/js/app.js"></script>
        @if(\Config::get('isAdmin'))
        <script src="/ckeditor/ckeditor.js"></script>
        <script src="/js/cropper.js"></script>
        <link href="/css/cropper.css" rel="stylesheet">
        <script src="/js/runCropper.js"></script>
        <script src="/js/jquery.form.js"></script>
        <script src="/js/settings.js"></script>
        @endif
        
        <script src="/js/owl.carousel.js"></script>
        <link href="/css/owl.carousel.css" rel="stylesheet" type="text/css">
        <link href="/css/owl.theme.default.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="app">
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
                </div>
            </nav>
            
            <div class="main-content bg-sand">
                <div class="posts">
                    <div class="post">
                        <h3>Image Library</h3>
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a class="@if(!$errors->any()) collapsed @endif" data-toggle="collapse" data-parent="#accordion" href="#addCompanyForm">
                                            <i class="fa fa-plus"></i>
                                            Upload new image
                                        </a>
                                    </h4>
                                </div>
                                <div id="addCompanyForm" class="panel-collapse collapse @if($errors->any()) in @endif">
                                    <div class="panel-body">
                                        @if($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        {!! Form::open(array("url"=>"$action", 'files' => true)) !!}

                                        {!! Form::hidden('backlink', \Request::server('REQUEST_URI')); !!}
                                        @if(isset($case))
                                        {!! Form::hidden('case', $case); !!}
                                        @endif
                                        {!! Form::file('image');!!}
                                        <br/><br/>
                                        {!! Form::submit('Upload file');!!}

                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="portfolio_items">
                    @foreach($files as $src=>$file)
                    <div class="col-md-3">
                        <div class="thumbnail">
                            <a href="javascript:returnFileUrl('{{$src}}')" class="js-image-link" data-url="{{$src}}">
                                <img src="{{$src}}" width="200"/>
                            </a>

                            <div class="caption">
                                <h4>{{basename($src)}}</h4>
                                Uploaded: {{$file['modify']}}<br/>
                                Size: {{$file['size']}}, {{$file['width']}}x{{$file['height']}}
                                <p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <script>
                    function returnFileUrl(fileUrl) {
                        window.opener.CKEDITOR.tools.callFunction( {{\Request::get('CKEditorFuncNum')}}, fileUrl);
                        window.close();
                    }
                </script>
            </div>
        </div>
    </body>
</html>