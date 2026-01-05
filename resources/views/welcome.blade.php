<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to My App</title>
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>
<body class="app-body">
    <div class="app-container">
        <!-- Header -->
        <header class="app-header">
            <div class="header-content">
                <div class="app-logo">
                    My App

        </header>

        <!-- Main Content -->
        <main class="app-main">
            <h1 class="app-title">
                Welcome to My App
            </h1>
            <p class="app-description">
                A modern, powerful, and easy-to-use application built with Laravel.
            </p>

            <!-- Call to Action Buttons -->
            <div class="app-buttons">
                @auth
                    <a href="{{ url('/home') }}" class="btn-primary">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-secondary">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </main>

        <!-- Footer -->
        <footer class="app-footer">
            <p>&copy; {{ date('Y') }} My App. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
