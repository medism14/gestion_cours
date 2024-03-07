<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Authentification</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="icon" href="{{ asset('images/papaRounded.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <script>
            // Redéfinir toutes les fonctions de la console pour qu'elles ne fassent rien
            console.log = function() {};
            console.warn = function() {};
            console.error = function() {};
            console.info = function() {};
        </script>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <style>
        
        body {
            font-family: 'figtree', sans-serif;
            background: gray;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        header {
            min-height: 60px;
        }


    </style>

    <body class="bg-gray-200">
        <!-- Header -->
        <header class="bg-gray-700 flex items-center rounded border-1 w-[90%] mx-auto mt-1 mb-[20px] text-xs sm:text-base">
            <div class="flex-1 flex justify-start"><img class="ml-5 rounded-full" src="{{ asset('images/papa.png') }}" width="55" height="55"></div>
            <div id="petitEcran" class="flex-1"><h1 class="w-[100px] text-[0.7rem] md:text-lg font-bold text-gray-100 mx-auto border-gray-100">Espace Numérique d'Enseignement</h1></div>
            <div id="grandEcran" class="flex-1 flex justify-center hidden"><h1 class="text-xs md:text-sm lg:text-lg font-bold text-gray-100 border-gray-100 border-b-2 transition-all duration-500 hover:pb-3">Espace Numérique d'Enseignement</h1></div>
            <div class="flex-1 flex justify-end">
            <nav class="mr-5">
                <ul class="list-style-none">
                    <li><a href="{{ route('auth') }}" class="px-4 py-2 sm:px-6 sm:py-3 bg-gray-100 text-black cursor-pointer rounded-lg transition-all duration-300 ease-in-out hover:bg-blue-100">Accueil</a></li>
                </ul>
            </nav>
            </div>
        </header>

        @if(session('error'))
            <script>
                var errors = '';
            </script>
            @if (is_array(session('error')))
                @foreach (session('error') as $i => $v)
                     @foreach ($v as $sub_v)
                    <script>
                        errors += '{{ $sub_v }}\n';
                    </script>

                    @endforeach
                @endforeach
                <script>
                    alert(errors);
                </script>
            @else
                <script>
                    alert("{{ session('error') }}");
                </script>
            @endif
        @endif

        @if (session('success'))
            <div class="message block text-center text-green-500 italic mb-2">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-center">
            <img id="userIcon" src="{{ asset('images/papaIcon.png') }}" width="100" height="100" alt="Image">
        </div>

        <!-- Main Section -->
            <h1 class="text-center text-2xl bg-gray-700 rounded-lg rounded-b-none w-[70%] lg:w-[50%] mx-auto mt-[20px] text-white p-5">Authentification</h1>
            <section class="p-5 w-[70%] lg:w-[50%] bg-[#BACFF0] mx-auto rounded-t-none rounded-lg flex flex-col mb-6">
                <form action="{{ route('login') }}" method="POST" class="m-0 p-0">
                    @csrf
                    <div>
                        <!-- Row -->
                        <div class="flex w-full items-center mt-3">
                            <div class="flex-1 flex flex-col space-y-1 items-center justify-center">
                                <label for="email" class="font-bold">Email:</label>
                                <input id="email" name="email" type="text" value="{{ isset($_COOKIE["email"]) && $_COOKIE["email"] !== "" ? $_COOKIE["email"] : '' }}" class="outline-none focus:ring focus:border-blue-100 rounded w-full md:w-[60%] shadow-md p-1">
                            </div>
                        </div>  

                        <!-- Row -->
                        <div class="flex w-full items-center mt-5">
                            <div class="flex-1 flex flex-col space-y-1 items-center justify-center">
                                <label class="block text-sm font-bold" for="password">Password: </label>
                                <div class="flex w-full justify-center mt-1">
                                    <input id="password" value="{{ isset($_COOKIE["password"]) && $_COOKIE["password"] !== "" ? $_COOKIE["password"] : '' }}" class="outline-none focus:ring focus:border-blue-100 rounded w-full md:w-[60%] shadow-md p-1" name="password" id="password" type="password">
                                    <button type="button" id="eye" class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                        </div> 

                        <!-- Column -->
                        <div class="mt-5 flex-1 flex flex-col space-x-1 items-center justify-start">
                            <label for="remember">Se souvenir de moi</label>
                            <input type="checkbox" id="remember" name="remember" class="md:scale-125" @if(isset($_COOKIE["password"]) && $_COOKIE["password"] !== "" ) checked @endif>
                        </div>
                    </div>
                    <!-- Row -->
                    <div class="flex w-full items-center mt-10">
                        <!-- Column -->
                        <div class="flex-1 flex flex-col space-y-1 items-center justify-center">
                            <button class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-all duration-300 hover:bg-blue-600">Se connecter</button>
                        </div>
                    </div>
                </form>
            </section>

        <!-- Footer -->
        <footer class="h-[40px] fixed hidden flex items-center justify-center bottom-0 left-0 text-center bg-[#CF7315] text-white w-full">
            Tout droits reservés ©
        </footer>

        @yield('scripts')
        
        <script>

            const messages = Array.from(document.getElementsByClassName('message'));
            const mediaQuery = window.matchMedia('(max-width: 768px)');

            if (messages) {
                setTimeout(() => {
                    messages.forEach((message) => {
                        message.remove();
                    });
                }, 4000)
            }
            
            const password = document.getElementById('password');
            const email = document.getElementById('email');
            const eye = document.getElementById('eye');
            const userIcon = document.getElementById('userIcon');

            eye.addEventListener('click', () => {
                if (password.type === 'password') {
                    password.type = 'text'; 
                } else {
                    password.type = 'password';
                }
            });

            //Modification de la taille de l'input password
            function modifTaille () {
                let width = eye.getBoundingClientRect().width;

                let newWidth = email.offsetWidth  - width;

                password.style.width = `${newWidth}px`;
            }

            window.addEventListener('resize', () => {
                modifTaille()
            });

            modifTaille();

            const petitEcran = document.getElementById('petitEcran');
            const grandEcran = document.getElementById('grandEcran');

            window.addEventListener('resize', () => {
                EntName();
            });

            function EntName () {
                if (mediaQuery.matches) {
                    petitEcran.classList.remove('hidden');
                    grandEcran.classList.add('hidden');

                    userIcon.setAttribute('width', '80');
                    userIcon.setAttribute('height', '80');
                } else {
                    petitEcran.classList.add('hidden');
                    grandEcran.classList.remove('hidden');

                    userIcon.setAttribute('width', '120');
                    userIcon.setAttribute('height', '120');
                }
            }
            EntName();
        </script>
    </body>
    </html>
