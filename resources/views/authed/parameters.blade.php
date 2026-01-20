@extends('Layouts.authed')

@section('title', 'Paramètres du compte')

@section('content')
<div class="section-animate max-w-4xl mx-auto p-4 md:p-8 space-y-8">
    <!-- Header -->
    <div class="text-center md:text-left">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Paramètres du compte</h1>
        <p class="mt-2 text-gray-500">Gérez vos informations personnelles et la sécurité de votre accès.</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <form method="POST" action="{{ route('parameters') }}" onsubmit="return password_check()" class="divide-y divide-gray-50">
            @csrf
            
            @if (auth()->user()->role == 0)
            <!-- Personal Info Section -->
            <div class="p-8 md:p-12 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Informations Personnelles</h2>
                        <p class="text-xs text-gray-400 font-medium">Vos coordonnées d'identité et de contact.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="prenom" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Prénom</label>
                        <input id="prenom" name="prenom" type="text" value="{{ $user->first_name }}" 
                            class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-2">
                        <label for="nom" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nom</label>
                        <input id="nom" name="nom" type="text" value="{{ $user->last_name }}" 
                            class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Email professionnel</label>
                        <input id="email" name="email" type="email" value="{{ $user->email }}" 
                            class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-2">
                        <label for="phone" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Téléphone</label>
                        <input id="phone" name="phone" type="text" value="{{ $user->phone }}" 
                            class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                    </div>
                </div>
            </div>
            @endif

            <!-- Security Section -->
            <div class="p-8 md:p-12 space-y-8 bg-gray-50/30">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Sécurité</h2>
                        <p class="text-xs text-gray-400 font-medium">Laissez vide pour conserver votre mot de passe actuel.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="mdp" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nouveau mot de passe</label>
                        <div class="relative group">
                            <input id="mdp" name="mdp" type="password" placeholder="••••••••"
                                class="block w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl focus:ring-2 focus:ring-amber-500 transition-all text-sm font-semibold">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-300">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="mdp_confirmation" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Confirmation</label>
                        <div class="relative group">
                            <input id="mdp_confirmation" name="mdp_confirmation" type="password" placeholder="••••••••"
                                class="block w-full px-4 py-3 bg-white border border-gray-100 rounded-2xl focus:ring-2 focus:ring-amber-500 transition-all text-sm font-semibold">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-300">
                                <i class="fa-solid fa-check-double text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-8 md:p-10 bg-gray-50/50 flex flex-col md:flex-row items-center justify-center gap-4">
                <button type="submit" class="w-full md:w-auto px-12 py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-gray-800 transition-all shadow-xl hover:shadow-gray-200 transform hover:-translate-y-1">
                    Enregistrer les modifications
                </button>
                <a href="{{ url()->previous() }}" class="w-full md:w-auto px-10 py-4 bg-white text-gray-500 font-bold rounded-2xl hover:bg-gray-100 text-center transition-all border border-gray-200">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function() {
        const mdp = document.getElementById('mdp');
        const mdp_confirmation = document.getElementById('mdp_confirmation');

        window.password_check = function() {
            if (mdp.value && mdp.value !== mdp_confirmation.value) {
                alert("Les mots de passe ne correspondent pas");
                return false;
            }
            return typeof submitFunction === 'function' ? submitFunction() : true;
        };
    })();
</script>
@endsection