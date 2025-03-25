<footer class="footer footer-transparent d-print-none fw-bold text-white">
    <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-lg-auto ms-lg-auto">
                <ul class="list-inline list-inline-dots mb-0">
                   <li class="list-inline-item">
                        <span class="" rel="noopener">
                            Powered by: <a style="color: #333; font-size: 16px; font-weight: bold;" target="_blank" href="{{url('https://techpay.co.zm/public/?ref=' . config('app.name'))}}">
                               <img src="{{url('assets/img/techpay.png')}}" width="70px">
                            </a>
                        </span>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        Copyright &copy; {{date('Y')}}
                        <span class="">{{config('app.name')}}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
