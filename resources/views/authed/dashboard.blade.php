@extends('Layouts.authed')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-4xl font-bold text-center">Dashboard</h1>
    @if (auth()->user()->role == 0)
        <div class="flex flex-wrap justify-around mt-6">
            <div class="
                h-[9rem] w-[7rem] text-xs
                sm:h-[10rem] sm:w-[10rem] sm:text-sm 
                md:h-[11rem] md:w-[10rem] md:text-md
                lg:h-[13rem] lg:w-[14rem] lg:text-xl
                bg-green-900 rounded-lg text-white
                flex flex-col">
                <h1 class="border-b-2 
                pt-5 pb-5
                text-center rounded-lg">Nombre de filière</h1>
                <p class="w-full text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-center pt-4">{{ $nombreFilieres }}</p>
                <div class="flex-1 flex items-end justify-end">
                    <form action="{{ route('sectors.index') }}" method="post" class="m-0 p-0">
                        @csrf
                        <button type="submit" value="nombreFilieres" name="nombreFilieres" class="p-1 text-xs md:text-base text-white">Voir plus <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>

            <div class="
                h-[9rem] w-[7rem] text-xs
                sm:h-[10rem] sm:w-[10rem] sm:text-sm 
                md:h-[11rem] md:w-[10rem] md:text-md
                lg:h-[13rem] lg:w-[14rem] lg:text-xl
                bg-yellow-700 rounded-lg text-white
                flex flex-col">
                <h1 class="border-b-2 
                pt-5 pb-5
                text-center rounded-lg">Nombre de professeur</h1>
                <p class="w-full text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-center pt-4">{{ $nombreProfs }}</p>
                <div class="flex-1 flex items-end justify-end">
                    <form action="{{ route('users.index') }}" method="post" class="m-0 p-0">
                        @csrf
                        <button type="submit" value="nombreProfesseurs" name="nombreProfesseurs" class="p-1 text-xs md:text-base text-white">Voir plus <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>

            <div class="
                h-[9rem] w-[7rem] text-xs
                sm:h-[10rem] sm:w-[10rem] sm:text-sm 
                md:h-[11rem] md:w-[10rem] md:text-md
                lg:h-[13rem] lg:w-[14rem] lg:text-xl
                bg-indigo-900 rounded-lg text-white
                flex flex-col">
                <h1 class="border-b-2 pt-5 pb-5 text-center rounded-lg">Nombre d'étudiants</h1>
                <p class="w-full text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-center pt-4">{{ $nombreEtudiants }}</p>
                <div class="flex-1 flex items-end justify-end">
                    <form action="{{ route('users.index') }}" method="post" class="m-0 p-0">
                        @csrf
                        <button type="submit" value="nombreEtudiants" name="nombreEtudiants" class="p-1 text-xs md:text-base text-white">Voir plus <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="block">
            <h1 class="text-center text-3xl mt-12">Interface Administrateur</h1>
            <h1 class="text-center text-md">Le pouvoir est entre vos mains</h1>
        </div>
    @else
        <div class="flex flex-wrap justify-around mt-6">
            <div class="
                h-[9rem] w-[7rem] text-xs
                sm:h-[10rem] sm:w-[10rem] sm:text-sm 
                md:h-[11rem] md:w-[10rem] md:text-md
                lg:h-[13rem] lg:w-[14rem] lg:text-xl
                bg-green-900 rounded-lg text-white
                flex flex-col">
                <h1 class="border-b-2 
                pt-5 pb-5
                text-center rounded-lg">Ressources disponibles</h1>
                <p id="ressourcesDisponibles" class="w-full text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-center pt-4">{{ $ressourcesDisponibles }}</p>
                <div class="flex-1 flex items-end justify-end">
                    <form action="{{ route('resources.index') }}" method="post" class="m-0 p-0">
                        @csrf
                        <button type="submit" value="nombreFilieres" name="nombreFilieres" class="p-1 text-xs md:text-base text-white">Voir plus <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>

            @if (auth()->user()->role == 2)
                <div class="
                    h-[9rem] w-[7rem] text-xs
                    sm:h-[10rem] sm:w-[10rem] sm:text-sm 
                    md:h-[11rem] md:w-[10rem] md:text-md
                    lg:h-[13rem] lg:w-[14rem] lg:text-xl
                    bg-yellow-700 rounded-lg text-white
                    flex flex-col">
                    <h1 class="border-b-2 
                    pt-5 pb-5
                    text-center rounded-lg">Nouvelles Ressources</h1>
                    <p id="nouvelleRessource" class="w-full text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-center pt-4">{{ auth()->user()->notifs }}</p>
                    <div class="flex-1 flex items-end justify-end">
                        <form action="{{ route('resources.index') }}" method="post" class="m-0 p-0">
                            @csrf
                            <button type="submit" value="nouvelleRessource" name="nouvelleRessource" class="p-1 text-xs md:text-base text-white">Voir plus <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="
                h-[9rem] w-[7rem] text-xs
                sm:h-[10rem] sm:w-[10rem] sm:text-sm 
                md:h-[11rem] md:w-[10rem] md:text-md
                lg:h-[13rem] lg:w-[14rem] lg:text-xl
                bg-indigo-900 rounded-lg text-white
                flex flex-col">
                <h1 class="border-b-2 pt-5 pb-5 text-center rounded-lg">Messages non lus</h1>
                <p id="messagesNonLus" class="w-full text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-center pt-4">{{ $messagesNonLus }}</p>
                <div class="flex-1 flex items-end justify-end">
                    <form action="{{ route('forums.index') }}" method="post" class="m-0 p-0">
                        @csrf
                        <button type="submit" value="nombreEtudiants" name="nombreEtudiants" class="p-1 text-xs md:text-base text-white">Voir plus <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>
        </div>

    @endif

    @if (auth()->user()->role == 1)
        <div class="block">
            <h1 class="text-center text-3xl mt-12">Interface Professeur</h1>
            <h1 class="text-center text-md">Ici vous pourrez accéder à vos cours et discutez avec votre filière !</h1>
        </div>
    @elseif (auth()->user()->role == 2)
    <h1 class="text-center text-3xl mt-12">Interface Etudiant</h1>
        <h1 class="text-center">Ici vous pourrez accéder à vos cours et discutez avec votre filière !</h1>
    @endif
        
@endsection

@section('scripts')
<script>
    function positionnementTooltip() {

    };
</script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>

    

    @if (auth()->user()->role == 2) 
        const nouvelleRessource = document.getElementById('nouvelleRessource');
        let notifButtonDash = document.getElementById('notif');
        notifButtonDash.addEventListener('click', () => {   
            nouvelleRessource.innerHTML = '0';
        })

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
        let type = data.type;

        actualUser.levels_users.forEach(async (level) => {
            if (resource.module.level_id == level.level_id) {
                @if (auth()->user()->role == 2) 
                    nouvelleRessource.innerHTML = '' + nombreNotif.textContent;
                @endif

                const responseRessourcesDisponibles = await fetch(`/resources/ressourceDisponible/${actualUserId}`);
                const dataRessourcesDisponibles = await responseRessourcesDisponibles.json();

                ressourcesDisponibles.innerHTML = '' + dataRessourcesDisponibles;
            }
        });
        
    });

    var channel = pusher.subscribe('forum-channel');
        
    channel.bind('forum-new-message', async function(data) {
        let actualUserId = {{ auth()->user()->id }};
        let response = await fetch(`/getUserInfos/${actualUserId}`);
        let actualUser = await response.json();

        console.log(data);

        actualUser.levels_users.forEach(async (level) => {
            if (data.forum.level.id == level.level_id) {
                let levelId = level.level_id;
                await messagesRefresh(levelId);
            }
        });
    });

    channel.bind('forum-delete-message', async function (data) {
        let actualUserId = {{ auth()->user()->id }};
        let response = await fetch(`/getUserInfos/${actualUserId}`);
        let actualUser = await response.json();

        console.log(data);

        actualUser.levels_users.forEach(async (level) => {
            if (data.forum.level.id == level.level_id) {
                let levelId = level.level_id;
                await messagesRefresh(levelId);
            }
        });
    });

    channel.bind('forum-clear', async function (data) {
        let actualUserId = {{ auth()->user()->id }};
        let response = await fetch(`/getUserInfos/${actualUserId}`);
        let actualUser = await response.json();

        actualUser.levels_users.forEach(async (level) => {
            if (data.forum.level.id == level.level_id) {
                let levelId = level.level_id;
                await messagesRefresh(levelId);
            }
        });
    });

    async function messagesRefresh (levelId) {
        const responseMessagesNonLus = await fetch(`/forums/messagesNonLus/${levelId}`);
        const dataMessagesNonLus = await responseMessagesNonLus.json();

        messagesNonLus.innerHTML = '' + dataMessagesNonLus;
    }

</script>

@endsection