@extends('Layouts.authed')

@section('title', 'Forum')

<style>
    #ecritureMessage {
        box-sizing: border-box;
    }
</style>

@section('content')
@php
    $forumId = 667;
@endphp

    <div class="flex w-full">
        <div class="w-1/6"></div>
        <div class="w-4/6 flex justify-center">
            <h1 class="text-center md:text-3xl font-bold">Forums de discussion</h1>
        </div>
        <div class="w-1/6 flex justify-end text-[0.7rem] md:text-xs align-end">
            @if (auth()->user()->role == 0)
                <form method="POST" action="{{ route('forums.suppAllMsg', ['level_id' => $level_id]) }}" class="p-0 m-0" onsubmit="return confirm('Voulez-vous vraiment supprimer toutes les messages?')">
                    @method('DELETE')
                    @csrf
                    <button class="px-2 py-1 rounded-lg text white border-2 border-red-500 transition duration-300 hover:bg-red-500 hover:text-white">Supprimer tout les messages</button>
                </form>
            @endif
        </div>
    </div>
    
    
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
                        <div class="messages flex flex-col justify-center p-2 items-end">
                            <input type="hidden" value="{{ $forum->id }}">
                            <span class="text-red-600 flex items-center"><span>{{ $forum->user->role == 0 ? 'Administrateur' : ($forum->user->role == 1 ? 'Professeur' : 'Etudiant') }}: {{ $forum->user->first_name }} {{ $forum->user->last_name }}</span>

                            @if (auth()->user()->role == 0) 
                                <button class="suppMsg ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                            @endif
                            </span>
                            <p class="w-full text-right break-words">
                                {!! $forum->message !!}
                            </p>
                        </div>
                     <!-- Si le message vient d'une autre personne -->
                    @else
                        <div class="messages flex flex-col justify-start p-2">
                            <input type="hidden" value="{{ $forum->id }}">
                            <span class="text-red-600 flex items-center"><span>{{ $forum->user->role == 0 ? 'Administrateur' : ($forum->user->role == 1 ? 'Professeur' : 'Etudiant') }}: {{ $forum->user->first_name }} {{ $forum->user->last_name }}</span>

                            @if (auth()->user()->role == 0) 
                                <button class="suppMsg ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                            @endif
                            </span>
                            <p>{!! $forum->message !!}</p>
                        </div>
                    @endif
                <!-- Si c'est un etudiant -->
                @elseif ($forum->user->role == 2)
                    <!-- Si le message vient de moi -->
                    @if ($forum ->user->id == auth()->user()->id)
                        <div class="messages flex flex-col justify-center p-2 items-end">
                            <input type="hidden" value="{{ $forum->id }}">
                            <span class="text-blue-600 flex items-center"><span>{{ $forum->user->role == 0 ? 'Administrateur' : ($forum->user->role == 1 ? 'Professeur' : 'Etudiant') }}: {{ $forum->user->first_name }} {{ $forum->user->last_name }}</span>

                            @if (auth()->user()->role == 0) 
                                <button class="suppMsg ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                            @endif
                            </span>
                            <p class="w-full text-right break-words">
                            {!! $forum->message !!}
                            </p>
                        </div>
                     <!-- Si le message vient d'une autre personne -->
                    @else
                        <div class="messages flex flex-col justify-start p-2 text-left">
                            <input type="hidden" value="{{ $forum->id }}">
                            <span class="text-blue-600 flex items-center"><span>{{ $forum->user->role == 0 ? 'Administrateur' : ($forum->user->role == 1 ? 'Professeur' : 'Etudiant') }}: {{ $forum->user->first_name }} {{ $forum->user->last_name }} </span>

                            @if (auth()->user()->role == 0) 
                                <button class="suppMsg ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                            @endif
                            </span>
                            <p>{!! $forum->message !!}</p>
                        </div>
                    @endif  
                @endif
            @endforeach
            <!--  -->
        </div>
        @if (auth()->user()->role != 0)
            <!-- Message d'envoie -->
            <div class="flex justify-center flex-col">
                <form method="POST" action="{{ route('forums.addMsgForum', ['level_id' => request('level_id')]) }}" class="p-0 m-0" onsubmit="return submitFunction()">
                @csrf
                <div class="flex justify-center">
                    <textarea id="ecritureMessage" name="ecritureMessage" cols="50" rows="5" class="rounded-lg outline-none p-2" maxlength="450"></textarea>
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
    function positionnementTooltip() {

    }

    //Manipulation de la messagerie
        const messagerie = document.getElementById('messagerie');
        const messagerieContainer = document.getElementById('messagerieContainer');
        function ReglageTailleMessagerie () {
            let diffHaut = Math.floor(messagerie.getBoundingClientRect().top);
            messagerie.style.height = window.innerHeight - diffHaut + 'px';

            messagerieContainer.scrollTop = messagerieContainer.scrollHeight;
        } 
        ReglageTailleMessagerie();

        window.addEventListener('resize', function() {
            ReglageTailleMessagerie();
        });
        
    //
    
    @if (auth()->user()->role == 0)
    //Supprimer un commentaire
        

        function AllMessages () {
            var allMessage = Array.from(document.getElementsByClassName('suppMsg'));

            allMessage.forEach((msg) => {
                msg.addEventListener('click', async (event) => {
                    let id = parseInt(msg.parentNode.parentNode.querySelector('input').value);

                    if (confirm("Etes vous sûr de vouloir supprimer le message ?")) {
                        await fetch(`/forums/suppForum/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                    }
                });
            });
        }
        AllMessages();
    //
    @endif

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

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        var pusher = new Pusher('6979301f0eee4d497b90', {
        cluster: 'eu'
        });

        var channel = pusher.subscribe('forum-channel');
        channel.bind('forum-new-message', async function(data) {

            let forum = data.forum;

            let forumId = forum.id;

            if (data.actualiser) {
                location.reload();
            }

            let level_id_broadcast = parseInt(forum.level.id);

            let level_id_actual = parseInt({{ $level_id }});

            let responseResetMsg = fetch(`/users/resetMsg/${level_id_actual}`);

            if (level_id_broadcast == level_id_actual) {
                let userMessageOrigine = forum.user;
                let actualUser = @json(auth()->user());
                let message = forum.message;

                //Si l'origine du message est un professeur
                if (userMessageOrigine.role == 1) {
                    //Si c'est l'utilisateur connecté
                    if (actualUser.id == userMessageOrigine.id) {
                        messagerieContainer.innerHTML += `
                            <div class="messages flex flex-col justify-center p-2 items-end">
                                <input type="hidden" value="${forumId}">
                                
                                <span class="text-red-600 flex items-center"><span>${forum.user.first_name} ${forum.user.last_name}</span>

                                @if (auth()->user()->role == 0) 
                                    <button class="suppMsg ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                                @endif
                                </span>
                                <p class="w-full text-right break-words">
                                    ${message}
                                </p>
                            </div>
                        `;
                    //Si c'est pas lui
                    } else {
                        messagerieContainer.innerHTML += `
                        <div class="messages flex flex-col justify-start p-2">
                            <input type="hidden" value="${forumId}">
                            <span class="text-red-600 flex items-center"><span>${forum.user.first_name} ${forum.user.last_name}</span>

                            @if (auth()->user()->role == 0) 
                                <button class="suppMsg ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                            @endif
                            </span>
                            <p>${message}</p>
                        </div>
                        `;
                    }   
                //Si l'origine du message est un étudiant
                } else if (userMessageOrigine.role == 2) {
                    //Si c'est l'utilisateur connecté
                    if (actualUser.id == userMessageOrigine.id) {
                        messagerieContainer.innerHTML += `
                            <div class="messages flex flex-col justify-center p-2 items-end">
                                <input type="hidden" value="${forumId}">
                                <span class="text-blue-600 flex items-center"><span>${forum.user.first_name} ${forum.user.last_name}</span>

                                @if (auth()->user()->role == 0) 
                                    <button class="suppMsg ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                                @endif
                                </span>
                                <p class="w-full text-right break-words">
                                ${message}
                                </p>
                        </div>
                        `;
                    //Si c'est pas lui
                    } else {
                        messagerieContainer.innerHTML += `
                            <div class="messages flex flex-col justify-start p-2 text-left">
                                <input type="hidden" value="${forumId}">
                                <span class="text-blue-600 flex items-center"><span>${forum.user.first_name} ${forum.user.last_name}</span>

                                @if (auth()->user()->role == 0) 
                                    <button class="suppMsg ml-2 text-red-600 border-red-600 border-2 p-1 rounded transition-all duration-300 hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></button>
                                @endif
                                </span>
                                <p>${message}</p>
                            </div>
                        `;
                    }
                }
                ReglageTailleMessagerie();
                AllMessages();
            }
        });

        channel.bind('forum-delete-message', async function (data) {
            let forum = data.forum

            let level_id_actual = parseInt({{ $level_id }});

            let responseResetMsg = await fetch(`/users/resetMsg/${level_id_actual}`);

            const allMsg = Array.from(document.getElementsByClassName('messages'));

            allMsg.forEach((message) => {
                let input = message.querySelector('input');

                if (input.value == forum.id) {
                    message.remove();
                }
            });
        });

        channel.bind('forum-clear', async function (data) {
            let level_id_broadcast = parseInt(data.level_id);

            let level_id_actual = parseInt({{ $level_id }});

            let responseResetMsg = await fetch(`/users/resetMsg/${level_id_actual}`);

            if (level_id_broadcast == level_id_actual) {
                location.reload();
            }
        })

        
    </script>

@endsection