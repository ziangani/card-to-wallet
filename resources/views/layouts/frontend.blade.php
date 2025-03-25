<!doctype html>
<html lang="en">
@include('layouts.head')
<body class=" d-flex flex-column frontend">
<div class="page page-center page-loader">
    <div class="container container-slim py-4">
        <div class="text-center">
            <div class="mb-3">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="{{url('/assets/img/logo.png')}}" height="56" alt=""></a>
            </div>
            <div class="text-secondary mb-3">Preparing application</div>
            <div class="progress progress-sm">
                <div class="progress-bar progress-bar-indeterminate"></div>
            </div>
        </div>
    </div>
</div>
@yield('body')


<script src="{{asset('assets/js/tabler.min.js')}}" defer></script>
<script src="{{asset('assets/libs/jquery/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('assets/libs/blockui/jquery.blockUI.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('assets/libs/dropzone/dropzone-min.js')}}"></script>
<script src="{{asset('assets/libs/sweatalert/sweetalert.min.js')}}"></script>

<script src="{{asset('assets/js/techpay.js?nocache=' . time())}}"></script>
@yield('scripts')
</body>
</html>
