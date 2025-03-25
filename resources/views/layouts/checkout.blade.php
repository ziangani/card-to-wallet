<!Doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{config('app.name')}}</title>
    <!-- CSS files -->
    <link href="{{asset('assets/css/tabler.css')}}" rel="stylesheet"/>
    <link href="{{asset('assets/libs/sweatalert/sweetalert.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/libs/dropzone/dropzone.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/libs/bootstrap-datepicker/css/bootstrap-datepicker3.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/techpay.css?nocache=' )}}" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
    @yield('css')
    <script>
        let API_GATEWAY = '{{url('')}}/';
    </script>
</head>

<body class="backend">
<div class="page page-center page-loader">
    <div class="container container-slim py-4">
        <div class="text-center">
            <div class="mb-3">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="{{url('assets/img/logo.png')}}" height="56" alt=""></a>
            </div>
            <div class="text-secondary mb-3">Preparing application</div>
            <div class="progress progress-sm">
                <div class="progress-bar progress-bar-indeterminate"></div>
            </div>
        </div>
    </div>
</div>
<div class="page">
    <div class="page-wrapper">
        @yield('body')
    </div>
</div>

<script src="{{asset('assets/js/tabler.min.js')}}" defer></script>
<script src="{{asset('assets/libs/jquery/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('assets/libs/blockui/jquery.blockUI.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/libs/sweatalert/sweetalert.min.js')}}"></script>

<script src="{{asset('assets/js/techpay.js')}}"></script>
@yield('scripts')



@if(app()->environment('production'))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H3NK1421E8"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-H3NK1421E8');
    </script>
@endif

</body>
</html>
