<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-lg-auto ms-lg-auto">
                <ul class="list-inline list-inline-dots mb-0">
                   <li class="list-inline-item">
                        <span class="link-secondary" rel="noopener">
                            Powered by: <a style="color: #5b449b; font-weight: bold" target="_blank" href="{{url('https://techpay.co.zm/public/?ref=' . config('app.name'))}}">
                               <img src="{{url('/techpay.png')}}" width="80px">
                            </a>
                        </span>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        Copyright &copy; {{date('Y')}}
                        <span class="link-secondary">{{config('app.name')}}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
