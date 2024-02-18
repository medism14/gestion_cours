@extends('Layouts.authed')

@section('title', 'Forum')

<style>
    #ecritureMessage {
        box-sizing: border-box;
    }
</style>

@section('content')
    <h1 class="text-center md:text-3xl font-bold">Forums de discussion</h1>
    
    <div id="messagerie" class="rounded-lg flex flex-col space-y-2">
        <!-- Tout les messages -->
        <div id="messagerieContainer" class="bg-gray-300 flex-1 flex flex-col text-sm p-2 text-left overflow-y-auto">
            @php
                $dateAncienMessage = null;
                $i = 0;
            @endphp
            @foreach ($forums as $forum)
                <?php 
                    $dateMessage = new DateTime(explode(' ',$forum->created_at)[0]);
                    $dateActuel = new DateTime(explode(' ', now())[0]);

                    $diffJour = $dateActuel->diff($dateMessage)->days;

                    if ($i == 0) {
                        if ($diffJour == 0) {
                            echo '<span class="text-center date">Aujourd\'hui</span>';
                        } else if ($diffJour == 1) {
                            echo '<span class="text-center date">Hier</span>';
                        } else if ($diffJour == 2) {
                            echo '<span class="text-center date">Avant-Hier</span>';
                        } else {
                            echo '<span class="text-center date">Il y\'a ' . $diffJour . ' jours</span>';
                        }
                    }

                    $i++;

                    if ($dateAncienMessage != null) {
                        $diffAncienMessages = $dateAncienMessage->diff($dateMessage)->days;

                        if ($diffAncienMessages != 0) {
                            if ($diffJour == 0) {
                                echo '<span class="text-center date">Aujourd\'hui</span>';
                            } else if ($diffJour == 1) {
                                echo '<span class="text-center date">Hier</span>';
                            } else if ($diffJour == 2) {
                                echo '<span class="text-center date">Avant-Hier</span>';
                            } else {
                                echo '<span class="text-center date">Il y\'a ' . $diffJour . ' jours</span>';
                            }
                        }
                    }

                    $dateAncienMessage = $dateMessage;
                ?>
                <!-- Si c'est un professeur -->
                @if ($forum->user->role == 1)
                    <!-- Si le message vient de moi -->
                    @if ($forum ->user->id == auth()->user()->id)
                        <div class="flex flex-col justify-center p-2 items-end">
                            <span class="text-red-600 flex items-center"><span>{{ $forum->user->first_name }} {{ $forum->user->last_name }}</span>

                            @if (auth()->user()->role == 0) 
                                <form action="{{ route('forums.suppForum', ['id' => $forum->id]) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Etes vous sûr de supprimer ce message ?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                            </span>
                            <p title="Aujourd'hui 15:12" class="w-full text-right break-words">
                                {{ $forum->message }}
                            </p>
                        </div>
                     <!-- Si le message vient d'une autre personne -->
                    @else
                        <div class="flex flex-col justify-start p-2">
                            <span class="text-red-600 flex items-center"><span>{{ $forum->user->first_name }} {{ $forum->user->last_name }}</span>

                            @if (auth()->user()->role == 0) 
                                <form action="{{ route('forums.suppForum', ['id' => $forum->id]) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Etes vous sûr de supprimer ce message ?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                            </span>
                            <p title="Aujourd'hui 15:12">{{ $forum->message }}</p>
                        </div>
                    @endif
                <!-- Si c'est un etudiant -->
                @elseif ($forum->user->role == 2)
                    <!-- Si le message vient de moi -->
                    @if ($forum ->user->id == auth()->user()->id)
                        <div class="flex flex-col justify-center p-2 items-end">
                            <span class="text-blue-600 flex items-center"><span>{{ $forum->user->first_name }} {{ $forum->user->last_name }}</span>

                            @if (auth()->user()->role == 0) 
                                <form action="{{ route('forums.suppForum', ['id' => $forum->id]) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Etes vous sûr de supprimer ce message ?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                            </span>
                            <p title="Aujourd'hui 15:12" class="w-full text-right break-words">
                            {{ $forum->message }}
                            </p>
                        </div>
                     <!-- Si le message vient d'une autre personne -->
                    @else
                        <div class="flex flex-col justify-start p-2 text-left">
                            <span class="text-blue-600 flex items-center"><span>{{ $forum->user->first_name }} {{ $forum->user->last_name }} </span>

                            @if (auth()->user()->role == 0) 
                                <form action="{{ route('forums.suppForum', ['id' => $forum->id]) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Etes vous sûr de supprimer ce message ?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                            </span>
                            <p title="Aujourd'hui 15:12">{{ $forum->message }}</p>
                        </div>
                    @endif  
                @endif
            @endforeach
            <!--  -->
        </div>
        @if (auth()->user()->role != 0)
            <!-- Message d'envoie -->
            <div class="flex justify-center flex-col">
                <form method="POST" action="{{ route('forums.addMsgForum', ['level_id' => request('level_id')]) }}" class="p-0 m-0">
                @csrf
                <div class="flex justify-center">
                    <textarea id="ecritureMessage" name="ecritureMessage" cols="50" rows="5" class="rounded-lg outline-none p-2" maxlength="150"></textarea>
                </div>
                <div class="flex justify-center">
                        <button class="px-3 py-1 bg-blue-400 text-white rounded-lg m-2 shadow-md transition-all duration-300 hover:bg-blue-500">Envoyer</button>
                    </form>
                </div>
            </div>
        @endif
    </div>

@endsection

@section('scripts')
<script>
    //Manipulation de la messagerie
        const messagerie = document.getElementById('messagerie');
        const messagerieContainer = document.getElementById('messagerieContainer');
        let diffHaut = Math.floor(messagerie.getBoundingClientRect().top);
        messagerie.style.height = window.innerHeight - diffHaut + 'px';

        messagerieContainer.scrollTop = messagerieContainer.scrollHeight;
    //
    
    //Check pour les medias
    @if (auth()->user()->role != 0)
        const ecritureMessage = document.getElementById('ecritureMessage');
    @endif
        if (mediaQuery.matches) {
            @if (auth()->user()->role != 0)
                ecritureMessage.setAttribute('cols', '30');
                ecritureMessage.setAttribute('rows', '3');
            @endif
            var tailleEcran = window.innerWidth - 10;
            messagerie.style.width = tailleEcran + 'px';
        } else {
            @if (auth()->user()->role != 0)
                ecritureMessage.setAttribute('cols', '50');
                ecritureMessage.setAttribute('rows', '4');
            @endif

            messagerie.style.width = '100%';
        }
    //

    
</script>
@endsection