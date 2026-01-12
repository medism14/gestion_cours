<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"/>
        

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="icon" href="{{ asset('images/papaRounded.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <script>
            // Redéfinir toutes les fonctions de la console pour qu'elles ne fassent rien
            console.warn = function() {};
        </script>
        <script src="https://cdn.tailwindcss.com"></script>

        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
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
            <section id="leftSection" class="block md:w-[20%]">
                <nav id="sidebar" class="fixed w-none md:w-[20%] bg-gray-800 h-screen">
                    <div class="absolute m-1 right-0 flex items-center my-auto h-screen text-4xl">
                        <button id="btn-unDisplay" class="text-white hover:text-gray-300" type="button"><i class="fas fa-angle-left"></i></button>
                    </div>
                    <!-- SideBar Title -->
                    <div class="block p-5 rounded-lg text-white text-xl text-center">
                        <a href="{{ route('dashboard') }}" class="border-b-2">Espace Numérique d'Enseignement</a>
                    </div>

                    <hr class="mb-4 rounded border-4 border-gray-400"></hr>
                    <!-- SideBar Content -->
                    @if (auth()->user()->role == 0)
                        <div class=" text-white p-6 text-center text-xl border-b-2 border-slate-500 w-[80%] mx-auto rounded-lg">
                            <a href="{{ route('users.index') }}" class="sidebar-content relative pb-2"><i class="fa fa-users"></i> Utilisateurs</a>
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
                        <a href="{{ route('forums.index') }}" class="sidebar-content relative pb-2"><i class="fa fa-comments"></i> Forums</a>
                    </div>

                    <div class=" text-white p-6 text-center text-xl border-b-2 border-slate-500 w-[80%] mx-auto rounded-lg">
                        <a href="{{ route('annonces.index') }}" class="sidebar-content relative pb-2"><i class="fa fa-bullhorn"></i> Annonces</a>
                    </div>

                    <div class=" text-white p-6 text-center text-xl border-b-2 border-slate-500 w-[80%] mx-auto rounded-lg">
                        <a href="{{ route('documents.index') }}" class="sidebar-content relative pb-2"><i class="fa fa-file-pdf"></i> Documents</a>
                    </div>

                    <!-- Footer -->
                    <footer class="fixed w-full md:w-[20%] text-center text-white bottom-0 bg-gray-600 hidden">
                        <p>Tout droits reservés ©</p>
                    </footer>
                </nav>
                
            </section>
            <!-- Côté Droit -->
            <section id="rightSection" class="flex-1 md:w-[80%]">
                <!-- Côté droit navbar -->
                <nav id="navbar" class="fixed w-full bg-gray-800 text-white flex p-2 md:w-[80%]">
                    <div id="btn-display" class="hidden absolute fixed text-4xl top-0 left-0 mt-4">
                        <button class="text-gray-100 hover:text-gray-300" type="button"><i class="fas fa-angle-right"></i></button>
                    </div>
                    <div id="divNavbar" class="flex-1 flex items-center">
                        <span class="flex w-full justify-start ml-5">
                            <img src="{{ asset('images/papaRounded.png') }}" alt="Logo" width="50" height="50">
                        </span>
                        <div class="ml-12 md:ml-24 flex-1 text-center text-base md:text-xl font-bold">
                        </div> 

                        <div class="flex items-center justify-end">
                            @if (auth()->user()->role != 0)
                                <button id="annonce" class="notifClass mr-2 md:mr-6 border-2 p-2 rounded-lg transition-all duration-300 ease-in-out bg-white text-black hover:bg-gray-400 relative ">
                                    <span id="nombreAnnonce" class="text-gray-900 font-bold text-[0.5rem] text-blue-500 font-bold absolute top-0 right-0">{{ auth()->user()->annonces }}</span>
                                    <i class="fa fa-bullhorn"></i>
                                </button>
                            @endif
                            @if (auth()->user()->role == 2)
                                <button id="notif" class="annonceClass mr-2 md:mr-6 border-2 p-2 rounded-lg transition-all duration-300 ease-in-out bg-white text-black hover:bg-gray-400 relative ">
                                    <span id="nombreNotif" class="text-gray-900 font-bold text-[0.5rem] text-blue-500 font-bold absolute top-0 right-0">{{ auth()->user()->notifs }}</span>
                                    <i class="fa fa-bell"></i>
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
                    
                    <div id="notifList" class="notifList hidden absolute z-50 flex flex-col overflow-y-auto text-xs md:text-base p-3 bg-slate-800 border-2 border-gray-400 rounded-lg" style="max-height: calc(3rem * 3);">
                        <div class="flex w-full items-center">
                            <h1 id="notifResource" class="flex-1 text-center font-bold mb-3  px-4 border-r-2">Notifications Ressources</h1>
                            <form action="{{ route('users.suppNotifs') }}"  method="POST" class="m-0 p-0 flex justify-end" onsubmit="return confirm('Voulez-vous vraiment supprimer vos notifications ?')">
                                @csrf
                                <button id="allDeleteNotif" class="hidden text-red-500 font-bold mb-2 p-2">Tout supprimer</button>
                            </form>
                        </div>
                        <hr class="my-2">
                    </div>

                    <div id="annonceList" class="annonceList hidden absolute z-50 flex flex-col overflow-y-auto text-xs md:text-base p-3 bg-slate-800 border-2 border-gray-400 rounded-lg" style="max-height: calc(3rem * 3);">
                        <div class="flex w-full items-center">
                            <h1 id="notifAnnonce" class="flex-1 text-center font-bold mb-3 px-4 border-r-2">Notifications Annonces</h1>
                            <form action="{{ route('annonces.suppAnnonces') }}"  method="POST" class="m-0 p-0 flex justify-end" onsubmit="return confirm('Voulez-vous vraiment supprimer vos notifications ?')">
                                @csrf
                                @method('DELETE')
                                <button id="allDeleteAnnonce" class="hidden text-red-500 font-bold mb-2 p-2">Tout supprimer</button>
                            </form>
                        </div>
                        <hr class="my-2">
                    </div>

                    <div id="userList" class="hidden z-20 absolute flex flex-col text-xs md:text-sm p-3 bg-slate-800 border-2 border-gray-400 rounded-lg">
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
                                @if (auth()->user()->role == 2)
                                    @foreach (auth()->user()->levels_users as $levels_users)
                                        <span class="text-center">Filière: {{ $levels_users->level->sector->name }} {{ $levels_users->level->name }}</span>
                                        <hr class="border-gray-500">
                                    @endforeach
                                @endif
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
                                    errors += "{!! $sub_v !!}\n";
                                </script>

                                @endforeach
                            @endforeach
                            <script>
                                alert(errors);
                            </script>
                        @else
                            <script>
                                alert("{!! session('error') !!}");
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

        <!-- Modal Voir Annonce -->
        <div id="modalVoirAnnonce" class="hidden z-50 absolute bg-gray-500 bg-opacity-75 inset-0">
            <!-- Modal body -->
            <div class="bg-gray-100 w-[50%] mx-auto mt-20 flex flex-col rounded">
                <!-- Header -->
                <div class="bg-green-800 text-white text-center p-4 rounded rounded-b-none text-xl relative">
                    Annonce
                    <span class="absolute right-0 px-4"><i class="fas fa-times cursor-pointer" id="closeModal"></i></span>
                </div>
                <!-- Body -->
                <div class="p-4 flex flex-col space-y-5">
                    <!-- Row -->
                    <div class="flex justify-center">
                        <div class="flex-1 flex justify-center">
                            <h3 class="text-xl font-bold underline" id="annonceTitle"></h3>
                        </div>
                    </div>

                    <!-- Row -->
                    <div class="flex justify-center">
                        <div class="flex-1 flex justify-start">
                            <h3 class="text-base font-bold" id="annonceUser"></h3>
                        </div>
                    </div>

                    <!-- Row -->
                    <div class="flex">
                        <div class="flex-1 flex break-words">
                            <p id="annonceContent"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script>
    // Create a MediaQueryList object for the media query "(max-width:768px)"
    var mediaQuery = window.matchMedia("(max-width:768px)");

    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    //
    ///////////////////////////////////////////////////////
    //Notif MANIPULATION
    ///////////////////////////////////////////////////////

    @if (auth()->user()->role == 2)
    document.addEventListener("DOMContentLoaded", async function () {
        const annonceList = document.getElementById('annonceList');
        const notifButton = document.getElementById('notif');
        const notifList = document.getElementById('notifList'); 
        const allDeleteNotif = document.getElementById('allDeleteNotif');
        const nombreNotif = document.getElementById('nombreNotif');

        const notif = document.getElementById('notif');

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
                data.forEach(async (notif) => {
                    let responseOfNowDate = await fetch('/nowDate');
                    let now = await responseOfNowDate.json();

                    let responseOfCreatedDate = await fetch(`/getNotifCreatedTime/${notif.id}`);
                    let created_at = await responseOfCreatedDate.json();

                    let finalDate;

                    now = new Date(now);
                    created_at = new Date(created_at);

                    let differenceEnMilli = now - created_at;

                    let diffJour = Math.floor(differenceEnMilli / (1000*60*60*24));

                    if (diffJour == 0) {
                        finalDate = 'Aujourd\'hui';
                    } else if (diffJour == 1) {
                        finalDate = 'Hier';
                    } else if (diffJour == 2) {
                        finalDate = 'Avant-hier';
                    } else {
                        finalDate = `Il y'a ${diffJour} jours`;
                    }

                    notifList.innerHTML += `
                        <div class="notifValue flex space-x-2 md:space-x-4 p-2 text-xs md:text-base border-b-2">
                            <div class="flex notifs justify-center flex-col items-center">
                                <span class="">Module:</span>
                                <span class="underline">${notif.resource.module.name}</span>
                                <span class="">Date:</span>
                                <span class="underline">${finalDate}</span>
                            </div>
                            <div class="flex notifs justify-end items-center">
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
                    notifResource.classList.remove('border-r-2');
                    notifList.innerHTML += `
                        <span id="aucuneNotif" class="text-center text-xs md:text-base">Aucune notification</span>
                    `;
                } else {
                    allDeleteNotif.classList.remove('hidden');
                }

            })
            .catch(error => {
                console.error('Error: ' + error);
        });

        //Lorsqu'on clique sur le boutons de notif
        notifButton.addEventListener('click', () => {
            notifList.classList.toggle('hidden');

            annonceList.classList.add('hidden');
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
        
    });

        function positionnementNotif () {
            let positionX;
            let positionY;

            if (mediaQuery.matches) {
                positionX = window.innerWidth / 3;

                positionY = notif.getBoundingClientRect().height + notif.getBoundingClientRect().y + 5;
            } else {
                positionX = window.innerWidth - (notif.getBoundingClientRect().x + notif.getBoundingClientRect().width);

                positionY = notif.getBoundingClientRect().height + notif.getBoundingClientRect().y + 5;
            }

            notifList.classList.add(`top-[${positionY}px]`, `right-[${positionX}]`);
        }

        positionnementNotif();
    @endif

    ///////////////////////////////////////////////////////
    //Annonce MANIPULATION
    ///////////////////////////////////////////////////////
        const annonceBtn = document.getElementById('annonce');
        const nbreAnnonceBtn = document.getElementById('nbreAnnonce');
        if (!annonceList) {
            var annonceList = document.getElementById('annonceList');
        }
        
        const modalVoirAnnonce = document.getElementById('modalVoirAnnonce');
        const annonceTitle = document.getElementById('annonceTitle');
        const annonceUser = document.getElementById('annonceUser');
        const annonceContent = document.getElementById('annonceContent');
        const closeModal = document.getElementById('closeModal');

        const nombreAnnonce = document.getElementById('nombreAnnonce');

        const allDeleteAnnonce = document.getElementById('allDeleteAnnonce');

    @if (auth()->user()->role != 0)
        //Remplir les annonces
        async function ramenerValeursAnnonces() {
            let response = await fetch(`/annonces/getAnnonces`);

            let data = await response.json();

            data.forEach(async (annonce) => {

                let responseOfNowDate = await fetch('/nowDate');
                let now = await responseOfNowDate.json();

                let responseOfCreatedDate = await fetch(`/annonces/getAnnonceCreatedTime/${annonce.id}`);
                let created_at = await responseOfCreatedDate.json();

                now = new Date(now);
                created_at = new Date(created_at);

                let differenceEnMilli = now - created_at;

                let diffJour = parseInt(differenceEnMilli / (1000*60*60*24));

                let finaleDate = 'Aujourd\'hui';

                if (diffJour == 0) {
                    finaleDate = `Aujourd'hui`;
                } else if (diffJour == 1) {
                    finaleDate = `Hier`;
                } else if (diffJour == 2) {
                    finaleDate = `Avant-Hier`;
                } else {
                    finaleDate = `Il y'a ${diffJour} jours`;
                }

                annonceList.innerHTML += `
                    <div class="annonceValue flex justify-center text-xs md:text-base space-x-2 py-3 border-b-2">
                        <div class="flex-1 flex items-center space-x-2 justify-center underline">${finaleDate}</div>
                        <input value="${annonce.id}" class="hidden">
                        <button class="voirAnnonce flex-1 bg-blue-600 outline-none text-xs md:text-base shadow-md transition-all duration-300 ease-in-out px-1 md:px-3 md:py-1 rounded-lg hover:bg-blue-700">Voir l'annonce</button>
                    </div>
                `;

            })
            
            if (data.length == 0) {
                notifAnnonce.classList.remove('border-r-2');
                annonceList.innerHTML += `
                    <span id="aucuneAnnonce" class="text-center text-xs md:text-base">Aucune annonce</span>
                `;
            } else {
                allDeleteAnnonce.classList.remove('hidden');
            }
        }

        ramenerValeursAnnonces();

         //Pour afficher les annonces
        annonceBtn.addEventListener('click', async () => {
            annonceList.classList.toggle('hidden');

            if (notifList) {
                notifList.classList.add('hidden');
            }

            let response = await fetch(`/annonces/resetAnnonces`);
            nombreAnnonce.innerHTML = '0'
        });

        //Fermer les annonces
        closeModal.addEventListener('click', () => {
                location.reload();
            });

         //S'il clique pour voir une annonce
         annonceList.addEventListener('click', async (event) => {
                let item = event.target;

                if (item.classList.contains('voirAnnonce')) {
                    let id = item.parentNode.querySelector('input').value;
                    
                    let response = await fetch(`/annonces/getAnnonce/${id}`);
                    let data = await response.json();
                    let lastName;

                    if (data.annonce.user.last_name == null) {
                        lastName = '';
                    } else {
                        lastName = data.annonce.user.last_name;
                    }

                    if (data.annonce.user.role == 0) {
                    role = 'Responsable: ';
                    } else {
                        role = 'Professeur: ';
                    }

                    annonceTitle.innerHTML = data.annonce.title;
                    annonceUser.innerHTML = role + ': ' + data.annonce.user.first_name + ' ' + lastName;
                    
                    let annonceContentt = data.annonce.content;

                    annonceContent.innerHTML = annonceContentt.replace(/\n/g, "<br>");

                    modalVoirAnnonce.classList.remove('hidden');
                }
            });
        
        document.addEventListener('click', (event) => {
            let item = event.target;

            if ((!item.classList.contains('annonceClass') && !item.classList.contains('notifClass')) && (!item.parentNode.classList.contains('annonceClass') && !item.parentNode.classList.contains('notifClass'))) {
                if ((!item.classList.contains('annonceList') && !item.classList.contains('notifList')) && (!item.parentNode.classList.contains('annonceList') && !item.parentNode.classList.contains('notifList')) && (!item.parentNode.parentNode.classList.contains('annonceList') && !item.parentNode.parentNode.classList.contains('notifList')) && (!item.parentNode.parentNode.parentNode.classList.contains('annonceList') && !item.parentNode.parentNode.parentNode.classList.contains('notifList'))) {
                    annonceList.classList.add('hidden');
                    notifList.classList.add('hidden');
                }
            }
        });

        //Bien positionner la liste des annonces
        function positionnementAnnonce () {
            let positionX;
            let positionY;

            if (mediaQuery.matches) {
                positionX = window.innerWidth / 3;

                positionY = annonceBtn.getBoundingClientRect().height + annonceBtn.getBoundingClientRect().y + 5;
            } else {
                positionX = window.innerWidth - (annonceBtn.getBoundingClientRect().x + annonceBtn.getBoundingClientRect().width);

                positionY = annonceBtn.getBoundingClientRect().height + annonceBtn.getBoundingClientRect().y + 5;
            }

                annonceList.classList.add(`top-[${positionY}px]`, `right-[${positionX}]`);
        }

        positionnementAnnonce();

    @endif

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
                    if (annonceList) {
                        annonceList.classList.add('hidden');
                    }

                    if (notifList) {
                        notifList.classList.add('hidden');
                    }
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
                    if (annonceList) {
                        annonceList.classList.add('hidden');
                    }

                    if (notifList) {
                        notifList.classList.add('hidden');
                    }

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
                            if (annonceList) {
                                annonceList.classList.add('hidden');
                            }

                            if (notifList) {
                                notifList.classList.add('hidden');
                            }
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
                        if (annonceList) {
                            annonceList.classList.add('hidden');
                        }

                        if (notifList) {
                            notifList.classList.add('hidden');
                        }
                    });

                    userList.addEventListener('mouseleave', () => {
                        inUserList = false;
                        userList.classList.add('hidden');
                    });
            //
        }


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
            nav.classList.remove('w-[80%]');
            nav.classList.add('min-w-full');

            divNavbar.classList.remove('w-[80%]');
            divNavbar.classList.add('min-w-full');

            // Remove the class 'hidden' from 'btnDisplay'
            btnDisplay.classList.remove('hidden');
        }

        // SideBar on display
        if (side.classList.contains('md:w-[20%]')) {
            // Remove the class 'md:w-[20%]' from 'side' and add the class 'md:w-none'
            side.classList.remove('md:w-[20%]');
            side.classList.add('md:w-none');

            leftSection.classList.add('hidden');

            //navbar manip
            nav.classList.remove('w-[80%]');
            nav.classList.add('min-w-full');

            divNavbar.classList.remove('w-[80%]');
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
                nav.classList.add('w-[80%]');
                nav.classList.remove('min-w-full');

                divNavbar.classList.add('w-[80%]');
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
            // Remove the class 'md:w-none' from 'side' and add the class 'md:w-[20%]'
            side.classList.remove('md:w-none');
            side.classList.add('md:w-[20%]');

            leftSection.classList.remove('hidden');

            //navbar manip
            nav.classList.add('w-[80%]');
            nav.classList.remove('min-w-full');

            divNavbar.classList.add('w-[80%]');
            divNavbar.classList.remove('min-w-full');

            // Add the class 'hidden' to 'btnDisplay'
            btnDisplay.classList.add('hidden');
        }
    });


    if (mediaQuery.matches) {
        side.classList.add('hidden');

        nav.classList.remove('w-[80%]');
        nav.classList.add('min-w-full');

        divNavbar.classList.remove('w-[80%]');
        divNavbar.classList.add('min-w-full');

        btnDisplay.classList.remove('hidden');


    } else {

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
                positionnementTooltip();
            }, 4000)
        }
        
</script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    var pusher = new Pusher('6979301f0eee4d497b90', {
        cluster: 'eu'
    });

    var channelAnnonce = pusher.subscribe('annonce-channel');

    channelAnnonce.bind('annonce-refresh', async function (data) {
        let actualUser = @json(auth()->user());
        let annonce = data.annonce;
        let type = data.type;

        let trouver = false;
        let annonce_relation;

        annonce.annonces_relations.forEach((relation) => {
            if (relation.user_id == actualUser.id) {
                trouver = true;
            }
        });

        if (trouver) {
            switch (type) {
                case 'add':
                    let responseAdd = await fetch(`/annonces/getAnnonces`);

                    let dataAnnoncesAdd = await responseAdd.json();

                    let annonceValueAdd = Array.from(document.getElementsByClassName('annonceValue'));

                    if (annonceValueAdd) {
                        annonceValueAdd.forEach((annonce) => {
                            annonce.remove();
                        });
                    }

                    dataAnnoncesAdd.forEach(async (annonce) => {

                        let responseOfNowDate = await fetch('/nowDate');
                        let now = await responseOfNowDate.json();

                        let responseOfCreatedDate = await fetch(`/annonces/getAnnonceCreatedTime/${annonce.id}`);
                        let created_at = await responseOfCreatedDate.json();

                        now = new Date(now);
                        created_at = new Date(created_at);

                        let differenceEnMilli = now - created_at;

                        let diffJour = parseInt(differenceEnMilli / (1000*60*60*24));

                        let finaleDate = 'Aujourd\'hui';

                        if (diffJour == 0) {
                            finaleDate = `Aujourd'hui`;
                        } else if (diffJour == 1) {
                            finaleDate = `Hier`;
                        } else if (diffJour == 2) {
                            finaleDate = `Avant-Hier`;
                        } else {
                            finaleDate = `Il y'a ${diffJour} jours`;
                        }

                        annonceList.innerHTML += `
                            <div class="annonceValue flex justify-center text-xs md:text-base space-x-2 py-3 border-b-2">
                                <div class="flex-1 flex items-center space-x-2 justify-center underline">${finaleDate}</div>
                                <input value="${annonce.id}" class="hidden">
                                <button class="voirAnnonce flex-1 bg-blue-600 outline-none text-xs md:text-base shadow-md transition-all duration-300 ease-in-out px-1 md:px-3 md:py-1 rounded-lg hover:bg-blue-700">Voir l'annonce</button>
                            </div>
                        `;

                    });

                    document.getElementById('allDeleteAnnonce').classList.remove('hidden');

                    let aucuneAnnonce = document.getElementById('aucuneAnnonce');

                    if (aucuneAnnonce) {
                        aucuneAnnonce.remove();
                        notifAnnonce.classList.add('border-r-2');
                    }

                    nombreAnnonce.innerHTML = parseInt(nombreAnnonce.textContent) + 1;
                break;
                    
                case 'edit':
                    let responseEdit = await fetch(`/annonces/getAnnonces`);

                    let dataAnnoncesEdit = await responseEdit.json();

                    let annonceValueEdit = Array.from(document.getElementsByClassName('annonceValue'));

                    if (annonceValueEdit) {
                        annonceValueEdit.forEach((annonce) => {
                            annonce.remove();
                        });
                    }
                    

                    dataAnnoncesEdit.forEach(async (annonce) => {

                        let responseOfNowDate = await fetch('/nowDate');
                        let now = await responseOfNowDate.json();

                        let responseOfCreatedDate = await fetch(`/annonces/getAnnonceCreatedTime/${annonce.id}`);
                        let created_at = await responseOfCreatedDate.json();

                        now = new Date(now);
                        created_at = new Date(created_at);

                        let differenceEnMilli = now - created_at;

                        let diffJour = parseInt(differenceEnMilli / (1000*60*60*24));

                        let finaleDate = 'Aujourd\'hui';

                        if (diffJour == 0) {
                            finaleDate = `Aujourd'hui`;
                        } else if (diffJour == 1) {
                            finaleDate = `Hier`;
                        } else if (diffJour == 2) {
                            finaleDate = `Avant-Hier`;
                        } else {
                            finaleDate = `Il y'a ${diffJour} jours`;
                        }

                        annonceList.innerHTML += `
                            <div class="annonceValue flex justify-center text-xs md:text-base space-x-2 py-3 border-b-2">
                                <div class="flex-1 flex items-center space-x-2 justify-center underline">${finaleDate}</div>
                                <input value="${annonce.id}" class="hidden">
                                <button class="voirAnnonce flex-1 bg-blue-600 outline-none text-xs md:text-base shadow-md transition-all duration-300 ease-in-out px-1 md:px-3 md:py-1 rounded-lg hover:bg-blue-700">Voir l'annonce</button>
                            </div>
                        `;

                    });

                    if (dataAnnoncesEdit.length == 0) {
                        notifAnnonce.classList.remove('border-r-2');
                        document.getElementById('allDeleteAnnonce').classList.add('hidden');
                        annonceList.innerHTML += `
                            <span id="aucuneAnnonce" class="text-center text-xs md:text-base">Aucune annonce</span>
                        `;
                    } else {
                        notifAnnonce.classList.add('border-r-2');
                        document.getElementById('allDeleteAnnonce').classList.remove('hidden');

                        let aucuneAnnonce = document.getElementById('aucuneAnnonce');

                        if (aucuneAnnonce) {
                            aucuneAnnonce.remove();
                        }
                    }

                    if (parseInt(nombreAnnonce.textContent) == 0) {
                        nombreAnnonce.innerHTML = parseInt(nombreAnnonce.textContent) + 1;
                    }

                break;

                case 'delete':
                    let responseDelete = await fetch(`/annonces/getAnnonces`);

                    let dataAnnoncesDelete = await responseDelete.json();

                    let annonceValueDelete = Array.from(document.getElementsByClassName('annonceValue'));

                    if (annonceValueDelete) {
                        annonceValueDelete.forEach((annonce) => {
                            annonce.remove();
                        });
                    }
                    

                    dataAnnoncesDelete.forEach(async (annonce) => {

                        let responseOfNowDate = await fetch('/nowDate');
                        let now = await responseOfNowDate.json();

                        let responseOfCreatedDate = await fetch(`/annonces/getAnnonceCreatedTime/${annonce.id}`);
                        let created_at = await responseOfCreatedDate.json();

                        now = new Date(now);
                        created_at = new Date(created_at);

                        let differenceEnMilli = now - created_at;

                        let diffJour = parseInt(differenceEnMilli / (1000*60*60*24));

                        let finaleDate = 'Aujourd\'hui';

                        if (diffJour == 0) {
                            finaleDate = `Aujourd'hui`;
                        } else if (diffJour == 1) {
                            finaleDate = `Hier`;
                        } else if (diffJour == 2) {
                            finaleDate = `Avant-Hier`;
                        } else {
                            finaleDate = `Il y'a ${diffJour} jours`;
                        }

                        annonceList.innerHTML += `
                            <div class="annonceValue flex justify-center text-xs md:text-base space-x-2 py-3 border-b-2">
                                <div class="flex-1 flex items-center space-x-2 justify-center underline">${finaleDate}</div>
                                <input value="${annonce.id}" class="hidden">
                                <button class="voirAnnonce flex-1 bg-blue-600 outline-none text-xs md:text-base shadow-md transition-all duration-300 ease-in-out px-1 md:px-3 md:py-1 rounded-lg hover:bg-blue-700">Voir l'annonce</button>
                            </div>
                        `;

                    });

                    if (dataAnnoncesDelete.length == 0) {
                        notifAnnonce.classList.remove('border-r-2');
                        document.getElementById('allDeleteAnnonce').classList.add('hidden');
                        annonceList.innerHTML += `
                            <span id="aucuneAnnonce" class="text-center text-xs md:text-base">Aucune annonce</span>
                        `;
                    }
                    

                    if (parseInt(nombreAnnonce.textContent) > 0) {
                        nombreAnnonce.innerHTML = parseInt(nombreAnnonce.textContent) - 1;
                    }
                break;
            }
        }
        
    });

    var channelNotif = pusher.subscribe('notif-channel');

    channelNotif.bind('notif-refresh', async function (data) {
        let resource = data.resource;

        let actualUserId = {{ auth()->user()->id }};
        let type = data.type;

        const response = await fetch(`/getUserInfos/${actualUserId}`);
        
        let actualUser = await response.json();

        actualUser.levels_users.forEach((level) => {
            if (resource.module.level_id == level.level_id) {
                //Supprimer les valeurs déjà existantes
                let notifValue = Array.from(document.getElementsByClassName('notifValue'));
                if (notifValue) {
                    notifValue.forEach((notif) => {
                        notif.remove();
                    });
                }

                //Pour les notifications et le supprimer
                let aucuneNotif = document.getElementById('aucuneNotif');
                if (aucuneNotif) {
                    aucuneNotif.remove();
                }
                
                //Récupérer le id pour l'utiliser dans le fetch
                let id = {{ auth()->user()->id }};

                fetch(`/getUserNotifs/${id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        data.forEach(async (notif) => {
                            let responseOfNowDate = await fetch('/nowDate');
                            let now = await responseOfNowDate.json();

                            let responseOfCreatedDate = await fetch(`getNotifCreatedTime/${notif.id}`);
                            let created_at = await responseOfCreatedDate.json();

                            let finalDate;

                            now = new Date(now);
                            created_at = new Date(created_at);

                            let differenceEnMilli = now - created_at;

                            let diffJour = Math.floor(differenceEnMilli / (1000*60*60*24));

                            if (diffJour == 0) {
                                finalDate = 'Aujourd\'hui';
                            } else if (diffJour == 1) {
                                finalDate = 'Hier';
                            } else if (diffJour == 2) {
                                finalDate = 'Avant-hier';
                            } else {
                                finalDate = `Il y'a ${diffJour} jours`;
                            }

                            notifList.innerHTML += `
                                <div class="notifValue flex space-x-2 md:space-x-4 p-2 text-xs md:text-base border-b-2">
                                    <div class="flex notifs justify-center flex-col items-center">
                                        <span class="">Module:</span>
                                        <span class="underline">${notif.resource.module.name}</span>
                                        <span class="">Date:</span>
                                        <span class="underline">${finalDate}</span>
                                    </div>
                                    <div class="flex notifs justify-end items-center">
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
                            notifResource.classList.remove('border-r-2');

                            notifList.innerHTML += `
                                <span id="aucuneNotif" class="text-center text-xs md:text-base">Aucune notification</span>
                            `;
                        } else {
                            notifResource.classList.add('border-r-2');
                            allDeleteNotif.classList.remove('hidden');
                        }

                    })
                    .catch(error => {
                        console.error('Error: ' + error);
                });

                let nombreNotifSpan = document.getElementById('nombreNotif');

                let nombreNotifActuel = parseInt(nombreNotifSpan.textContent);

                switch (type) {
                    case 'add': 
                        nombreNotifSpan.innerHTML = '' + parseInt(nombreNotifActuel + 1);
                    break;
                        
                    case 'edit':
                        if (nombreNotifActuel == 0) {
                            nombreNotifSpan.innerHTML = '' + nombreNotifActuel + 1;
                        }
                    break;
                    
                    case 'delete':
                        if (nombreNotifActuel > 0) {
                            nombreNotifSpan.innerHTML = '' + nombreNotifActuel - 1;
                        }
                    break;
                }
            }
        });
        
    });

    //Soummission des formulaires
        var formIsSubmitting = false;

        function submitFunction () {

            if (formIsSubmitting) {
                return false
            } else {
                formIsSubmitting = true;
                return true;
            }
        }

        function submitFunctionFalse () {
            return false
        }
    //

</script>
 
@yield('scripts')
        
    </body>
    </html>
