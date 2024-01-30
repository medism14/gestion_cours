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
                bg-green-900 rounded-lg text-white">
                <h1 class="border-b-2 
                pt-5 pb-5
                text-center rounded-lg">Nombre de filière</h1>
                <p class="w-full text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-center pt-4">{{ $nombreFilieres }}</p>
            </div>

            <div class="
                h-[9rem] w-[7rem] text-xs
                sm:h-[10rem] sm:w-[10rem] sm:text-sm 
                md:h-[11rem] md:w-[10rem] md:text-md
                lg:h-[13rem] lg:w-[14rem] lg:text-xl
                bg-yellow-700 rounded-lg text-white">
                <h1 class="border-b-2 
                pt-5 pb-5
                text-center rounded-lg">Nombre de professeur</h1>
                <p class="w-full text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-center pt-4">{{ $nombreProfs }}</p>
            </div>

            <div class="
                h-[9rem] w-[7rem] text-xs
                sm:h-[10rem] sm:w-[10rem] sm:text-sm 
                md:h-[11rem] md:w-[10rem] md:text-md
                lg:h-[13rem] lg:w-[14rem] lg:text-xl
                bg-indigo-900 rounded-lg text-white">
                <h1 class="border-b-2 
                pt-5 pb-5
                text-center rounded-lg">Nombre d'étudiants</h1>
                <p class="w-full text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-center pt-4">{{ $nombreEtudiants }}</p>
            </div>
        </div>
        <div class="block">
            <h1 class="text-center text-3xl mt-12">Interface Administrateur</h1>
            <h1 class="text-center text-md">Le pouvoir est entre vos mains</h1>
        </div>
    @else
        <h1 class="text-center">Ici vous pourrez accéder à vos cours et discutez avec votre filière !</h1>
    @endif
@endsection

@section('scripts')
    
@endsection