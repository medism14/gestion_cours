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
         <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
         <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <style>
        
        body {
            font-family: 'figtree', sans-serif;
            background: rgb(230, 213, 202);
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

        .sidebar-content::before {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 0;
            border-bottom: 2px solid white;
            transition: 0.3s ease-in-out;
            width: 0;
            transform: translateX(-50%);
        }
        .sidebar-content:hover::before {
            width: 120%;
        }

        .notifs {
            min-height: 3rem;
            min-width: 8rem;
        }

        @media screen and (max-width: 768px) {
            .notifs {
            min-width: 6rem;
        }
        }


    </style>

    <body class="bg-gray-200">
        <!-- Header -->
        <header class="flex">
            <!-- SideBar -->
            <section id="leftSection" class="block md:w-[25%]">
                <nav id="sidebar" class="fixed w-none md:w-[25%] bg-gray-800 h-screen">
                    <div class="absolute m-1 right-0 flex items-center my-auto h-screen text-4xl">
                        <button id="btn-unDisplay" class="text-white hover:text-gray-300" type="button"><i class="fas fa-angle-left"></i></button>
                    </div>
                    <!-- SideBar Title -->
                    <div class="block p-5 rounded-lg text-white text-xl text-center ">
                        <a href="{{ route('dashboard') }}" class="border-b-2">Gestion de cours</a>
                    </div>

                    <hr class="mb-12 rounded border-4 border-gray-400"></hr>
                    <!-- SideBar Content -->
                    @if (auth()->user()->role == 0)
                        <div class=" text-white p-6 text-center text-xl border-b-2 border-slate-500 w-[80%] mx-auto rounded-lg">
                            <a href="{{ route('users.index') }}" class="sidebar-content relative pb-2"><i class="fa fa-users"></i>Utilisateurs</a>
                        </div>
                        <div class=" text-white p-6 text-center text-xl border-b-2 border-slate-500 w-[80%] mx-auto rounded-lg">
                            <a href="{{ route('sectors.index') }}" class="sidebar-content relative pb-2"><i class="fa fa-stream"></i> Filières</a>
                        </div>
                        <div class=" text-white p-6 text-center text-xl border-b-2 border-slate-500 w-[80%] mx-auto rounded-lg">
                            <a href="{{ route('modules.index') }}" class="sidebar-content relative pb-2"> <i class="fas fa-book"></i>Modules</a>
                        </div>
                    @endif
                    <div class=" text-white p-6 text-center text-xl border-b-2 border-slate-500 w-[80%] mx-auto rounded-lg">
                        <a href="{{ route('resources.index') }}" class="sidebar-content relative pb-2"> <i class="fa fa-cube"></i> Ressources</a>
                    </div>
                    <div class=" text-white p-6 text-center text-xl border-b-2 border-slate-500 w-[80%] mx-auto rounded-lg">
                        <a href="{{ route('forums.index') }}" class="sidebar-content relative pb-2"><i class="fa fa-bullhorn"></i> Forums</a>
                    </div>

                    <!-- Footer -->
                    <footer class="fixed w-full md:w-[25%] text-center text-white bottom-0 bg-gray-600">
                        <p>Tout droits reservés ©</p>
                    </footer>
                </nav>
                
            </section>
            <!-- Côté Droit -->
            <section id="rightSection" class="flex-1 md:w-[75%]">
                <!-- Côté droit navbar -->
                <nav id="navbar" class="fixed w-full bg-gray-800 text-white flex p-4 md:w-[75%]">
                    <div id="btn-display" class="hidden absolute fixed text-4xl top-0 left-0 mt-4">
                        <button class="text-black hover:text-gray-700" type="button"><i class="fas fa-angle-right"></i></button>
                    </div>
                    <div id="divNavbar" class="flex-1 flex items-center">
                        <div class="ml-12 md:ml-24 flex-1 text-center text-base md:text-xl font-bold">
                        </div> 

                        <div class="flex items-center justify-end">
                            @if (auth()->user()->role == 2)
                                <button id="notif" class="mr-2 md:mr-6 border-2 p-2 rounded-lg transition-all duration-300 ease-in-out bg-white text-black hover:bg-gray-400 relative ">
                                    <span id="nombreNotif" class="text-gray-900 font-bold text-[0.5rem] text-blue-500 font-bold absolute top-0 right-0">{{ auth()->user()->notifs }}</span>
                                    <i class="fa fa-bell "></i>
                                </button>
                            @endif
                            <a href="{{ route('parameters') }}" id="user" class="mr-2 md:mr-6 border-2 p-2 rounded-lg transition-all duration-300 ease-in-out bg-white text-black hover:bg-gray-400 relative " title="Paramètres">
                                <i class="fas fa-user"></i>
                            </a>
                            <a id="logout" class="mr-2 md:mr-6 border-2 p-2 rounded-lg transition-all duration-300 ease-in-out bg-white text-black hover:bg-gray-400 relative " title="Déconnexion" href="{{ route('logout') }}">
                                <i class="fas fa-door-open"></i>
                            </a> 
                            
                        </div>
                    </div>
                    
                    <div id="notifList" class="hidden absolute z-50 flex flex-col overflow-y-auto text-xs md:text-sm p-3 bg-slate-800 border-2 border-gray-400 rounded-lg" style="max-height: calc(3rem * 3);">
                        <div class="flex w-full items-center">
                            <h1 class="w-3/4 text-center font-bold mb-3 text-base md:text-lg p-2 border-r-2">Notifications Resources</h1>
                            <form action="{{ route('users.suppNotifs') }}"  method="POST" class="m-0 p-0 w-1/4 text-center" onsubmit="return confirm('Voulez-vous vraiment supprimer vos notifications ?')">
                                @csrf
                                <button id="allDeleteNotif" class="text-red-500 font-bold mb-2 text-xs md:text-xs">Tout supprimer</button>
                            </form>
                        </div>
                        <hr class="my-2">
                    </div>

                    <div id="userList" class="hidden absolute flex flex-col text-xs md:text-sm p-3 bg-slate-800 border-2 border-gray-400 rounded-lg">
                        <div class="flex w-full flex-col">
                            <h1 class="w-full text-center font-bold mb-3 text-base md:text-lg p-2">Informations Utilisateur</h1>
                            <div class="flex flex-col">
                                <span class="text-center">Prenom: {{ auth()->user()->first_name }}</span>
                                <hr class="border-gray-500">
                                <span class="text-center">Nom: {{ auth()->user()->last_name }}</span>
                                <hr class="border-gray-500">
                                <span class="text-center">Role: {{ auth()->user()->role == 0 ? 'Administrateur' : (auth()->user()->role == 1 ? 'Professeur' : 'Etudiant') }}</span>
                                <hr class="border-gray-500">
                                <span class="text-center">Email: {{ auth()->user()->email }}</span>
                                <hr class="border-gray-500">
                                <span class="text-center">Téléphone: {{ auth()->user()->phone }}</span>
                                <hr class="border-gray-500">
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Côté droit content -->
                <section class="mt-[80px] ml-[5px] mr-[5px] md:ml-[10px] md:mr-[10px]">
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

                    @yield('content') 
                </section>
            </section>
        </header>

    <script>
    // Create a MediaQueryList object for the media query "(max-width:768px)"
    var mediaQuery = window.matchMedia("(max-width:768px)");

    ///////////////////////////////////////////////////////
    //User manipulation
    ///////////////////////////////////////////////////////
    

    const user = document.getElementById('user');
    const userList = document.getElementById('userList');
    let inUserList = false;

    let tailleDiv2 = user.getBoundingClientRect().width / 2;
    let taille = user.getBoundingClientRect().width;
    let positionDroite = (window.innerWidth - user.getBoundingClientRect().right);

    let positionHaute = user.getBoundingClientRect().top + taille + 5;

    userList.style.right = `${positionDroite}px`;
    userList.style.top = `${positionHaute}px`;

    //L'activation de la barre d'utilisateur
        if (mediaQuery.matches) {
            //Pour les tels
                user.addEventListener('touchstart', () => {
                    userList.classList.remove('hidden');
                });

                user.addEventListener('touchend', () => {
                    setTimeout(() => {
                        if (!inUserList) {
                            userList.classList.add('hidden');
                        }
                    }, 100)
                });

                userList.addEventListener('touchstart', () => {
                    inUserList = true;
                });

                userList.addEventListener('touchend', () => {
                    inUserList = false;
                    userList.classList.add('hidden');
                });
            //
        } else {
            //Pour le pc
                user.addEventListener('mouseenter', () => {
                            userList.classList.remove('hidden');
                    });

                    user.addEventListener('mouseleave', () => {
                        setTimeout(() => {
                            if (!inUserList) {
                                userList.classList.add('hidden');
                            }
                        }, 100)
                    });

                    userList.addEventListener('mouseenter', () => {
                        inUserList = true;
                    });

                    userList.addEventListener('mouseleave', () => {
                        inUserList = false;
                        userList.classList.add('hidden');
                    });
            //
        }
    //
    @if (auth()->user()->role == 2)
    ///////////////////////////////////////////////////////
    //Notif MANIPULATION
    ///////////////////////////////////////////////////////

    document.addEventListener("DOMContentLoaded", function () {
        const notifButton = document.getElementById('notif');
        const notifList = document.getElementById('notifList');
        const allDeleteNotif = document.getElementById('allDeleteNotif');
        const nombreNotif = document.getElementById('nombreNotif');

        //Pour recuperer les notifications
        let id = {{ auth()->user()->id }};

        //Pour recuperer les notifications
        fetch(`/getUserNotifs/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {

                function formatDate(date) {
                    let createdAt = new Date(date)

                    let now = new Date();

                    let differenceMilli = now - createdAt;

                    let diffJour = Math.floor(differenceMilli / (1000*60*60*24));

                    let finalDate;

                    if (diffJour == 0) {
                        finalDate = 'aujourd\'hui';
                    } else if (diffJour == 1) {
                        finalDate = 'Hier';
                    } else if (diffJour == 2) {
                        finalDate = 'Avant-hier';
                    } else {
                        finalDate = `Il y'a ${diffJour} jours`;
                    }

                    return finalDate;
                }


                data.forEach((notif) => {
                    notifList.innerHTML += `
                        <div class="flex space-x-4 p-2 border-b-2">
                            <div class="flex notifs justify-center flex-col items-center">
                                <span class="">Module:</span>
                                <span class="underline">${notif.resource.module.name}</span>
                                <span class="">Date:</span>
                                <span class="underline">${formatDate(notif.created_at)}</span>
                            </div>
                            <div class="flex notifs justify-center items-center">
                                <form action="{{ route('resources.index') }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    <input name="searchNotif" id="searchNotif" type="text" class="hidden" value="${notif.resource.id}">
                                    <button type="submit" class="bg-yellow-600 px-2 py-1 rounded-lg shadow-md shadow-gray-600 transition-all duration-300 hover:bg-yellow-700">Y accéder</button>
                                </form>
                                </div>
                        </div>
                    `;
                })

                if (data.length == 0) {
                    allDeleteNotif.classList.add('hidden');
                    notifList.innerHTML += `
                        <span class="text-center">Aucune notification</span>
                    `;
                }

            })
            .catch(error => {
                console.error('Error: ' + error);
        });

        //Lorsqu'on clique sur le boutons de notif
        notifButton.addEventListener('click', () => {
            notifList.classList.toggle('hidden');
            nombreNotif.textContent = '0';
            fetch(`/resetNotifs/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then()
            .catch(error => {
                console.error('Error: ' + error);
            });
        })
        
        //Supprimer tout les fichiers
        allDeleteNotif.addEventListener('click', () => {
            console.log('loup');
        });
        
    });
    @endif
    ///////////////////////////////////////////////////////
    //DOM MANIPULATION
    ///////////////////////////////////////////////////////

    // Get the elements with the IDs 'btn-unDisplay', 'btn-display', 'navbar', and 'rightSection'
    const btnUnDisplay = document.getElementById('btn-unDisplay');
    const btnDisplay = document.getElementById('btn-display');
    const side = document.getElementById('sidebar');
    const rightSection = document.getElementById('rightSection');
    const leftSection = document.getElementById('leftSection');
    const nav = document.getElementById('navbar');   
    const divNavbar = document.getElementById('divNavbar');


    // Add a click event listener to 'btnUnDisplay'
    btnUnDisplay.addEventListener('click', () => {
        // If the media query matches (phone mode)
        if (mediaQuery.matches) {
            // Remove the class 'w-full' from 'side' and add the class 'w-none'
            side.classList.remove('w-full');
            side.classList.add('w-none');

            // Remove the class 'hidden' from 'rightSection'
            rightSection.classList.remove('hidden');
            leftSection.classList.add('hidden');

            //navbar manip
            nav.classList.remove('w-[75%]');
            nav.classList.add('min-w-full');

            divNavbar.classList.remove('w-[75%]');
            divNavbar.classList.add('min-w-full');

            // Remove the class 'hidden' from 'btnDisplay'
            btnDisplay.classList.remove('hidden');
        }

        // SideBar on display
        if (side.classList.contains('md:w-[25%]')) {
            // Remove the class 'md:w-[25%]' from 'side' and add the class 'md:w-none'
            side.classList.remove('md:w-[25%]');
            side.classList.add('md:w-none');

            leftSection.classList.add('hidden');

            //navbar manip
            nav.classList.remove('w-[75%]');
            nav.classList.add('min-w-full');

            divNavbar.classList.remove('w-[75%]');
            divNavbar.classList.add('min-w-full');

            // Remove the class 'hidden' from 'btnDisplay'
            btnDisplay.classList.remove('hidden');
        }
    });

    // Add a click event listener to 'btnDisplay'
    btnDisplay.addEventListener('click', () => {
        // If the media query matches (phone mode)
        if (mediaQuery.matches) {
            if (side.classList.contains('w-none')) {
                // Remove the class 'w-none' from 'side' and add the class 'w-full'
                side.classList.remove('hidden');
                side.classList.add('w-full');

                //navbar manip
                nav.classList.add('w-[75%]');
                nav.classList.remove('min-w-full');

                divNavbar.classList.add('w-[75%]');
                divNavbar.classList.remove('min-w-full');

                // Add the class 'hidden' to 'rightSection'
                rightSection.classList.add('hidden');
                leftSection.classList.remove('hidden');

                // Add the class 'hidden' to 'btnDisplay'
                btnDisplay.classList.add('hidden');
            }
        }

        // SideBar not on display
        if (side.classList.contains('md:w-none')) {
            // Remove the class 'md:w-none' from 'side' and add the class 'md:w-[25%]'
            side.classList.remove('md:w-none');
            side.classList.add('md:w-[25%]');

            leftSection.classList.remove('hidden');

            //navbar manip
            nav.classList.add('w-[75%]');
            nav.classList.remove('min-w-full');

            divNavbar.classList.add('w-[75%]');
            divNavbar.classList.remove('min-w-full');

            // Add the class 'hidden' to 'btnDisplay'
            btnDisplay.classList.add('hidden');
        }
    });


    if (mediaQuery.matches) {
        side.classList.add('hidden');

        nav.classList.remove('w-[75%]');
        nav.classList.add('min-w-full');

        divNavbar.classList.remove('w-[75%]');
        divNavbar.classList.add('min-w-full');

        btnDisplay.classList.remove('hidden');

        notifList.classList.remove(`right-[163px]`);
        notifList.classList.remove(`top-[55px]`);

        notifList.classList.add(`right-[116px]`);
        notifList.classList.add(`top-[55px]`);


    } else {

        notifList.classList.remove(`right-[116px]`);
        notifList.classList.remove(`top-[55px]`);

        notifList.classList.add(`right-[163px]`);
        notifList.classList.add(`top-[55px]`);

    }

    ///////////////////////////////////////////////////////
    //message Manipulation
    ///////////////////////////////////////////////////////

    const messages = Array.from(document.getElementsByClassName('message'));
        if (messages) { 
            setTimeout(() => {
                messages.forEach((message) => {
                    message.remove();
                })
            }, 4000)
        }
        
</script>
 
@yield('scripts')
        
    </body>
    </html>
