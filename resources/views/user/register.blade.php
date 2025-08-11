<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Creative AI</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <!-- Animated Background Elements -->
    <div class="absolute top-20 left-20 w-32 h-32 rounded-full bg-blue-200 opacity-20 blur-xl"></div>
    <div class="absolute bottom-20 right-20 w-40 h-40 rounded-full bg-indigo-200 opacity-20 blur-xl"></div>
    
    <div class="relative max-w-md w-full mx-auto">
        <!-- Card with Glass Morphism Effect -->
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl overflow-hidden border border-white/30">
            <!-- Decorative Header -->
            <div class="bg-gradient-to-r  p-6 text-center relative" style="background: linear-gradient(90deg, #6c63ff, #4d44db); ">
               
                <h1 class="text-2xl font-bold text-white mt-4">Create Account</h1>
                <p class="text-blue-100 mt-1">Join our creative community</p>
            </div>

            <div class="p-8">
                <!-- Google Sign-Up Button -->
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

                <!-- Registration Form -->
                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name Field -->
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input name="name" value="{{ old('name') }}" type="text"
                                class="block w-full pl-10 pr-3 py-3 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/50"
                                placeholder="John Doe">
                        </div>
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input name="email" value="{{ old('email') }}" type="email"
                                class="block w-full pl-10 pr-3 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/50"
                                placeholder="your@email.com">
                        </div>
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input name="password" type="password"
                                class="block w-full pl-10 pr-3 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/50"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input name="password_confirmation" type="password"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white/50"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="flex items-center">
                        <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('terms') ? 'checked' : '' }}>
                        <label for="terms" class="ml-2 block text-sm text-gray-700">
                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms</a> and <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
                        </label>
                    </div>
                    @error('terms')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 transition-all duration-300 hover:shadow-md" style="background: linear-gradient(90deg, #6c63ff, #4d44db); ">
                        Create Account
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 hover:underline">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>