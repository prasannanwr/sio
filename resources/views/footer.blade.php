{{--
<a href="#" class="btn btn-lg btn-primary back-to-top"><i class="bi bi-arrow-up"></i></a>
--}}
<footer class="w-full p-20 bg-slate-950">
    <div class="container">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:grid-cols-3">

            <style>
                .social-icons {
                    display: flex;
                    list-style-type: none;
                    padding: 0;
                }

                .social-icons li {
                    margin-right: 10px;
                }

                .social-icons a {
                    text-decoration: none;
                    color: #ffffff;
                    font-size: 24px;
                }
            </style>
            <div>
                <a class="navbar-brand" href="/">
                    <img src="{{ asset('landing/assets/img/gallery/sioshipping_logo.png') }}"  class="h-28 bg-white p-1.5 rounded-xl"
                    alt="logo">
                </a>
                <p style="color: #ffffff">
                    {{ __('hompage.127') }} <a
                        href="{{ route('contact') }}">{{ __('hompage.126.name') }}</a> {{ __('hompage.127a') }}</p>
                <div class="social-icons-container">
                    <ul class="social-icons">
                        <li><a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                    </ul>
                </div>
            </div>

            <div class="">
                <h3 class="text-primary mb-3">{{ __('hompage.128') }}</h3>
                <div class="d-flex flex-column">
                    <a class="text-white mb-2" href="{{ route('landing') }}">{{ __('nav.1') }}</a>
                    <a class="text-white mb-2" href="{{ route('who_we_are') }}">{{ __('nav.2') }}</a>
                    <a class="text-white mb-2" href="{{ route('shipping_page') }}">{{ __('hompage.42') }}</a>
                    <a class="text-white mb-2" href="{{ route('pick_up_point_page') }}">{{ __('hompage.46') }}</a>
                    <a class="text-white mb-2" href="{{ route('blogs') }}">{{ __('nav.11') }}</a>
                </div>
            </div>
            <div class="">
                <h3 class="text-primary mb-3">{{ __('hompage.129') }}</h3>
                <div class="d-flex flex-column">
                    <a class="text-white mb-2" target="_blank" href="https://siopay.eu">
                        {{ __('hompage.130') }}</a>
                    <a class="text-white mb-2" target="_blank" href="https://siostore.eu">
                        {{ __('hompage.131') }}</a>
                    <a class="text-white mb-2" target="_blank"
                        href="https://dash.siodatacheck.com/">{{ __('hompage.132') }}</a>
                </div>
            </div>

        </div>
        <br>
        <div class="flex justify-between flex-col md:flex-row">

            <div class="col-lg-6 text-left text-lg-start mb-3 mb-lg-0">
                <p class="m-0 text-white">&copy; <a href="#">{{ env('APP_NAME') }}</a> MUTISERVIZI S.R.L</p>
            </div>
            <div class="p-2">
                <ul class="nav d-inline-flex">
                    <li class="nav-item"><a class="nav-link text-white py-0" href="{{ route('privacy') }}">{{ __('hompage.133') }}</a></li>
                    <li class="nav-item"><a class="nav-link text-white py-0" href="{{ route('terms') }}">{{ __('hompage.134') }}</a></li>
                    <li class="nav-item" style="margin-top: -6px;"> 
                        <div class="btn-group dropup">
                          <button type="button" class="btn btn-link dropdown-toggle text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('hompage.120') }}
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('order_guide') }}">{{ __('hompage.121') }}</a>
                            <a class="dropdown-item" href="{{ route('how_to_pack') }}">{{ __('hompage.122') }}</a>
                            <a class="dropdown-item" href="{{ route('how_to_measure') }}">{{ __('hompage.123') }}</a>
                            <a class="dropdown-item" href="{{ route('account_remove_info') }}">{{ __('hompage.142') }}</a>
                            <a class="dropdown-item" href="{{ route('prohibited_goods') }}">{{ __('hompage.124') }}</a>
                          </div>
                        </div></li>
                </ul>
            </div>

        </div>

    </div>
</footer>
<script>
    var but = document.querySelector('.navbar-toggler')

    but.addEventListener('click', () => {
        let menu = document.getElementById('navbarSupportedContent')
        if (menu.style.display != 'block') {
            menu.style.display = 'block'
        } else {
            menu.style.display = 'none'
        }
    })

    const ddp = document.querySelector('#navbarDropdown')

    ddp.addEventListener('click', () => {
        let menu = document.querySelector('.service-menu')
        if (menu.style.display != 'block') {
            menu.style.display = 'block'
        } else {
            menu.style.display = 'none'
        }
    })

    const lang = document.querySelector('#langDropdown')

    lang.addEventListener('click', () => {
        let menu = document.querySelector('.lang-dp')
        if (menu.style.display != 'block') {
            menu.style.display = 'block'
        } else {
            menu.style.display = 'none'
        }
    })
</script>
<script src="{{ asset('admin_assets/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('landing2/lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('landing2/lib/waypoints/waypoints.min.js') }}"></script>
<script src="{{ asset('landing2/lib/counterup/counterup.min.js') }}"></script>
<script src="{{ asset('landing2/lib/owlcarousel/owl.carousel.min.js') }}"></script>

<!-- Contact Javascript File -->
<script src="{{ asset('landing2/mail/jqBootstrapValidation.min.js') }}"></script>
<script src="{{ asset('landing2/mail/contact.js') }}"></script>

<!-- Template Javascript -->
<script src="{{ asset('landing2/js/main.js') }}"></script>
