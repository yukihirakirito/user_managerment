<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="User Management System - Professional Dashboard">
    <title>@yield('title', 'User Management System')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    {{-- Font: Inter (Modern & Clean) --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    <div id="app" class="app-wrapper">
        {{-- Navbar --}}
        @include('layouts.components.navbar')

        {{-- Main Container --}}
        <div class="app-container">
            {{-- Sidebar --}}
            @include('layouts.components.sidebar')

            {{-- Main Content --}}
            <main class="app-main">
                {{-- Breadcrumbs --}}
                @hasSection('breadcrumbs')
                    <nav class="breadcrumbs mb-4" aria-label="Breadcrumb">
                        <ol class="breadcrumb">
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                @endif

                {{-- Page Header --}}
                @hasSection('page-header')
                    <div class="page-header mb-4">
                        @yield('page-header')
                    </div>
                @endif

                {{-- Alerts --}}
                @include('layouts.components.alerts')

                {{-- Page Content --}}
                <div class="page-content">
                    @yield('content')
                </div>
            </main>
        </div>

        {{-- Footer --}}
        @include('layouts.components.footer')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Custom JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
    
    @stack('scripts')
</body>
</html>