<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand- d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{url('/')}}">
                <img src="{{url('assets/img/logo.png')}}" width="110" height="32" alt="{{config('app.name')}}"
                     class="navbar-brand-image">
{{--                {{config('app.name')}}--}}
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar">
                    <div class="container-xl">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="{{url('https://lamu.edu.zm/')}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon- icon-tabler icons-tabler-outline icon-tabler-file-search" width="24" height="24" viewBox="0 0 24 24"
                                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                         stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path
                                            d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path
                                            d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/></svg>
                                    <span class="nav-link-title">
                      Home
                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link open-tracker" href="#"  data-bs-toggle="modal" data-bs-target="#modalTracking">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round"
                                         class="icon- icon-tabler icons-tabler-outline icon-tabler-file-search">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                        <path d="M12 21h-5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v4.5"/>
                                        <path d="M16.5 17.5m-2.5 0a2.5 2.5 0 1 0 5 0a2.5 2.5 0 1 0 -5 0"/>
                                        <path d="M18.5 19.5l2.5 2.5"/>
                                    </svg>
                                    <span class="nav-link-title">
                                      Track Application
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</header>

