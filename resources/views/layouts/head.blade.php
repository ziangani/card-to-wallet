<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{config('app.name')}}</title>
    <!-- CSS files -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{asset('assets/css/tabler.css')}}" rel="stylesheet"/>
    <link href="{{asset('assets/libs/sweatalert/sweetalert.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/libs/dropzone/dropzone.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/libs/bootstrap-datepicker/css/bootstrap-datepicker3.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/techpay.css?nocache=' . time())}}" rel="stylesheet"/>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
    <style>
        :root {
            --tblr-font-sans-serif:  "Red Hat Text", BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-family: "Red Hat Text", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            font-size: 1rem;
            font-feature-settings: "liga" 1;
        }

    </style>
    @yield('css')
    <script>
        let API_GATEWAY = '{{url('')}}/';
    </script>
</head>
