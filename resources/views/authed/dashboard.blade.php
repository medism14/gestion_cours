@extends('Layouts.authed')

@section('title', 'Tableau de bord')

@section('content')
<div class="section-animate space-y-8 p-4 md:p-8">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bonjour, {{ auth()->user()->first_name }} !</h1>
            <p class="mt-1 text-gray-500">Heureux de vous revoir sur votre espace numérique d'enseignement.</p>
        </div>
        <div class="flex items-center space-x-3 text-sm font-medium text-gray-500 bg-white px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-100">
            <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
            <span>Système opérationnel</span>
        </div>
    </div>

    @if (auth()->user()->role == 0)
        <!-- Admin Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Stats Card 1: Filières -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <i class="fa-solid fa-layer-group text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500">Nombre de filières</h3>
                    <div class="flex items-baseline space-x-2">
                        <span class="text-3xl font-bold text-gray-900">{{ $nombreFilieres }}</span>
                    </div>
                </div>
                <div class="mt-6">
                    <form action="{{ route('sectors.index') }}" method="post" class="m-0 p-0">
                        @csrf
                        <button type="submit" value="nombreFilieres" name="nombreFilieres" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                            Gérer les filières 
                            <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats Card 2: Professeurs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                        <i class="fa-solid fa-user-tie text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500">Nombre de professeurs</h3>
                    <div class="flex items-baseline space-x-2">
                        <span class="text-3xl font-bold text-gray-900">{{ $nombreProfs }}</span>
                    </div>
                </div>
                <div class="mt-6">
                    <form action="{{ route('users.index') }}" method="post" class="m-0 p-0">
                        @csrf
                        <button type="submit" value="nombreProfesseurs" name="nombreProfesseurs" class="inline-flex items-center text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                            Voir les professeurs 
                            <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats Card 3: Étudiants -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                        <i class="fa-solid fa-graduation-cap text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500">Nombre d'étudiants</h3>
                    <div class="flex items-baseline space-x-2">
                        <span class="text-3xl font-bold text-gray-900">{{ $nombreEtudiants }}</span>
                    </div>
                </div>
                <div class="mt-6">
                    <form action="{{ route('users.index') }}" method="post" class="m-0 p-0">
                        @csrf
                        <button type="submit" value="nombreEtudiants" name="nombreEtudiants" class="inline-flex items-center text-sm font-semibold text-purple-600 hover:text-purple-700 transition-colors">
                            Gérer les étudiants 
                            <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Admin Welcome Area -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 sm:p-12 text-center text-white overflow-hidden relative shadow-xl">
            <div class="relative z-10">
                <h2 class="text-2xl sm:text-4xl font-bold mb-4">Interface Administrateur</h2>
                <p class="text-gray-400 max-w-2xl mx-auto text-sm sm:text-base">
                    Bienvenue dans votre centre de contrôle. D'ici, vous pouvez piloter l'ensemble de la plateforme, gérer les accès et superviser le contenu académique.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('users.index') }}" class="px-6 py-3 bg-white text-gray-900 font-bold rounded-xl hover:bg-gray-100 transition-all transform hover:-translate-y-1">Nouveau utilisateur</a>
                    <a href="{{ route('annonces.index') }}" class="px-6 py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all border border-gray-600 transform hover:-translate-y-1">Publier une annonce</a>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
        </div>

    @else
        <!-- Learner/Teacher Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Ressource Available Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 text-lg">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500">Ressources disponibles</h3>
                    <span id="ressourcesDisponibles" class="text-3xl font-bold text-gray-900">{{ $ressourcesDisponibles }}</span>
                </div>
                <div class="mt-6 border-t border-gray-50 pt-4 text-xs font-semibold uppercase tracking-wider text-gray-400">
                    <form action="{{ route('resources.index') }}" method="post" class="m-0 p-0 flex items-center justify-between">
                        @csrf
                        <span>Accès rapide</span>
                        <button type="submit" value="nombreFilieres" name="nombreFilieres" class="text-blue-600 hover:text-blue-700 underline">Voir tout</button>
                    </form>
                </div>
            </div>

            @if (auth()->user()->role == 2)
                <!-- New Notifications Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
                    <div class="flex items-center justify-between">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300 text-lg">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-500">Nouvelles ressources</h3>
                        <span id="nouvelleRessource" class="text-3xl font-bold text-gray-900">{{ auth()->user()->notifs }}</span>
                    </div>
                </div>
            @endif

            <!-- Messages Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 text-lg">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500">Messages non lus</h3>
                    <span id="messagesNonLus" class="text-3xl font-bold text-gray-900">{{ $messagesNonLus }}</span>
                </div>
                <div class="mt-6 border-t border-gray-50 pt-4 text-xs font-semibold uppercase tracking-wider text-gray-400">
                    <form action="{{ route('forums.index') }}" method="post" class="m-0 p-0 flex items-center justify-between">
                        @csrf
                        <span>Discussions</span>
                        <button type="submit" value="nombreEtudiants" name="nombreEtudiants" class="text-indigo-600 hover:text-indigo-700 underline">Rejoindre</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Role Banner -->
        <div class="relative bg-white border border-gray-100 rounded-3xl p-8 overflow-hidden shadow-sm">
            <div class="flex items-center space-x-6 relative z-10">
                <div class="h-16 w-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl hidden sm:flex">
                    @if (auth()->user()->role == 1) <i class="fa-solid fa-chalkboard-user"></i> @else <i class="fa-solid fa-user-graduate"></i> @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Espace {{ auth()->user()->role == 1 ? 'Enseignant' : 'Étudiant' }}</h2>
                    <p class="text-gray-500 mt-1">
                        @if (auth()->user()->role == 1)
                            Ici vous pourrez accéder à vos cours et discuter avec votre filière !
                        @else
                            Consultez vos ressources académiques et interagissez avec vos camarades.
                        @endif
                    </p>
                </div>
            </div>
            <!-- Abstract background shape -->
            <div class="absolute right-0 top-0 h-full w-1/3 bg-blue-50/50 clip-path-diagonal hidden lg:block"></div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    @if (auth()->user()->role == 2) 
        const nouvelleRessource = document.getElementById('nouvelleRessource');
        let notifButtonDash = document.getElementById('notif');
        if(notifButtonDash) {
            notifButtonDash.addEventListener('click', () => {   
                nouvelleRessource.innerHTML = '0';
            });
        }
    @endif

    const ressourcesDisponibles = document.getElementById('ressourcesDisponibles');
    const messagesNonLus = document.getElementById('messagesNonLus');

    var pusher = new Pusher('6979301f0eee4d497b90', {
        cluster: 'eu'
    });

    var channelNotif = pusher.subscribe('notif-channel');

    channelNotif.bind('notif-refresh', async function (data) {
        let actualUserId = {{ auth()->user()->id }};
        let response = await fetch(`/getUserInfos/${actualUserId}`);
        let actualUser = await response.json();

        let resource = data.resource;
        
        actualUser.levels_users.forEach(async (level) => {
            if (resource.module.level_id == level.level_id) {
                @if (auth()->user()->role == 2) 
                    const nombreNotif = document.getElementById('nombreNotif');
                    nouvelleRessource.innerHTML = '' + (nombreNotif ? nombreNotif.textContent : '0');
                @endif

                const responseRessourcesDisponibles = await fetch(`/resources/ressourceDisponible/${actualUserId}`);
                const dataRessourcesDisponibles = await responseRessourcesDisponibles.json();

                if(ressourcesDisponibles) ressourcesDisponibles.innerHTML = '' + dataRessourcesDisponibles;
            }
        });
    });

    var channel = pusher.subscribe('forum-channel');
        
    channel.bind('forum-new-message', async function(data) {
        let actualUserId = {{ auth()->user()->id }};
        let response = await fetch(`/getUserInfos/${actualUserId}`);
        let actualUser = await response.json();

        actualUser.levels_users.forEach(async (level) => {
            if (data.forum.level.id == level.level_id) {
                await messagesRefresh(level.level_id);
            }
        });
    });

    async function messagesRefresh (levelId) {
        const responseMessagesNonLus = await fetch(`/forums/messagesNonLus/${levelId}`);
        const dataMessagesNonLus = await responseMessagesNonLus.json();
        if(messagesNonLus) messagesNonLus.innerHTML = '' + dataMessagesNonLus;
    }
</script>
@endsection