<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kayan AI</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f2ef;
        }
        .linkedin-blue {
            color: #0a66c2;
        }
        .linkedin-blue-bg {
            background-color: #0a66c2;
        }
        .hover\:linkedin-blue-dark:hover {
            background-color: #004182;
        }
        .input-focus:focus {
            border-color: #0a66c2;
            box-shadow: 0 0 0 1px #0a66c2;
        }
        .logo-container {
            height: 48px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="h-2 linkedin-blue-bg"></div>
            <div class="p-8">
                <div class="text-center mb-6">
                    <!-- Your Logo -->
                    <div class="logo-container flex justify-center mb-4">
                        <img src="{{ asset('assets/images/kayan.png') }}" alt="Kayan AI Logo" class="h-full object-contain">
                    </div>
                    <h1 class="text-2xl font-semibold text-gray-800">Sign in</h1>
                    <p class="text-gray-500 mt-1">to continue to Kayan AI</p>
                </div>

                <!-- Google Sign-In Button -->
                <a href="{{ route('login.google') }}" class="w-full flex items-center justify-center gap-2 py-2 px-4 border border-gray-300 rounded-md mb-6 text-gray-700 font-medium hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                        <path fill="#4285F4" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#34A853" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#EA4335" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>

                <!-- Divider -->
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-2 bg-white text-sm text-gray-500">or</span>
                    </div>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <input name="email" value="{{ old('email') }}" type="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500"
                            placeholder="Email"
                            required
                            autocomplete="email"
                            autofocus>
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <input name="password" type="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500"
                            placeholder="Password"
                            required
                            autocomplete="current-password">
                        @error('password')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember" type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember-me" class="ml-2 block text-sm text-gray-600">Remember me</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-500 hover:text-blue-400">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" 
                            class="w-full linkedin-blue-bg hover:linkedin-blue-dark text-white font-medium py-2 px-4 rounded-md transition">
                        Sign in
                    </button>
                </form>

                <div class="mt-6 text-center text-sm">
                    <p class="text-gray-600">New to Kayan AI? 
                        <a href="{{ route('register') }}" class="linkedin-blue font-medium">Join now</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>