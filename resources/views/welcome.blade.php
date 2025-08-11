<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Creative AI</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <!-- Animated Background Elements -->
    <div class="absolute top-20 left-20 w-32 h-32 rounded-full bg-blue-200 opacity-20 blur-xl"></div>
    <div class="absolute bottom-20 right-20 w-40 h-40 rounded-full bg-indigo-200 opacity-20 blur-xl"></div>
    
    <div class="relative max-w-md w-full mx-auto">
        <!-- Card with Glass Morphism Effect -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl overflow-hidden border border-white/30">
            <!-- Decorative Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-center relative">
                <h1 class="text-2xl font-bold text-white mt-4">Creative AI</h1>
                <p class="text-blue-100 mt-1">Your intelligent voice assistant</p>
            </div>

            <div class="p-8">
                <!-- Welcome Message -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Welcome Back!</h2>
                    <p class="text-gray-600 mt-2">Sign in to continue your creative journey</p>
                </div>

                <!-- Google Sign-In Button -->
                <a href="{{ route('login.google') }}" class="w-full flex items-center justify-center gap-3 py-3 px-4 border border-gray-300 rounded-xl bg-white text-gray-700 font-medium hover:bg-gray-50 transition-all shadow-sm hover:shadow-md mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#4285F4" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#34A853" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#EA4335" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        <path fill="none" d="M0 0h48v48H0z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300/50"></div> <!-- Softer border -->
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-3   text-gray-500 text-xs font-medium uppercase tracking-wider"> <!-- Improved text -->
                        or register with email
                        </span>
                    </div>
                </div>

                <!-- Email/Password Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-1">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/50"
                                placeholder="your@email.com"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                            >
                        </div>
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/50"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember-me"
                                name="remember"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 hover:shadow-md"
                    >
                        Sign In
                    </button>
                </form>

                <!-- Sign Up Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 hover:underline ml-1">
                            Create one now
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>