<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-animated">
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light luxury-card mb-4">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <div class="bg-gradient-primary rounded-3 p-2 me-3">
                        <span class="text-white fs-4">📊</span>
                    </div>
                    <div>
                        <h4 class="mb-0 text-gradient fw-bold">Excel Master</h4>
                        <small class="text-muted">نظام إدارة البيانات</small>
                    </div>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" href="{{ route('login') }}">تسجيل الدخول</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-gradient ms-2" href="{{ route('register') }}">إنشاء حساب</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        <span class="text-white fw-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="fw-semibold">{{ Auth::user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><h6 class="dropdown-header">{{ Auth::user()->name }}</h6></li>
                                    <li><small class="dropdown-item-text text-muted">{{ Auth::user()->email }}</small></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
