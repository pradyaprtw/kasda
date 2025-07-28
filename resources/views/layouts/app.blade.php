<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @livewireStyles
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistem Kas Daerah</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .form-control,
        .form-select {
            border-color: black;
        }

        label {
            font-weight: bold;
        }
.create-wrapper {
    max-width: 60%;      /* atau misalnya 800px */
    margin: 0 auto;      /* agar tetap center */
}
    </style>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                {{-- <img class="navbar-brand me-1" style="height: 40px; width: 40px" src="https://play-lh.googleusercontent.com/07QFXQhz0omkCq0O7Hy4h2y1kPII_ndxZDTsjNAxSUXJ-VxeOLUp_aiBlTRL7iYJIQ" alt="logo kota metro"> --}}
                <a class="navbar-brand">
                    Sistem Kas Daerah
                </a>
                <ul class="navbar-nav ms-auto">

                    <!-- Authentication Links -->
                    @auth
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded {{ request()->is('home') ? 'text-primary fw-bold' : 'text-dark' }}" href="{{ route('home') }}">
                               <i class="bi bi-house-door me-1"></i>{{ __('Dashboard') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded {{ request()->is('sp2d') ? 'text-primary fw-bold' : 'text-dark' }}" href="{{ route('sp2d') }}">
                                <i class="bi bi-cash-coin me-1"></i>{{ __('SP2D') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded {{ request()->is('penerima') ? 'text-primary fw-bold' : 'text-dark' }}" href="{{ route('penerima') }}">
                                <i class="bi bi-person-fill me-1"></i>{{ __('Nama CV/Penerima') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded {{ request()->is('instansi') ? 'text-primary fw-bold' : 'text-dark' }}" href="{{ route('instansi') }}">
                                <i class="bi bi-building me-1"></i>{{ __('Instansi') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded {{ request()->routeIs('export.index') ? 'text-primary fw-bold' : 'text-dark' }}" href="{{ route('export.index') }}">
                                <i class="bi bi-download me-1"></i>{{ __('Export') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="btn btn-danger ms-3" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-1"></i>Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
    </div>
    </nav>
    <main class="py-4">
        @yield('content')
    </main>
    </div>
    @livewireScripts
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-create-modal', (event) => {
                var modal = document.getElementById('createModal');
                var modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        });
    </script>
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')
</body>

</html>
