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
           
            <div class="p-8">
                <!-- Welcome Message -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Admin Login!</h2>
                </div>
                
                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
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
                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 hover:shadow-md" style="background: linear-gradient(90deg, #6c63ff, #4d44db); "
                    >
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>