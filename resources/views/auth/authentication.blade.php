@extends('Layouts.auth')

@section('title', 'Authentification')

@section('content')
    <div class="bg-gray-400 mx-auto p-12 pb-8 w-[80%] md:w-[40%] text-center rounded-lg text-gray-700 overflow-x-hidden">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <h1 class="font-bold text-lg md:text-2xl underline mb-5">Authentification</h1>
                <!-- Row -->
                <div class="mb-5 w-full mr-2">
                    <label class="block text-sm font-bold text-gray-700" for="email">Email: </label>
                    <input id="email" class="mt-1 w-full px-4 py-1 rounded-md border-gray-300 shadow-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="email" id="email" type="email">
                </div>
                <!-- Row -->
                <div class="w-full">
                    <label class="block text-sm font-bold text-gray-700" for="password">Password: </label>
                    <div class="flex w-full mt-1">
                        <input id="password" class="flex-1 block px-4 py-1 rounded-md border-gray-300 shadow-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="password" id="password" type="password">
                        <button type="button" id="eye" class="p-2 bg-green-300 rounded-lg hover:bg-green-400"><i class="fas fa-eye"></i></button>
                        </div>
                </div>
            <!-- Row -->
            <div>
                <button class="mt-10 px-5 py-2 text-white rounded-lg bg-blue-500 transition-all duration-300 ease-in-out hover:bg-blue-600 outline-none">Se connecter</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        const password = document.getElementById('password');
        const eye = document.getElementById('eye');

        eye.addEventListener('click', () => {
            if (password.type === 'password') {
                password.type = 'text'; 
            } else {
                password.type = 'password';
            }
        });
    </script>
@endsection