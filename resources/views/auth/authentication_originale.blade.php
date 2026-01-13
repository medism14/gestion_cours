<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Espace Numérique</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/ismdIcon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.5s ease-out forwards',
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            /* Fond abstrait moderne */
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-color: #111827; /* Fallback dark */
            min-height: 100vh;
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Custom Input Autofill fix */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body class="flex flex-col h-screen overflow-hidden">

    <!-- Notification Container (Toast) -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2">
        @if(session('error'))
            @if(is_array(session('error')))
                @foreach (session('error') as $errors)
                    @foreach ($errors as $error)
                        <div class="animate-fade-in bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg flex items-center pr-10 relative">
                            <span class="mr-2"><i class="fas fa-exclamation-circle"></i></span>
                            <p class="text-sm font-medium">{{ $error }}</p>
                            <button onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-700 hover:text-red-900"><i class="fas fa-times"></i></button>
                        </div>
                    @endforeach
                @endforeach
            @else
                <div class="animate-fade-in bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg flex items-center pr-10 relative">
                    <span class="mr-2"><i class="fas fa-exclamation-circle"></i></span>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                    <button onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-700 hover:text-red-900"><i class="fas fa-times"></i></button>
                </div>
            @endif
        @endif

        @if(session('success'))
            <div class="animate-fade-in bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg flex items-center pr-10 relative">
                <span class="mr-2"><i class="fas fa-check-circle"></i></span>
                <p class="text-sm font-medium">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-green-700 hover:text-green-900"><i class="fas fa-times"></i></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        
        <div class="w-full max-w-md animate-fade-in-up">
            
            <!-- Card -->
            <div class="glass-panel shadow-2xl rounded-2xl overflow-hidden">
                
                <!-- Header Section -->
                <div class="px-8 py-10 pb-6 text-center">
                    <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center shadow-md mb-6 ring-4 ring-brand-50">
                        <img src="{{ asset('images/ismd.jpg') }}" class="h-14 w-14 object-contain" alt="ISMD Logo">
                    </div>
                    
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                        Bienvenue
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Connectez-vous à votre Espace Numérique
                    </p>
                </div>

                <!-- Form Section -->
                <div class="px-8 py-8 pt-2">
                    <form action="{{ route('login') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Adresse Email</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required 
                                    value="{{ isset($_COOKIE['email']) ? $_COOKIE['email'] : '' }}"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition duration-200"
                                    placeholder="exemple@campus.com">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                                </div>
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                    value="{{ isset($_COOKIE['password']) ? $_COOKIE['password'] : '' }}"
                                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition duration-200"
                                    placeholder="••••••••">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors cursor-pointer">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox" 
                                    @if(isset($_COOKIE['password']) && $_COOKIE['password'] !== "") checked @endif
                                    class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded cursor-pointer">
                                <label for="remember" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">
                                    Se souvenir de moi
                                </label>
                            </div>

                            <a href="#" class="text-sm font-medium text-brand-600 hover:text-brand-500 transition-colors">
                                Mot de passe oublié ?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <i class="fas fa-sign-in-alt text-brand-500 group-hover:text-brand-400 transition-colors"></i>
                                </span>
                                Se connecter
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Footer within Card -->
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} Institut Supérieur de Management et du Développement.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon class
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    icon.classList.add('text-brand-600');
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    icon.classList.remove('text-brand-600');
                }
            });

            // Auto-remove toasts after 5 seconds
            const toasts = document.querySelectorAll('#toast-container > div');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => toast.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
