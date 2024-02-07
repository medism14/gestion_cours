<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="icon" href="{{ asset('images/roundedLogo.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        section {
            min-height: calc(100vh - 150px);
            display: block;
        }

        header {
            min-height: 60px;
        }


    </style>

    <body class="bg-gray-200">
        <!-- Header -->
        <header class="bg-gray-700 flex justify-between items-center rounded border-1 w-[90%] mx-auto mt-1 mb-[50px] text-xs sm:text-base">
            <img class="rounded-full ml-5 mr-7" src="{{ asset('images/logo.png') }}" width="55" height="55">
            <h1 class="text-base md:text-lg font-bold text-gray-100 border-b-2 transition-all duration-500 hover:pb-3">Gestion de cours</h1>
            <nav class="mr-5">
                <ul class="list-style-none">
                    <li><a href="{{ route('auth') }}" class="px-4 py-2 sm:px-6 sm:py-3 bg-gray-100 text-black cursor-pointer rounded-lg transition-all duration-300 ease-in-out hover:bg-blue-100">Accueil</a></li>
                </ul>
            </nav>
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

        <!-- Main Section -->
        <section class="w-full">
                @yield('content') 
        </section>

        <!-- Footer -->
        <footer class="h-[40px] fixed flex items-center justify-center bottom-0 left-0 text-center bg-gray-700 text-white w-full">
            Tout droits reservés ©
        </footer>

        @yield('scripts')
        
        <script>

            const messages = Array.from(document.getElementsByClassName('message'));

            if (messages) {
                setTimeout(() => {
                    messages.forEach((message) => {
                        message.remove();
                    });
                }, 4000)
            }
            
        </script>
    </body>
    </html>
