<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="generator" content="Mobirise v5.8.14, mobirise.com">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <meta name="description" content="">
    <meta name="facebook-domain-verification" content="tzt0oaxx29rjpen78k30jn6zk73g6c"/>

    <title>Welcome</title>

    <!-- Favicons -->
    <link rel="icon" href="{{ asset($header['logo'] ?? 'assets/frontend/img/logo-1.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/frontend/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/frontend/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/frontend/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/frontend/vendor/animate.css/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/frontend/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/frontend/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">


    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/frontend/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets/frontend/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/vendor/swiper/swiper-bundle.min.js') }}"></script>



    <!-- Main CSS File -->
    <link href="{{ asset('assets/frontend/css/main.css') }}" rel="stylesheet">
</head>

<body class="blog-page">
<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="logo d-flex align-items-center">
            <img src="{{ asset($header['logo'] ?? 'assets/frontend/img/logo-1.png') }}" class="logo-img"/>
        </a>

        <!-- Mobile Nav Toggle -->
        <button id="sidebarToggle" class="mobile-nav-toggle d-xl-none">
            <i class="bi bi-list"></i>
        </button>

        <!-- Regular Nav Menu (visible on desktop) -->
        <nav id="navmenu" class="navmenu d-none d-xl-flex">
            <ul>
                @foreach($header['navmenu']['links'] ?? [] as $link)
                    <li><a href="{{ url($link['href']) }}">{{ $link['text'] }}</a></li>
                @endforeach
            </ul>
            <div class="nav-buttons">
                @foreach($header['navmenu']['buttons'] ?? [] as $button)
                    <a href="{{ url($button['href']) }}"
                        class="btn-{{ strtolower(str_replace(' ', '-', $button['text'])) }}">{{ $button['text'] }}</a>
                @endforeach
                <div id="signup-form">
                    <signup-form></signup-form>
                </div>
            
                <a href="{{url('terms')}}" class="btn-sign-up">Terms</a>
            </div>
        </nav>

        <!-- Mobile Sidebar -->
        <div id="sidebar" class="sidebar">
            <button id="close-btn" class="close-btn">
                <i class="bi bi-x"></i>
            </button>
            <ul>
                @foreach($header['navmenu']['links'] ?? [] as $link)
                    <li><a href="{{ url($link['href']) }}">{{ $link['text'] }}</a></li>
                @endforeach
            </ul>
            <div class="nav-buttons">
                @foreach($header['navmenu']['buttons'] ?? [] as $button)
                    <a href="{{ url($button['href']) }}" 
                        class="btn-{{ strtolower(str_replace(' ', '-', $button['text'])) }}">
                        {{ $button['text'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</header>
<main class="main">

    <!-- ======= Hero Section ======= -->
    <section id="hero" class="hero">
        <div class="slides">
            @forelse($hero as $slide)
                <div class="slide" style="background-image: url('{{ $slide['backgroundImage'] }}');">
                    <div class="hero-content">
                        <h1>{{ $slide['h1'] }}</h1>
                        <p>{{ $slide['p'] }}</p>
                        <div class="button-container">
                            @foreach($slide['buttons'] as $button)
                                <a href="{{ url($button['href']) }}"
                                   class="btn-get-started {{ $button['class'] ?? '' }}">{{ $button['text'] }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="slide">
                    <div class="hero-content">
                        <h1>Welcome to Techpay</h1>
                        <p>Empowering businesses with seamless payment solutions.</p>
                        <div class="button-container">
                            <a href="#about" class="btn-get-started">Get Started</a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </section>


    <!-- Services Section -->
    <section id="services" class="services section">
        <div class="container section-title" data-aos="fade-up">
            <h2>{{ $services['sectionTitle']['h2'] ?? 'Our Services' }}</h2>
            <p>{{ $services['sectionTitle']['p'] ?? 'Discover our range of payment solutions' }}</p>
        </div>

        <div class="container" data-aos="fade-up">
            <div class="swiper init-swiper">
                <script type="application/json" class="swiper-config">
                    {
                        "loop": true,
                        "speed": 600,
                        "autoplay": {
                            "delay": 5000
                        },
                        "slidesPerView": "auto",
                        "pagination": {
                            "el": ".swiper-pagination",
                            "type": "bullets",
                            "clickable": true
                        },
                        "breakpoints": {
                            "320": {
                                "slidesPerView": 1,
                                "spaceBetween": 10
                            },
                            "480": {
                                "slidesPerView": 2,
                                "spaceBetween": 10
                            },
                            "640": {
                                "slidesPerView": 3,
                                "spaceBetween": 15
                            },
                            "992": {
                                "slidesPerView": 4,
                                "spaceBetween": 20
                            }
                        }
                    }
                </script>
                <div class="swiper-wrapper">
                    @forelse($services['serviceItems'] ?? [] as $item)
                        <div class="swiper-slide">
                            <div class="service-item position-relative">
                                <div class="icon">
                                    <i class="{{ $item['icon'] }}"></i>
                                </div>
                                <h3>{{ $item['h3'] }}</h3>
                                <p>{{ $item['p'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div class="col-12 text-center">
                                <p>No services available at the moment.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section><!-- /Services Section -->

    <!-- ======= What We Offer Section ======= -->
    <section id="what-we-offer" class="what-we-offer">
        <div class="container section-title" data-aos="fade-up">
            <h2>{{ $whatWeOffer['title'] ?? 'Our Services' }}</h2>
            <p>{{ $whatWeOffer['subtitle'] ?? 'What we do offer' }}</p>
        </div><!-- End Section Title -->

        <div class="container">
            <div class="row">
                @forelse($whatWeOffer['offers'] ?? [] as $offer)
                    <div class="col-md-4 mb-4">
                        <div class="offer-box">
                            <img src="{{ $offer['image'] }}" alt="{{ $offer['altText'] }}" class="offer-img">
                            <div class="offer-info">
                                <h4>{{ $offer['title'] }}</h4>
                                <p>{{ $offer['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>No offers available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- End What We Offer Section -->

    <!-- Usage Section -->
<section id="usage" class="usage section">
    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ $usage['title'] ?? 'Usage' }}</h2>
        <p>{{ $usage['subtitle'] ?? 'How to use our product' }}</p>
    </div><!-- End Section Title -->

    <div class="container" data-aos="fade-up">
        <div class="swiper init-swiper">
            <script type="application/json" class="swiper-config">
                {
                    "loop": true,
                    "speed": 600,
                    "autoplay": {
                        "delay": 5000
                    },
                    "slidesPerView": "auto",
                    "pagination": {
                        "el": ".swiper-pagination",
                        "type": "bullets",
                        "clickable": true
                    },
                    "breakpoints": {
                        "320": {
                            "slidesPerView": 1,
                            "spaceBetween": 10
                        },
                        "480": {
                            "slidesPerView": 2,
                            "spaceBetween": 10
                        },
                        "640": {
                            "slidesPerView": 3,
                            "spaceBetween": 15
                        },
                        "992": {
                            "slidesPerView": 4,
                            "spaceBetween": 20
                        }
                    }
                }
            </script>
            <div class="swiper-wrapper">
                @forelse($usage['examples'] ?? [] as $example)
                    <div class="swiper-slide">
                        <div class="usage-member">
                            <div class="member-img">
                                <img src="{{ $example['image'] ?? '' }}" class="img-fluid"
                                     alt="{{ $example['alt'] ?? '' }}">
                                <div class="sign-up">
                                    <a href="/sign-up">
                                        <button class="sign-up-btn">{{ $example['button_text'] ?? 'Sign Up' }}</button>
                                    </a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4 style="color: #8CC63F; text-align: center; font-size: 25px;">{{ $example['title'] ?? '' }}</h4>
                                <p>{{ $example['description'] ?? '' }}</p>
                            </div>
                        </div>
                    </div><!-- End Usage Member -->
                @empty
                    <div class="swiper-slide">
                        <div class="col-12 text-center">
                            <p>No usage examples available at the moment.</p>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section><!-- /Usage Section -->

<!-- Clients Section -->
<section id="clients" class="clients section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="swiper init-swiper">
            <script type="application/json" class="swiper-config">
                {
                    "loop": true,
                    "speed": 600,
                    "autoplay": {
                        "delay": 5000
                    },
                    "slidesPerView": "auto",
                    "pagination": {
                        "el": ".swiper-pagination",
                        "type": "bullets",
                        "clickable": true
                    }
                }
            </script>
            <div class="swiper-wrapper">
                <!-- Client slides go here -->
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section><!-- /Clients Section -->

    <!-- Clients Section -->
    <section id="clients" class="clients section">

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="swiper init-swiper">
                <script type="application/json" class="swiper-config">
                    {
                        "loop": true,
                        "speed": 600,
                        "autoplay": {
                            "delay": 5000
                        },
                        "slidesPerView": "auto",
                        "pagination": {
                            "el": ".swiper-pagination",
                            "type": "bullets",
                            "clickable": true
                        },
                        "breakpoints": {
                            "320": {
                                "slidesPerView": 2,
                                "spaceBetween": 5
                            },
                            "480": {
                                "slidesPerView": 3,
                                "spaceBetween": 10
                            },
                            "640": {
                                "slidesPerView": 4,
                                "spaceBetween": 15
                            },
                            "992": {
                                "slidesPerView": 6,
                                "spaceBetween": 20
                            }
                        }
                    }
                </script>
                <div class="swiper-wrapper align-items-center">
                    <div class="swiper-slide"><a href="{{ url('https://payeasy.techpay.co.zm/fm/9') }}"><img src="{{ asset('assets/frontend/img/logos/expo.png') }}" class="img-fluid" alt=""></a></div>
                    <div class="swiper-slide"><a href="{{ url('https://payeasy.techpay.co.zm/fm/6') }}"><img src="{{ asset('assets/frontend/img/logos/billiard-training-academy.png') }}" class="img-fluid" alt=""></a></div>
                    <div class="swiper-slide"><a href="{{ url('https://payeasy.techpay.co.zm/fm/9') }}"><img src="{{ asset('assets/frontend/img/logos/companion_wealth_wcw.png') }}" class="img-fluid" alt=""></a></div>
                    <div class="swiper-slide"><a href="{{ url('https://www.liberty.co.zm/Default.aspx') }}"><img src="{{ asset('assets/frontend/img/logos/liberty-insurance.png') }}" class="img-fluid" alt=""></a></div>
                    <div class="swiper-slide"><a href="{{ url('https://payeasy.techpay.co.zm/fm/8') }}"><img src="{{ asset('assets/frontend/img/logos/lochinvar.png') }}" class="img-fluid" alt=""></a></div>
                    <div class="swiper-slide"><a href="{{ url('https://payeasy.techpay.co.zm/fm/7') }}"><img src="{{ asset('assets/frontend/img/logos/unza_beca.png') }}" class="img-fluid" alt=""></a></div>
                    <div class="swiper-slide"><a href="{{ url('https://www.zimmarketing.org.zm/') }}"><img src="{{ asset('assets/frontend/img/logos/zim-logo.png') }}" class="img-fluid" alt=""></a></div>
                    <div class="swiper-slide"><a href="{{ url('http://zam.co.zm/') }}"><img src="{{ asset('assets/frontend/img/logos/zam.png') }}" class="img-fluid" alt=""></a></div>
                    <div class="swiper-slide"><a href="{{ url('https://payeasy.techpay.co.zm/fm/9') }}"><img src="{{ asset('assets/frontend/img/logos/overhill.png') }}" class="img-fluid" alt=""></a></div>
                </div>
                <div class="swiper-pagination"></div>

            </div>

        </div>

    </section><!-- /Clients Section -->

    <!-- ======= Prices Section ======= -->
    <section id="prices" class="prices">
        <div class="container">
            <div class="container section-title" data-aos="fade-up">
                <h2>Prices </h2>
                <p>We do offer awesome Prices</p>
            </div><!-- End Section Title -->
            <div class="row justify-content-center prices-content">
                <div class="image-column">
                    <img src="{{asset('assets/frontend/img/17.jpg')}}" alt="Card Images" class="card-image">
                </div>

                <div class="pricing-column">
                    <div class="price-card icon-box">
                        <div class="price-header">
                            <span>Card Checkout</span>
                        </div>
                        <div class="price-body">
                            <div class="price-row">
                                <p>Accept Visa, MasterCard & other major Cards</p>
                                <div class="price">3.5%<span>/transaction</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="price-card icon-box">
                        <div class="price-header">
                            <span>Mobile Money</span>
                        </div>
                        <div class="price-body">
                            <div class="price-row">
                                <p>We support MTN, Airtel and Zamtel Money</p>
                                <div class="price">2.5%<span>/transaction</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End Prices Section -->


    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>Get in touch with us</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">
          <div class="col-lg-6 ">
            <div class="row gy-4">

              <div class="col-lg-12">
                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="200">
                  <i class="bi bi-geo-alt"></i>
                  <h3>Address</h3>
                  <p>Plot No. 37861, Alick Nkhata Road, MassMedia, Lusaka, Zambia</p>
                </div>
              </div><!-- End Info Item -->

              <div class="col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
                  <i class="bi bi-telephone"></i>
                  <h3>Call Us</h3>
                  <div class="d-flex justify-content-between" style="font-size: 0.9em;">
                    <p>+260 764 188 678 || +260 764 188 670</p>
                  </div>
                </div>
              </div><!-- End Info Item -->

              <div class="col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
                  <i class="bi bi-envelope"></i>
                  <h3>Email Us</h3>
                  <p>info@techpay.com</p>
                </div>
              </div><!-- End Info Item -->

            </div>
          </div>

          <div class="col-lg-6">
            <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="500">
              <div class="row gy-4">

                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
                </div>

                <div class="col-md-6 ">
                  <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
                </div>

                <div class="col-md-12">
                  <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="4" placeholder="Message" required=""></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading" style="display: none;">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message" style="display: none;">Your message has been sent. Thank you!</div>

                  <button type="submit">Send Message</button>
                </div>

              </div>
            </form>
          </div><!-- End Contact Form -->

<script>
  const form = document.querySelector('.php-email-form');
  const loading = document.querySelector('.loading');
  const sentMessage = document.querySelector('.sent-message');

  form.addEventListener('submit', function(event) {
    event.preventDefault();
    loading.style.display = 'block';
    sentMessage.style.display = 'none';

    // Simulate sending the message
    setTimeout(function() {
      loading.style.display = 'none';
      sentMessage.style.display = 'block';
    }, 2000);
  });
</script>

        </div>

      </div>

    </section><!-- /Contact Section -->

</main>


<!-- ======= Footer ======= -->
<footer id="footer" class="footer dark-background">

    <div class="container">
        <div class="copyright">
            &copy; {{ date('Y') }} <strong><span>TechPay Limited</span></strong>. All Rights Reserved
        </div>
    </div>
</footer>
<!-- End Footer -->


<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- Main JS File -->
<script src="{{ asset('assets/frontend/js/main.js') }}"></script>
@vite('resources/js/app.js')

</body>

</html>
