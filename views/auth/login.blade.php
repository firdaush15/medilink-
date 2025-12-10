<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MediLink') }} - Login</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Vite CSS -->
    @vite(['resources/css/login.css'])
</head>
<body>
    <div class="overlay"></div>
    <div class="login-container">
        <!-- Logo -->
        <img src="{{ asset('assets/logo.png') }}" alt="MediLink Logo">
        <h2>HEALTH CARE</h2>

        <!-- Laravel login form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                @error('password')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn">SUBMIT</button>

            <!-- Forgot password & signup -->
            <div class="signup-text">
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Forgot your password?</a><br>
                @endif
                Don’t have an account? <a href="{{ route('register') }}">Sign up now!</a>
            </div>
        </form>
    </div>

    <!-- Optional JS validation (you can keep or remove) -->
    <script>
        const form = document.getElementById('loginForm');
        if(form) {
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                if(!email || !password) {
                    e.preventDefault();
                    alert('Please fill out all fields!');
                }
            });
        }
    </script>
</body>
</html>