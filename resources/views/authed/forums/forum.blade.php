@extends('Layouts.authed')

@section('title', 'Forum de discussion')

@section('content')
<div class="section-animate flex flex-col h-[calc(100vh-100px)] p-4 md:p-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 group">
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('forums.index') }}" class="h-10 w-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-gray-900 transition-all shadow-sm">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-gray-900 leading-tight">Forum {{ $levels->find($level_id)->name ?? '' }}</h1>
                <p class="text-xs text-emerald-500 font-bold uppercase tracking-widest flex items-center">
                    <span class="h-2 w-2 bg-emerald-500 rounded-full mr-2 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    Conversation en direct
                </p>
            </div>

            @if (auth()->user()->role == 0)
                <div class="h-8 w-px bg-gray-200 mx-2 hidden md:block"></div>
                <form method="POST" action="{{ route('forums.suppAllMsg', ['level_id' => $level_id]) }}" onsubmit="return confirm('Vider tout le forum ?')" class="contents">
                    @method('DELETE')
                    @csrf
                    <button class="px-4 py-2 bg-rose-50 text-rose-600 font-bold rounded-xl border border-rose-100/50 hover:bg-rose-600 hover:text-white transition-all shadow-sm flex items-center gap-2 text-sm group/btn">
                        <i class="fa-solid fa-broom group-hover/btn:rotate-12 transition-transform"></i>
                        Vider le forum
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Chat Container -->
    <div class="flex-1 bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
        <!-- Messages Area -->
        <div id="messagerieContainer" class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/30">
            @php $dateAncienMessage = null; @endphp
            @foreach ($forums as $forum)
                @php
                    $dateMessage = $forum->created_at->format('Y-m-d');
                    $isMe = $forum->user_id == auth()->id();
                    $isProf = $forum->user->role == 1;
                @endphp

                @if ($dateAncienMessage != $dateMessage)
                    <div class="flex justify-center my-8">
                        <span class="px-4 py-1 bg-white border border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-widest rounded-full shadow-sm">
                            {{ $forum->created_at->isToday() ? "Aujourd'hui" : ($forum->created_at->isYesterday() ? "Hier" : $forum->created_at->format('d M Y')) }}
                        </span>
                    </div>
                @endif
                @php $dateAncienMessage = $dateMessage; @endphp

                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} group animate-fade-in relative">
                    <div class="max-w-[80%] md:max-w-[60%] space-y-1">
                        <!-- Sender Name -->
                        @if (!$isMe)
                            <p class="text-[10px] font-bold uppercase tracking-wider mb-1 ml-2 {{ $isProf ? 'text-rose-500' : 'text-blue-500' }}">
                                {{ $isProf ? 'Professeur' : 'Étudiant' }} • {{ $forum->user->first_name }} {{ $forum->user->last_name }}
                            </p>
                        @endif

                        <!-- Bubble -->
                        <div class="relative flex items-end space-x-2">
                            <div class="px-4 py-3 rounded-2xl shadow-sm text-sm {{ $isMe 
                                ? 'bg-blue-600 text-white rounded-tr-none' 
                                : 'bg-white border border-gray-100 text-gray-800 rounded-tl-none' }}">
                                {!! nl2br(e($forum->message)) !!}
                                <p class="text-[10px] mt-2 opacity-60 text-right {{ $isMe ? 'text-blue-100' : 'text-gray-400' }}">
                                    {{ $forum->created_at->format('H:i') }}
                                </p>
                            </div>

                            @if (auth()->user()->role == 0)
                                <button class="suppMsg opacity-0 group-hover:opacity-100 transition-opacity p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg" data-id="{{ $forum->id }}">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Input Area -->
        @if (auth()->user()->role != 0)
            <div class="p-4 bg-white border-t border-gray-100">
                <form id="chatForm" method="POST" action="{{ route('forums.addMsgForum', ['level_id' => $level_id]) }}" class="flex items-end space-x-4">
                    @csrf
                    <div class="flex-1 relative">
                        <textarea id="ecritureMessage" name="ecritureMessage" rows="1" maxlength="450" required
                            class="block w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all resize-none placeholder-gray-400"
                            placeholder="Écrivez votre message ici..."></textarea>
                    </div>
                    <button type="submit" class="h-[48px] w-[48px] flex items-center justify-center bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all shadow-md shadow-blue-200 flex-shrink-0 group">
                        <i class="fa-solid fa-paper-plane group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const messagerieContainer = document.getElementById('messagerieContainer');
    const ecritureMessage = document.getElementById('ecritureMessage');
    const chatForm = document.getElementById('chatForm');

    // Scroll to bottom
    const scrollToBottom = () => {
        messagerieContainer.scrollTop = messagerieContainer.scrollHeight;
    };
    scrollToBottom();

    // Auto-expand textarea
    if (ecritureMessage) {
        ecritureMessage.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if (this.scrollHeight > 150) {
                this.style.overflowY = 'scroll';
                this.style.height = '150px';
            } else {
                this.style.overflowY = 'hidden';
            }
        });

        // Submit on Enter (Shift+Enter for new line)
        ecritureMessage.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    // Modal behavior for notifications/reset (mocked from previous script)
    @if (auth()->user()->role == 0)
    function initDeleteButtons() {
        document.querySelectorAll('.suppMsg').forEach(btn => {
            btn.onclick = async () => {
                const id = btn.getAttribute('data-id');
                if (confirm("Supprimer ce message ?")) {
                    await fetch(`/forums/suppForum/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                }
            };
        });
    }
    initDeleteButtons();
    @endif
</script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    var pusher = new Pusher('6979301f0eee4d497b90', { cluster: 'eu' });
    var channel = pusher.subscribe('forum-channel');
    
    channel.bind('forum-new-message', async function(data) {
        if (data.actualiser) { location.reload(); return; }
        
        const forum = data.forum;
        if (parseInt(forum.level.id) !== parseInt({{ $level_id }})) return;

        // Reset notifications for current user via background fetch
        fetch(`/users/resetMsg/{{ $level_id }}`);

        const isMe = forum.user.id === {{ auth()->id() }};
        const isProf = forum.user.role === 1;
        const time = new Date(forum.created_at).toLocaleTimeString([], {hour: '2m', minute:'2m'});
        const role = isProf ? "Professeur" : "Étudiant";
        const roleColor = isProf ? "rose" : "blue";

        const msgHtml = `
            <div class="flex ${isMe ? 'justify-end' : 'justify-start'} group animate-fade-in">
                <div class="max-w-[80%] md:max-w-[60%] space-y-1">
                    ${!isMe ? `<p class="text-[10px] font-bold uppercase tracking-wider mb-1 ml-2 text-${roleColor}-500">${role} • ${forum.user.first_name} ${forum.user.last_name}</p>` : ''}
                    <div class="relative flex items-end space-x-2">
                        <div class="px-4 py-3 rounded-2xl shadow-sm text-sm ${isMe ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white border border-gray-100 text-gray-800 rounded-tl-none'}">
                            ${forum.message.replace(/\n/g, '<br>')}
                            <p class="text-[10px] mt-2 opacity-60 text-right ${isMe ? 'text-blue-100' : 'text-gray-400'}">${time}</p>
                        </div>
                        ${ {{ auth()->user()->role }} === 0 ? `<button class="suppMsg opacity-0 group-hover:opacity-100 transition-opacity p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg" data-id="${forum.id}"><i class="fa-solid fa-trash-can text-xs"></i></button>` : ''}
                    </div>
                </div>
            </div>
        `;

        messagerieContainer.insertAdjacentHTML('beforeend', msgHtml);
        scrollToBottom();
        if ({{ auth()->user()->role }} === 0) initDeleteButtons();
    });

    channel.bind('forum-delete-message', function(data) {
        const id = data.forum.id;
        document.querySelectorAll(`[data-id="${id}"]`).forEach(btn => {
            btn.closest('.animate-fade-in').remove();
        });
    });

    channel.bind('forum-clear', function(data) {
        if (parseInt(data.level_id) === parseInt({{ $level_id }})) location.reload();
    });
</script>
@endsection