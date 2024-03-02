@extends('Layouts.authed')

@section('title', 'Forums')

<style>
    th div{
        border: 1px solid black;
        padding: 8px;
        background-color: rgb(31, 65, 137);
        color: white;
    }
    
    td div {
        text-align: center;
        padding: 5px;
        height: 40px;
        background-color: rgb(195, 200, 213);
    }

    @media screen and (max-width: 768px) {
        th div {
            padding: 3px;
        }

        td div {
            height: 48px;
            padding: 1px;
        }
    }

</style>

@section('content')
    <h1 class="text-center md:text-3xl lg: font-bold">Gestion de forums</h1>

    <!-- Barre de recherche -->
    <div class="block w-full mx-auto rounded-lg p-2 py-4 flex justify-center flex-col">
        <form action="{{ route('forums.index') }}" class="p-0 m-0">
            @csrf
            <div class="w-full flex justify-center space-x-1 items-center">
                <input id="search" placeholder="Ecrivez ici..." name="search" type="text" class="text-[0.7rem] lg:text-sm  border-1 border-gray-900 bg-gray-300 text-black outline-none p-2 rounded h-[2rem]">
                <i id="tooltipIcon" class="fas fa-question-circle p-1">
                </i>
                <div id="tooltipInfo" class="hidden break-words absolute bg-gray-600 z-1 px-3 md:px-5 py-1 md:py-3 text-white right-5 top-0 rounded-lg text-[0.6rem] md:text-sm">
                    Recherche par:
                    <p class="text-center mt-3">Nom de la filière</p>

                    <p class="text-start mt-5"><span class="underline">Conseil utile:</span> commencez par écrire le mot recherché et le système recherchera toutes les correspondances avec cette entrée.</p>
                </div>
            </div>
            <div class="w-full flex justify-center mt-3">
                <button type="submit" class="text-[0.7rem] lg:text-sm  p-1 border-2 border-blue-600 rounded-lg transition-all duration-300 ease-in-out bg-blue-600 hover:bg-blue-700 text-white">Rechercher</button>
            </div>
        </form> 
    </div>

    <!-- Tableau -->
    <div id="table-div" class="block w-full">
        <table id="tablelevel" class="mx-auto p-2 w-full md:w-[90%] whitespace-nowrap text-[0.7rem] lg:text-sm">
            <thead>
                <tr>
                    <th id="prenom"><div>Filière</div></th>
                    <th id="actions"><div>Actions</div></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($levels as $level)

                    <tr>
                        <td class="nom"><div class="flex justify-center items-center font-bold">{{ $level->sector->name }}: {{ $level->name }}</div></td>
                        <td class="actions">
                            <div class="flex justify-center items-center">
                                <a title="Voir" href="{{ route('forums.forum', ['level_id' => $level->id]) }}" type="submit" class="openModalView text-blue-600 text-xs p-2 border-2 border-blue-600 text-[0.7rem] lg:text-sm rounded-lg ml-3 mr-3 transition-all duration-300 ease-in-out hover:bg-blue-600 hover:text-white"><i class="fas fa-search"></i></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if ($levels->isEmpty())
                    <tr>
                        <td colspan="3"><div class="flex justify-center items-center">La table est vide</div></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($loup)
    <div class="w-full flex justify-center mt-3">
        <a href="/forums" class="bg-orange-400 text-white px-2 py-1 rounded-lg">Revenir</a>
    </div>
    @endif

    <!-- Pagination -->
    <div class="w-full max-w-full mt-5 md:w-[90%] mx-auto flex my-3 justify-center text-sm lg:text-base md:text-sm">
        <div class="pagination">
        @if ($levels->hasPages())
            <nav>
                @if ($levels->onFirstPage())
                    <span class="p-2 bg-gray-300 m-2 rounded shadow-md">
                        Precedent
                    </span>
                @else
                    <a href="{{ $levels->previousPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
                        Precedent
                    </a>
                @endif

                @if ($levels->hasMorePages())
                    <a href="{{ $levels->nextPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
                        Suivant
                    </a>
                @else
                    <span class="p-2 bg-gray-300 m-1 rounded shadow-md">
                        Suivant
                    </span>
                @endif
            </nav>
        @endif
        </div>
    </div>


@endsection

@section('scripts')
<script>

//

//Tooltip manipulation
const tooltipIcon = document.getElementById('tooltipIcon');
            const tooltipInfo = document.getElementById('tooltipInfo');
            const searchBar = document.getElementById('search');

            function positionnementTooltip () {
                let xPosition = tooltipIcon.getBoundingClientRect().x;
                let yPosition = tooltipIcon.getBoundingClientRect().y + tooltipIcon.getBoundingClientRect().height;

                if (mediaQuery.matches) {
                    xPosition = window.innerWidth - tooltipIcon.getBoundingClientRect().x;
                }
                
                tooltipInfo.classList.add(`left-[${xPosition}px]`);
                tooltipInfo.classList.add(`top-[${yPosition}px]`);
            }

            positionnementTooltip();

            tooltipIcon.addEventListener('mouseenter', function () {
                tooltipInfo.classList.remove('hidden');
            }); 

            tooltipIcon.addEventListener('mouseleave', function () {
                tooltipInfo.classList.add('hidden');
            });

            if (mediaQuery.matches) {
                tooltipIcon.addEventListener('click', function () {
                    tooltipInfo.classList.toggle('hidden');
                }); 
            }

            window.addEventListener('resize', () => {
                positionnementTooltip();
            });
            

        //
        
</script>
@endsection