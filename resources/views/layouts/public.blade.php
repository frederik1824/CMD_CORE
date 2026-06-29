<!DOCTYPE html>
<html lang="es" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ARS Salud Integral Demo | Protegemos tu salud, simplificamos tu experiencia')</title>
    <meta name="description" content="Portal oficial de la Administradora de Riesgos de Salud ARS Salud Integral. Afiliaciones, autorizaciones médicas, reembolsos, red de prestadores y prevención.">
    
    <!-- Google Fonts: Rubik & Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Rubik:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Lector Native Stylesheets -->
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/bootstrap-grid.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/flaticon/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/style.css') }}">

    <!-- Tailwind CSS (for extra helper utility styling) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        lector: {
                            title: '#403663',
                            desc: 'rgba(64, 54, 99, 0.8)',
                            coral: '#49bcf7', // Replaced with Lector Blue
                            sky: '#49bcf7',
                            border: '#ecf0f3',
                            ash: '#f9fafb',
                            white: '#fefefe',
                            green: '#0be881',
                            red: '#f53b57',
                            yellow: '#dec32b'
                        }
                    },
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                        rubik: ['Rubik', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        .scroll-smooth {
            scroll-behavior: smooth;
        }
        
        /* Force Lector Menu Text Colors to prevent disappearing */
        .header-section .header-bottom .main-menu li a {
            color: #000000 !important;
            font-weight: 600;
        }
        .header-section .header-bottom .main-menu li a:hover,
        .header-section .header-bottom .main-menu li a.active,
        .header-section .header-bottom .main-menu li a:focus {
            color: #49bcf7 !important;
        }
        
        /* Force Mobile Menu item colors */
        .mobile-menu .menu .mobile-menu-area-inner ul.m-menu li a {
            color: #000000 !important;
            font-weight: 600;
        }
        .mobile-menu .menu .mobile-menu-area-inner ul.m-menu li a:hover,
        .mobile-menu .menu .mobile-menu-area-inner ul.m-menu li a.active {
            color: #49bcf7 !important;
        }
        
        /* Replace theme accent hover color to Blue (#49bcf7) */
        .btn {
            background: #49bcf7 !important;
        }
        .btn:hover {
            box-shadow: 0px 5px 15px 0px rgba(73, 188, 247, 0.4) !important;
        }
        .reg-head {
            color: #49bcf7 !important;
            border-color: #49bcf7 !important;
        }
        .reg-head:hover {
            background: #49bcf7 !important;
            color: #fff !important;
        }
        
        /* Pulse Animation updated to Blue */
        @keyframes pulse3 {
            0% {
                box-shadow: 0 0 0 0 rgba(73, 188, 247, 0.5);
            }
            70% {
                box-shadow: 0 0 0 20px rgba(73, 188, 247, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(73, 188, 247, 0);
            }
        }
        .pulse3 {
            animation: pulse3 2s infinite;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex flex-col min-h-screen bg-[#f9fafb] antialiased" x-data="{ mobileMenuOpen: false }">



    <!-- INCLUDED HEADER -->
    @include('public.partials.header')

    <!-- CONTENT WRAPPER -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- INCLUDED FOOTER -->
    @include('public.partials.footer')

    <!-- Lector JS Files -->
    <script src="{{ asset('themes/lector/assets/js/jquery.js') }}"></script>
    <script src="{{ asset('themes/lector/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('themes/lector/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('themes/lector/assets/js/swiper.min.js') }}"></script>
    <script src="{{ asset('themes/lector/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('themes/lector/assets/js/functions.js') }}"></script>
    <script>
        $(document).ready(function() {
            if (typeof WOW !== 'undefined') {
                new WOW().init();
            }
        });
    </script>

</body>
</html>
