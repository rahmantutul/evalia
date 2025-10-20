<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Evalia </title>
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
            <!-- Header stripe -->
            <div class="h-2 linkedin-blue-bg"></div>
            
            <div class="p-8">
                <div class="text-center mb-6">
                    <!-- Your Logo -->
                    <div class="logo-container flex justify-center mb-4">
                        <img src="{{ asset('assets/images/kayan.png') }}" alt="Evalia  Logo" class="h-full object-contain">
                    </div>
                    <h1 class="text-2xl font-semibold text-gray-800">Log In</h1>
                    <p class="text-gray-500 mt-1">to continue to Evalia </p>
                </div>

                <!-- Show general errors -->
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                        <p class="text-sm text-red-600">{{ $errors->first() }}</p>
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                        <p class="text-sm text-green-600">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Divider -->
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <input name="username" value="{{ old('username') }}" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500 @error('username') border-red-500 @enderror"
                            placeholder="Username"
                            required
                            autocomplete="username"
                            autofocus>
                        @error('username')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <input name="password" type="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500 @error('password') border-red-500 @enderror"
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
            </div>
        </div>
    </div>
</body>
</html>