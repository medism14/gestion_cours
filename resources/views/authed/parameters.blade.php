@extends('Layouts.authed')

@section('title', 'Parametrage')

@section('content')
    <section class="flex flex-col">
        <div class="bg-pink-800 mx-auto p-5 w-[80%] md:w-[70%] text-center rounded-b-none rounded-lg text-gray-100">
            Paramétrer ton compte
        </div>
        <div class="p-5 mb-3 bg-gray-600 w-[80%] md:w-[70%] pb-10 mx-auto rounded-t-none rounded-lg">
            <form method="POST" action="{{ route('parameters') }}" class="m-0 p-0" onsubmit="return password_check()">
            @csrf
            @if (auth()->user()->role == 0)
            <!-- Row -->
            <div class="md:flex w-full mb-5">
                <!-- Column -->
                <div class="flex-1 text-center md:mr-5">
                    <label for="prenom" class="text-white">Prenom: </label>
                    <input id="prenom" name="prenom" type="text" value="{{ $user->first_name }}" class="p-2 outline-none rounded-lg text-md w-full shadow-lg mb-4 md:mb-0">
                </div>
                <!-- Column -->
                <div class="flex-1 text-center">
                    <label for="nom" class="text-white">Nom: </label>
                    <input id="nom" name="nom" type="text" value="{{ $user->last_name }}" class="p-2 outline-none rounded-lg text-md w-full shadow-lg">
                </div>
            </div>

            <!-- Row -->
            <div class="md:flex w-full mb-5">
                <!-- Column -->
                <div class="flex-1 text-center md:mr-5">
                    <label for="email" class="text-white">Email: </label>
                    <input id="email" name="email" type="email" value="{{ $user->email }}" class="p-2 outline-none rounded-lg text-md w-full shadow-lg mb-4 md:mb-0">
                </div>
                <!-- Column -->
                <div class="flex-1 text-center">
                    <label for="phone" class="text-white">Phone: </label>
                    <input id="phone" name="phone" type="number" value="{{ $user->phone }}" class="p-2 outline-none rounded-lg text-md w-full shadow-lg">
                </div>
            </div>
            @endif

            <!-- Row -->
            <div class="md:flex w-full mb-10">
                <!-- Column -->
                <div class="flex-1 text-center md:mr-5">
                    <label for="mdp" class="text-white">Mot de passe: </label>
                    <input id="mdp" name="mdp" type="password" class="p-2 outline-none rounded-lg text-md w-full shadow-lg mb-4 md:mb-0">
                </div>
                <!-- Column -->
                <div class="flex-1 text-center">
                    <label for="mdp_confirmation" class="text-white">Confirmation mot de passe: </label>
                    <input id="mdp_confirmation" name="mdp_confirmation" type="password" class="p-2 outline-none rounded-lg text-md w-full shadow-lg">
                </div>
            </div>

            <!-- Row -->
            <div class="md:flex w-full">
                <!-- Column -->
                <div class="flex-1 text-center md:mr-5 md:mb-0 mb-3 ">
                    <button class="bg-green-500 text-white px-4 py-2 rounded-lg transition-all hover:bg-green-600">Confirmer</button>
                </div>
                <!-- Column -->
                <div class="flex-1 text-center">
                    <button class="bg-red-500 text-white px-4 py-2 rounded-lg transition-all hover:bg-red-600">Annuler</button>
                </div>
            </div>
            </form>
        </div>
    </section>
    
@endsection

@section('scripts')
    <script>
        function positionnementTooltip () {
            
        }
        const mdp = document.getElementById('mdp');
        const mdp_confirmation = document.getElementById('mdp_confirmation');

        function password_check() {
            if (mdp.value != mdp_confirmation.value) {
                alert("Les mots de passe ne correspondent pas");
                return false;
            } else {
                return submitFunction();
            }
        }
    </script>
@endsection 