<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Siopay Logistics | @yield('page_title')</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">

    @yield('css')
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        @include('admin.sidebar')
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            @include('admin.header')
            <!--  Header End -->
            @yield('content')
        </div>
    </div>


    <script src="{{ asset('admin_assets/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('admin_assets/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin_assets/assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('admin_assets/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('admin_assets/assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <link
        href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-1.13.6/af-2.6.0/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fh-3.4.0/r-2.5.0/sc-2.2.0/sb-1.6.0/sp-2.2.0/sl-1.7.0/datatables.min.css"
        rel="stylesheet">

    {{--  <script>  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script
        src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-1.13.6/af-2.6.0/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fh-3.4.0/r-2.5.0/sc-2.2.0/sb-1.6.0/sp-2.2.0/sl-1.7.0/datatables.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/blitzer/jquery-ui.min.css"
        integrity="sha512-ibBo2Ns078qm7xZNTPbIrg5XP4pZ+Aujfmz0QFsce2f4LYpCnF1Esy6FkIRFBgXC9cY30XiS7Ui9+RpN8K7ICg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            getAllNotifications();

            setInterval(function() {
                getAllNotifications();
            }, 60000);

            function getAllNotifications() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('allNotifications') }}",
                    dataType: "json",
                    success: function(response) {
                        let notifications = response.notifications;
                        let html = '';
                        $.each(notifications, function(index, Element) {
                            let data = Element.data[0];
                            html += ` <div class="message-body d-flex align-items-center gap-3 mb-2">
                                        <div>
                                            <img style="width:30px; height:30px; border-radius:50%" src="{{ asset('admin_assets/assets/images/profile/user-1.jpg') }}"
                                                alt="">
                                        </div>
                                        <div>
                                            <b class="fw-bold">${data.user_name}</b>
                                            <p class="fw-bold mb-0">${data.body}</p>
                                            <a class="" href="${data.url}">View</a>
                                        </div>
                                    </div>`
                        });
                        html += ` <hr class="m-1">
                                <div class="text-center">
                                    <a class="btn-sm" href="#" id="markAsReadBtn">Mark as Read</a>
                                </div>`
                        $('#notificationDropDown').empty();
                        $('#notificationDropDown').html(html);
                        $('#notificationCounter').empty();
                        $('#notificationCounter').html(response.count);
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            }

            function markAllRead() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('markAsRead') }}",
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        getAllNotifications()
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            }

            $(document).on('click', '#markAsReadBtn', function() {
                markAllRead();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editorElement = document.querySelector('#content');

            if (!editorElement || typeof ClassicEditor === 'undefined') {
                return;
            }

            ClassicEditor.create(editorElement)
                .catch(function (error) {
                    console.error(error);
                });
        });
    </script>
    @yield('scripts');


</body>

</html>
