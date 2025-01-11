<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --text-prime: #449db4;
            --text-prime2: #2c6674;
            --text-white: white;
        }

        .row {
            margin-left: 0rem !important;
            margin-right: 0rem !important;
        }

        .img-fluid {
            max-width: 110% !important;
        }

        .blue {
            border: 2px solid var(--text-prime);
            border-radius: 2rem;
        }

        .blue2 {
            border: 1px solid var(--text-prime);
            border-radius: 2rem;
        }

        .smaller {
            font-size: .75rem;
        }

        .bg-prime {
            background-color: var(--text-prime);
        }

        .text-prime {
            color: var(--text-prime);
        }

        .text-prime2 {
            color: var(--text-prime2);
        }

        .text-hover {
            background-color: var(--text-white) !important;
            color: var(--text-prime) !important;
        }

        .btn-prime {
            background-color: var(--text-prime);
            color: white;
            padding: .3rem .6rem;
            display: inline-block;
            border-radius: .3rem;
            text-decoration: none;
            border: none;
            font-size: .8rem;
        }

        .btn-prime:hover {
            background-color: var(--text-prime2);
            color: white;
        }

        .btn-prime2 {
            background-color: gray;
            color: white;
            padding: .3rem .6rem;
            display: inline-block;
            border-radius: .3rem;
            text-decoration: none;
            border: none;
            font-size: .8rem
        }

        .btn-prime2:hover {
            background-color: var(--text-prime2);
            color: white;
        }

        .p {
            margin-bottom: 0rem;
        }

        .border-prime {
            border: 1px solid var(--text-prime);
        }

        .px-10 {
            padding-right: 6rem !important;
            padding-left: 6rem !important;
        }

        .sidebar .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-link2 {
            text-decoration: none;
            padding: 0.75rem 1rem;
            transition: var(--text-prime) 0.3s ease;
            color: var(--text-prime);
            padding: .5rem .6rem;
            margin: .3rem 0;
            display: inline-block;
            border-radius: .3rem;
            text-decoration: none;
            border: none;
            font-size: 1rem;
            text-transform: uppercase;
        }

        .nav-link2:hover {
            background-color: var(--text-prime2);
            color: white;
            /* Darker prime color */
        }

        .nav-link2:active {
            background-color: #003f7f;
            /* Even darker prime color */
        }

        .display-7 {
            font-size: 1.5rem;
        }

        img.fixed-height {
            height: 400px;
            /* Set the desired fixed height */
            object-fit: cover;
            /* Ensure the image scales nicely without distortion */
        }

        .background {
            background-image: url('images/Background_image4.png');
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }

        .login-cont {
            background-color: rgba(255, 255, 255, 0.2);
            /* Semi-transparent white background */
            backdrop-filter: blur(10px);
            /* Glass effect */
            border-radius: 10px;
            padding: 0 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Soft shadow */
            width: 100%;


        }

        .scrollable-dropdown {
            max-height: 200px;
            /* Adjust the height as needed */
            overflow-y: auto;
            overflow-x: hidden;
            width: 100%;
            /* Adjust width to match the button if needed */
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased {{ Route::is('login') ? 'background' : '' }}">
    <div class="min-h-screen">
        @if (!Request::is('/', 'login'))
            @include('layouts.navigation')
        @endif


        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="pt-0 mt-0 w-full" style="max-height: 80%">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <!-- Bootstrap Bundle with Popper (JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" defer></script>


</body>


</html>
