<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Evalia</title>
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
        .form-container {
            background-color: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(4px);
        }
        .logo-container {
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .username-preview {
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }
        .username-preview strong {
            color: #495057;
        }
        .input-focus:focus {
            border-color: #0a66c2;
            box-shadow: 0 0 0 2px rgba(10, 102, 194, 0.2);
        }
        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        .error-border {
            border-color: #dc2626;
        }
        .success-message {
            background-color: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 0.75rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="form-container rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header stripe -->
            <div class="h-2 linkedin-blue-bg"></div>
            
            <div class="p-8">
                <div class="text-center mb-6">
                    <!-- Your Custom Logo -->
                    <div class="logo-container">
                        <!-- Placeholder for logo -->
                        <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('assets/images/kayan.png') }}" alt="Evalia Logo" class="h-full object-contain">
                        </div>
                    </div>
                    <h1 class="text-2xl font-semibold text-gray-800 mt-3">Join Evalia</h1>
                    <p class="text-gray-500 mt-1">Voice Analysis Assistant</p>
                </div>
                
                <!-- Display success message if exists -->
                @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
                @endif
                
                <!-- Display general error if exists -->
                @if(session('error'))
                <div class="error-message bg-red-50 p-3 rounded mb-4">
                    {{ session('error') }}
                </div>
                @endif
                
                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registrationForm">
                    @csrf
                    
                    <!-- Email field - triggers username generation -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            type="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500 @error('email') error-border @enderror"
                            placeholder="Enter your email address"
                            required
                            autocomplete="email"
                            autofocus>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Username preview (read-only) -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <div class="username-preview">
                            Your username will be: <strong id="usernamePreview">Enter email above</strong>
                        </div>
                        <input 
                            id="username" 
                            name="username" 
                            type="hidden"
                            value="{{ old('username') }}">
                        @error('username')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input 
                            id="full_name" 
                            name="full_name" 
                            value="{{ old('full_name') }}" 
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500 @error('full_name') error-border @enderror"
                            placeholder="Enter your full name"
                            required
                            autocomplete="name">
                        @error('full_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    {{--  <!-- Position (Optional) -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position (Optional)</label>
                        <input 
                            id="position" 
                            name="position" 
                            value="{{ old('position') }}" 
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500 @error('position') error-border @enderror"
                            placeholder="Your position or title"
                            autocomplete="organization-title">
                        @error('position')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone (Optional) -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone (Optional)</label>
                        <input 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone') }}" 
                            type="tel"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500 @error('phone') error-border @enderror"
                            placeholder="Your phone number"
                            autocomplete="tel">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>  --}}

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500 @error('password') error-border @enderror"
                            placeholder="Create a password"
                            required
                            autocomplete="new-password">
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md input-focus placeholder-gray-500 @error('password_confirmation') error-border @enderror"
                            placeholder="Confirm your password"
                            required
                            autocomplete="new-password">
                        @error('password_confirmation')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" 
                            class="w-full linkedin-blue-bg hover:linkedin-blue-dark text-white font-medium py-2 px-4 rounded-md transition">
                        Create Account
                    </button>
                </form>

                <div class="mt-6 text-center text-sm">
                    <p class="text-gray-600">Already have an account? 
                        <a href="{{ route('login') }}" class="linkedin-blue font-medium">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const usernamePreview = document.getElementById('usernamePreview');
            const usernameInput = document.getElementById('username');
            
            // Function to generate username from email
            function generateUsername(email) {
                if (!email) return '';
                
                // Extract the part before @ and clean it up
                let username = email.split('@')[0];
                
                // Remove special characters and replace with underscores
                username = username.replace(/[^a-zA-Z0-9]/g, '_');
                
                // Remove multiple consecutive underscores
                username = username.replace(/_+/g, '_');
                
                // Remove leading/trailing underscores
                username = username.replace(/^_+|_+$/g, '');
                
                // Ensure username is at least 3 characters
                if (username.length < 3) {
                    username = username + '_user';
                }
                
                return username.toLowerCase();
            }
            
            // Update username preview when email changes
            emailInput.addEventListener('input', function() {
                const email = this.value.trim();
                const username = generateUsername(email);
                
                if (username) {
                    usernamePreview.textContent = username;
                    usernameInput.value = username;
                } else {
                    usernamePreview.textContent = 'Enter email above';
                    usernameInput.value = '';
                }
            });
            
            // Also update on form submission to ensure latest value
            document.getElementById('registrationForm').addEventListener('submit', function() {
                const email = emailInput.value.trim();
                const username = generateUsername(email);
                
                if (username) {
                    usernameInput.value = username;
                }
            });
            
            // Initialize on page load if there's already an email value
            if (emailInput.value) {
                const username = generateUsername(emailInput.value.trim());
                if (username) {
                    usernamePreview.textContent = username;
                    usernameInput.value = username;
                }
            }
            
            // Clear error styling when user starts typing in a field with error
            const errorFields = document.querySelectorAll('.error-border');
            errorFields.forEach(field => {
                field.addEventListener('input', function() {
                    this.classList.remove('error-border');
                    const errorElement = this.nextElementSibling;
                    if (errorElement && errorElement.classList.contains('error-message')) {
                        errorElement.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>