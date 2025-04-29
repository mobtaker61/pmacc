<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', isset($app_locale) ? $app_locale : app()->getLocale()) }}" dir="{{ (isset($app_locale) ? $app_locale : app()->getLocale()) === 'fa' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Persian Fonts -->
        @if((isset($app_locale) ? $app_locale : app()->getLocale()) === 'fa')
            <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/iransans-font@1.0.0/css/iransans.css" rel="stylesheet">
        @endif

        <!-- Bootstrap CSS -->
        @if((isset($app_locale) ? $app_locale : app()->getLocale()) === 'fa')
            <!-- RTL Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@3.3.4/dist/css/bootstrap-rtl.min.css" rel="stylesheet">
        @else
            <!-- LTR Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        @endif

        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

        <!-- Toastr CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

        <!-- Lightbox2 CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">

        <!-- Datepicker CSS -->
        @if((isset($app_locale) ? $app_locale : app()->getLocale()) === 'fa')
            <!-- Persian Datepicker CSS -->
            <link rel="stylesheet" href="https://unpkg.com/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
        @else
            <!-- Bootstrap Datepicker CSS -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
            @if((isset($app_locale) ? $app_locale : app()->getLocale()) === 'tr')
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
            @endif
        @endif

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Custom CSS -->
        <style>
            [dir="rtl"] {
                font-family: 'Vazirmatn', 'IRANSans', sans-serif !important;
                text-align: right !important;
            }
            [dir="ltr"] {
                font-family: 'Figtree', sans-serif !important;
                text-align: left !important;
            }
            /* Force font family for Persian text */
            .persian-text {
                font-family: 'Vazirmatn', 'IRANSans', sans-serif !important;
            }
            
            /* Highlight current language */
            .locale-indicator {
                display: inline-block;
                padding: 2px 5px;
                border-radius: 3px;
                font-weight: bold;
                background-color: #ffed90;
                color: #333;
                margin-left: 5px;
            }
            
            .text-highlight {
                background-color: #fff3cd;
                padding: 2px 5px;
                border-radius: 3px;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get TRY rate
                fetch('/settings/try-rate')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const tryRateElements = document.querySelectorAll('#tryRate');
                        tryRateElements.forEach(element => {
                            element.textContent = data.rate;
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching TRY rate:', error);
                        const tryRateElements = document.querySelectorAll('#tryRate');
                        tryRateElements.forEach(element => {
                            element.textContent = '1';
                        });
                    });
            });
        </script>
    </head>
    <body class="d-flex flex-column min-vh-100 {{ (isset($app_locale) ? $app_locale : app()->getLocale()) === 'fa' ? 'persian-text' : '' }}">
        @include('layouts.navigation')

        <div class="flex-grow-1">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-light border-bottom py-3">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                {{ $header }}
                            </div>
                        </div>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="py-4">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-light border-top py-3 mt-auto">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        <p class="mb-0 small">
                            Current Locale: <span class="locale-indicator">{{ (isset($app_locale) ? $app_locale : app()->getLocale()) }}</span>
                            <span class="text-highlight">(Direction: {{ (isset($app_locale) ? $app_locale : app()->getLocale()) === 'fa' ? 'RTL' : 'LTR' }})</span>
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Toastr JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        
        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Lightbox2 JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <!-- Datepicker Scripts -->
        @if((isset($app_locale) ? $app_locale : app()->getLocale()) === 'fa')
            <!-- Persian Datepicker Scripts -->
            <script src="https://unpkg.com/persian-date@1.1.0/dist/persian-date.min.js"></script>
            <script src="https://unpkg.com/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
        @else
            <!-- Bootstrap Datepicker Scripts -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
            @if((isset($app_locale) ? $app_locale : app()->getLocale()) === 'tr')
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.tr.min.js"></script>
            @endif
        @endif

        @vite('resources/js/app.js')

        <!-- Custom JavaScript -->
        <script>
            // Configure Toastr
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        </script>

        @if(session('success'))
            <script>
                toastr.success("{{ session('success') }}");
            </script>
        @endif

        @if(session('error'))
            <script>
                toastr.error("{{ session('error') }}");
            </script>
        @endif

        <script>
            // Wait for both jQuery and Bootstrap to be loaded
            $(document).ready(function() {
                // Initialize all tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Initialize all popovers
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });

                // Initialize all dropdowns
                var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl, {
                        autoClose: true
                    });
                });

                // Initialize datepickers based on locale
                const currentLocale = "{{ isset($app_locale) ? $app_locale : app()->getLocale() }}";
                
                if (currentLocale === 'fa') {
                    // Initialize Persian Datepickers
                    $('.persian-date').each(function() {
                        $(this).pDatepicker({
                            format: 'YYYY/MM/DD',
                            autoClose: true,
                            initialValue: false,
                            calendar: {
                                persian: {
                                    locale: 'fa'
                                }
                            },
                            onlyTimePicker: false,
                            timePicker: {
                                enabled: false
                            },
                            onSelect: function(unixDate) {
                                // Convert to Persian date format
                                let pDate = new persianDate(unixDate);
                                $(this.elem).val(pDate.format('YYYY/MM/DD'));
                            }
                        });
                    });
                } else {
                    // Initialize Bootstrap Datepickers for other locales
                    $('.persian-date').each(function() {
                        $(this).datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose: true,
                            language: currentLocale === 'tr' ? 'tr' : 'en',
                            todayHighlight: true
                        });
                    });
                }

                // Enable dropdowns on hover for desktop
                if (window.innerWidth > 768) {
                    $('.dropdown').hover(
                        function() {
                            $(this).find('.dropdown-menu').addClass('show');
                        },
                        function() {
                            $(this).find('.dropdown-menu').removeClass('show');
                        }
                    );
                }

                function formatNumber(number, decimals = 0) {
                    return new Intl.NumberFormat('fa-IR', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals
                    }).format(number);
                }
            });
        </script>
        
        @stack('scripts')
    </body>
</html>
