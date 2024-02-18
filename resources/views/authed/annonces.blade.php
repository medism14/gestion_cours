@extends('Layouts.authed')

@section('title', 'Annonces')

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
    <h1 class="text-center md:text-3xl lg: font-bold">Gestion de Annonces</h1>

    <!-- Barre de recherche -->
    <div class="block w-full mx-auto rounded-lg p-2 py-4 flex justify-center flex-col">
        <div class="w-full flex justify-center items-center">
        <form action="{{ route('annonces.index') }}" class="p-0 m-0">
            @csrf
            <input id="search" placeholder="Ecrivez ici..." name="search" type="text" class="text-[0.7rem] lg:text-sm  border-1 border-gray-900 bg-gray-300 text-black outline-none p-2 rounded h-[2rem]">
        </div>
        <div class="w-full flex justify-center mt-3">
            <button type="submit" class="text-[0.7rem] lg:text-sm  p-1 border-2 border-blue-600 rounded-lg transition-all duration-300 ease-in-out bg-blue-600 hover:bg-blue-700 text-white">Rechercher</button>
        </div>
        </form> 
    </div>

    <!-- Tableau -->
    <div id="table-div" class="block w-full">
        <div class="mx-auto w-full max-w-full md:w-[90%] flex justify-end">
            <button id="openModalAdd" class="border-2 text-green-600 border-green-600 transition-all text-[0.7rem] lg:text-sm duration-300 ease-in-out hover:bg-green-600 hover:text-white p-1 rounded-lg font-bold px-4"><i class="fas fa-plus"></i></button>
        </div>
        <table id="tableannonce" class="mx-auto p-2 w-full md:w-[90%] whitespace-nowrap text-[0.7rem] lg:text-sm">
            <thead>
                <tr>
                    <th id="titres"><div>Titres</div></th>
                    <th id="annonceur"><div>Annonceur</div></th>
                    <th id="actions"><div>Actions</div></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($annonces as $annonce)
                    <tr>
                        <td class="nom flex-1"><div class="flex justify-center items-center">{{ $annonce->title }}</div></td>
                        <td class="nom flex-1"><div class="flex justify-center items-center">{{ $annonce->user->role == '0' ? 'Responsable:' : ($annonce->user->role  == 1 ? 'Professeur: ' : '   ' ) }} {{ $annonce->user->first_name }} {{ $annonce->user->last_name }}</div></td>
                        <td class="actions">
                            <div class="flex justify-center items-center">
                                <button class="openModalView text-blue-600 text-xs p-2 border-2 border-blue-600 text-[0.7rem] lg:text-sm rounded-lg ml-3 mr-3 transition-all duration-300 ease-in-out hover:bg-blue-600 hover:text-white"><i class="fas fa-search"></i></button>
                                <button class="id hidden">{{ $annonce->id }}</button>
                                <button class="openModalEdit text-slate-600 text-xs p-2 border-2 border-slate-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-slate-600 hover:text-white"><i class="fas fa-pencil-alt"></i></button>
                                <form method="POST" onsubmit="return confirm('Vous êtes sur de votre choix ?')" action="{{ route('annonces.delete', ['id' => $annonce->id]) }}" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button value="{{ $annonce->id }}" class="text-red-600 text-xs p-2 border-2 border-red-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-red-600 hover:text-white"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if ($annonces->isEmpty())
                    <tr>
                        <td colspan="3"><div class="flex justify-center items-center">La table est vide</div></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($loup)
    <div class="w-full flex justify-center mt-3">
        <a href="/annonces" class="bg-orange-400 text-white px-2 py-1 rounded-lg">Revenir</a>
    </div>
    @endif

    <!-- Pagination -->
    <div class="w-full max-w-full mt-5 md:w-[90%] mx-auto flex my-3 justify-center text-sm lg:text-base md:text-sm">
        <div class="pagination">
        @if ($annonces->hasPages())
            <nav>
                @if ($annonces->onFirstPage())
                    <span class="p-2 bg-gray-300 m-2 rounded shadow-md">
                        Precedent
                    </span>
                @else
                    <a href="{{ $annonces->previousPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
                        Precedent
                    </a>
                @endif

                @if ($annonces->hasMorePages())
                    <a href="{{ $annonces->nextPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
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

    <!-- MODALS -->
    <!-- MODALS -->
    <!-- MODALS -->

    <!-- ADD MODALS -->
    <div id="addModal" class="hidden fixed z-50 inset-0 bg-gray-300 bg-opacity-75 flex justify-center overflow-y-auto">
        <!-- Modal -->
        <div id="subAddModal" class="flex flex-col absolute w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
        <form method="POST" action="{{ route('annonces.store') }}" class="m-0 p-0">    
            @csrf
            <!-- Close -->
            <div id="closeModalAdd" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Ajout d'annonces</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addTitle">Titre: </label>
                        <input id="addTitle" name="addTitle" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addDateExpiration">Date d'expiration: </label>
                        <input id="addDateExpiration" name="addDateExpiration" type="datetime-local" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center items-center">
                    <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <input type="text" id="searchAdd" placeholder="Recherchez ici..." class="px-2 py-1 rounded focus:ring-2 outline-none border-transparent border-blue-300">
                            <label for="addFiliere">Filières: </label>
                            <select name="addFiliere" id="addFiliere" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                                @if (auth()->user()->role == 0)
                                <option value="all" class="addFilieres">Toutes les filières</option>
                                @endif
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" class="addFilieres">{{ $level->sector->name}}: {{$level->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="filiereAdd" class="px-2 py-1 bg-slate-500 text-white rounded-lg transition-all duration-300 ease-in-out hover:bg-slate-600">Ajouter</button>
                        </div>
                    </div>

                    <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <label for="addPersonnes">Personnes: </label>
                            <select name="addPersonnes" id="addPersonnes" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                                @if (auth()->user()->role == 0)
                                <option value="all">Tout le monde</option>
                                <option value="teachers">Professeurs</option>
                                @endif
                                <option value="students" {{ auth()->user()->role != 0 ? 'selected' : '' }}>Etudiants</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center"> <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col space-y-3 items-center overflow-hidden">
                            <label for="listFilieres" class="underline">Listes des filières à qui passer l'annonce: </label>
                            <div id="listFilieres" class="w-full flex flex-col items-center space-y-3 border-none">
                                ---
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center"> <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <label for="addContenu">Contenu: </label>
                            <textarea name="addContenu" id="addContenu" cols="50" rows="20" maxlength="300" class="outline-none rounded focus:ring-2 p-2 border-blue-300"></textarea>
                        </div>
                    </div>
                </div>
            <!-- Footer -->
            <div class="w-full p-5 py-3 flex justify-around items-center">
                <button type="submit" id="saveAddButton" class="p-2 bg-green-600 text-white rounded-lg transition-all duration-300 ease-in-out hover:bg-green-700">Enregistrer</button>
                <button type="button" id="cancelAddButton" class="p-2 bg-red-600 text-white rounded-lg transition-all duration-300 ease-in-out hover:bg-red-700">Annuler</button>
            </div>
            </form>    
            </div>
        </div>
    </div>

    <!-- VIEW MODALS -->
    <div id="viewModal" class="hidden fixed z-50 inset-0 bg-gray-300 bg-opacity-75 flex justify-center overflow-y-auto">
        <!-- Modal -->
        <div id="subViewModal" class="flex flex-col absolute w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
            <!-- Close -->
            <div id="closeModalView" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Vue d'annonces</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewTitle">Titre: </label>
                        <input id="viewTitle" name="viewTitle" readonly type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewAnnonceur">Annonceur: </label>
                        <input id="viewAnnonceur" name="viewAnnonceur" readonly type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>

                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewDateExpiration">Date Expiration: </label>
                        <input id="viewDateExpiration" name="viewDateExpiration" readonly type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center items-center">
                    <div  id="viewFiliereParent" class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <label for="viewFiliere">Filières: </label>
                            <select name="viewFiliere" id="viewFiliere" readonly disabled class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                                @if (auth()->user()->role == 0)
                                    <option value="all" class="viewFilieres">Toutes les filières</option>
                                @endif
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" class="viewFilieres">{{ $level->sector->name}}: {{$level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <label>Personnes: </label>
                            <select readonly disabled class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                                @if (auth()->user()->role == 0)
                                    <option value="all" class="viewPersonnes">Tout le monde</option>
                                    <option value="teachers" class="viewPersonnes">Professeurs</option>
                                @endif
                                <option value="students" class="viewPersonnes" {{ auth()->user()->role != 0 ? 'selected' : '' }}>Etudiants</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center"> <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div id="listFilieresViewParent" class="flex-1 w-full p-2 flex justify-center flex-col space-y-3 items-center overflow-hidden">
                            <label for="listFilieresView" class="underline">Listes des filières à qui passer l'annonce: </label>
                            <div id="listFilieresView" class="w-full flex flex-col items-center space-y-3 border-none">
                                
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center"> <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <label for="viewContenu" class="underline">Contenu: </label>
                            <p id="viewContenu" class="break-words text-center"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT MODALS -->
    <div id="editModal" class="hidden fixed z-50 inset-0 bg-gray-300 bg-opacity-75 flex justify-center overflow-y-auto">
        <!-- Modal -->
        <div id="subEditModal" class="flex flex-col absolute w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
        <form method="POST" action="{{ route('annonces.edit') }}" class="m-0 p-0">    
            @csrf
            <!-- Close -->
            <input type="text" class="hidden" id="editId" name="id">
            <div id="closeModalEdit" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Modification d'annonces</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editTitle">Titre: </label>
                        <input id="editTitle" name="editTitle" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editDateExpiration">Date d'expiration: </label>
                        <input id="editDateExpiration" name="editDateExpiration" type="datetime-local" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center items-center">
                    <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <input type="text" id="searchEdit" placeholder="Recherchez ici..." class="px-2 py-1 rounded focus:ring-2 outline-none border-transparent border-blue-300">
                            <label for="editFiliere">Filières: </label>
                            <select name="editFiliere" id="editFiliere" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                                @if (auth()->user()->role == 0)
                                <option value="all" class="editFilieres">Toutes les filières</option>
                                @endif
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" class="editFilieres">{{ $level->sector->name}}: {{$level->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="filiereEdit" class="px-2 py-1 bg-slate-500 text-white rounded-lg transition-all duration-300 ease-in-out hover:bg-slate-600">Ajouter</button>
                        </div>
                    </div>

                    <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <label for="editPersonnes">Personnes: </label>
                            <select name="editPersonnes" id="editPersonnes" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                                @if (auth()->user()->role == 0)
                                <option value="all">Tout le monde</option>
                                <option value="teachers">Professeurs</option>
                                @endif
                                <option value="students" {{ auth()->user()->role != 0 ? 'selected' : '' }}>Etudiants</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center"> <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col space-y-3 items-center overflow-hidden">
                            <label for="listFilieresEdit" class="underline">Listes des filières à qui passer l'annonce: </label>
                            <div id="listFilieresEdit" class="w-full flex flex-col items-center space-y-3 border-none">
                                ---
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center"> <div class="flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <label for="editContenu">Contenu: </label>
                            <textarea name="editContenu" id="editContenu" cols="50" rows="20" maxlength="300" class="outline-none rounded focus:ring-2 p-2 border-blue-300"></textarea>
                        </div>
                    </div>
                </div>
            <!-- Footer -->
            <div class="w-full p-5 py-3 flex justify-around items-center">
                <button type="submit" id="saveEditButton" class="p-2 bg-green-600 text-white rounded-lg transition-all duration-300 ease-in-out hover:bg-green-700">Enregistrer</button>
                <button type="button" id="cancelEditButton" class="p-2 bg-red-600 text-white rounded-lg transition-all duration-300 ease-in-out hover:bg-red-700">Annuler</button>
            </div>
            </form>    
            </div>
        </div>
    </div>


@endsection

@section('scripts')
<script>



    //MODALS MANIPULATIONS

    //////////////////////////
    //ADD MODALS
    //////////////////////////
    /////////////////////////////////Ouverture et Fermeture du modal
    const closeModalAdd = document.getElementById('closeModalAdd');
    const openModalAdd = document.getElementById('openModalAdd');
    const addModal = document.getElementById('addModal');

    const saveAddButton = document.getElementById('saveAddButton');
    const cancelAddButton = document.getElementById('cancelAddButton');

    const addFiliere = document.getElementById('addFiliere');
    const addPersonnes = document.getElementById('addPersonnes');
    const addContenu = document.getElementById('addContenu');

    const searchAdd = document.getElementById("searchAdd");
    const filiereAdd = document.getElementById('filiereAdd');
    const listFilieres = document.getElementById('listFilieres');

    const optionsParDefaut = Array.from(document.getElementsByClassName('addFilieres'));

    let addList = false;

    //Verification de l'option selectionné
    function VerifOptionSelected () {
        if (addFiliere.options[addFiliere.selectedIndex].textContent == 'Toutes les filières') {
            filiereAdd.classList.add('hidden');
        } else {
            filiereAdd.classList.remove('hidden');
        }
        
    }
    
    VerifOptionSelected();

    //Partie recherche filière
    searchAdd.addEventListener('input', (event) => {
        let options = Array.from(document.getElementsByClassName('addFilieres'));
        if (!searchAdd.value) {
            let selected = false;
            options.forEach((option) => {
                option.classList.remove('hidden');
                option.disabled = false;

                if (!selected) {
                    option.selected = true;
                    selected = true;
                    if (options[0].value == 'all') { filiereAdd.classList.add('hidden'); } else { filiereAdd.classList.remove('hidden'); }
                }
            });
        } else {
            search(searchAdd.value, options);
        }
    });

    //Pour effectuer la recherche
    function search(value, options) {
        let selected = false;
        options.forEach((option) => {
            if (RegExp('^' + value, 'i').test(option.textContent)) {
                option.classList.remove('hidden');
                option.disabled = false;
                
                if (!selected) {
                    selected = true;
                    option.selected = true;
                }
            } else {
                option.classList.add('hidden');
                option.disabled = true;
            }
        });

        if (!selected) {
            filiereAdd.classList.add('hidden');
            addFiliere.innerHTML += '';
        } else {
            VerifOptionSelected();
        }
    }

    //Changement de filière
    addFiliere.addEventListener('change', () => {
        VerifOptionSelected();
    });

    //Pour l'ajout de filière
    filiereAdd.addEventListener('click', () => {
        let options = Array.from(document.getElementsByClassName('addFilieres'));

        if (options.length == 0) {
            alert('Il n\'y a plus de filière restante');
            return
        }

        if (options[0].textContent == 'Toutes les filières') { options[0].remove() }

        let option = addFiliere.options[addFiliere.selectedIndex];
        let optionNext = addFiliere.options[addFiliere.selectedIndex + 1];
        let optionBefore = addFiliere.options[addFiliere.selectedIndex - 1];
        
        if (!addList) {
            listFilieres.innerHTML = '';
            addList = true;
        }

        listFilieres.innerHTML += `
            <div class="flex w-full">
                <input value="${option.value}" class="hidden" name="addFilieres${option.value}">
                <p class="w-1/4"></p>
                <p class="w-2/4 flex content justify-center items-center">${option.textContent}</p>
                <p class="w-1/4 flex justify-start items-center text-red-600"><i class="fas fa-times text-lg cursor-pointer cancelAdd" title="Retirer la filière"></i></p>
            </div>
        `;
        option.remove();
    });

    //Pour la suppression de filière
    const cancelAdd = Array.from(document.getElementsByClassName('cancelAdd'));

    listFilieres.addEventListener('click', (event) => {
        let item = event.target;

        if (item.classList.contains('cancelAdd')) {
            let id = item.parentNode.parentNode.querySelector('input').value;
            let div = item.parentNode.parentNode;
            let value = item.parentNode.parentNode.querySelector('.content').textContent;

            div.remove();

            addFiliere.innerHTML += `
                <option value="${id}">${value}</option>
            `;  

            if (listFilieres.childElementCount == 0) {
                addFiliere.innerHTML = '';

                optionsParDefaut.forEach((option) => {
                    addFiliere.innerHTML += `<option value="${option.value}" class="addFilieres">${option.textContent}</option>`;
                });

                VerifOptionSelected();
                addList = false;
                listFilieres.innerHTML = '---';
            }
        }
    })

    function resetValuesAddModal() {
        window.location.reload();
    }

    //Fermeture modal en haut à droite
    closeModalAdd.addEventListener('click', () => {
        resetValuesAddModal();
    });

    //Fermeture modal en appuyant sur le bouton annuler
    cancelAddButton.addEventListener('click', () => {
        resetValuesAddModal();
    });

    //Ouverture du modal avec le bouton +
    openModalAdd.addEventListener('click', () => {
        addModal.classList.remove('hidden');
    });

    //////////////////////////
    //VIEW MODALS
    //////////////////////////
    const closeModalView = document.getElementById('closeModalView');
    const openModalView = Array.from(document.getElementsByClassName('openModalView'));
    const viewModal = document.getElementById('viewModal');

    const viewFiliere = document.getElementById('viewFiliere');
    const viewFiliereParent = document.getElementById('viewFiliereParent');
    const viewFilieres = Array.from(document.getElementsByClassName('viewFilieres'));

    const viewPersonnes = Array.from(document.getElementsByClassName('viewPersonnes'));
    const viewTitle = document.getElementById('viewTitle');
    const viewContenu = document.getElementById('viewContenu');
    const viewAnnonceur = document.getElementById('viewAnnonceur');
    const viewDateExpiration = document.getElementById('viewDateExpiration');

    const listFilieresViewParent = document.getElementById('listFilieresViewParent');
    const listFilieresView = document.getElementById('listFilieresView');

    //Fermeture modal en haut à droite
    closeModalView.addEventListener('click', () => {
        resetValuesAddModal();
    });

    //Ouverture du modal view
    openModalView.forEach((btn) => {
        btn.addEventListener('click', async (event) => {
            let id = parseInt(btn.parentNode.querySelector('.id').textContent);

            let response = await fetch(`/annonces/getAnnonceRelation/${id}`);

            let data = await response.json();

            viewTitle.value = data.annonce.title;

            let lastname;
            let role;
            
            if (data.annonce.user.last_name == null) {
                last_name = '';
            } else {
                last_name = data.annonce.user.last_name;
            }

            if (data.annonce.user.role == 0) {
                role = 'Responsable: ';
            } else {
                role = 'Professeur: ';
            }

            viewAnnonceur.value = role + '' + data.annonce.user.first_name + ' ' + last_name;
            viewDateExpiration.value = data.annonce.date_expiration;

            const annonce = data.annonce;
            const filieres = data.filieres;

            //Pour les Filieres
            if (annonce.choix_filieres != 'all') {
                listFilieresViewParent.classList.remove('hidden');
                viewFiliereParent.classList.add('hidden');

                for (filiere of filieres) {
                    let name = filiere.sector.name + ': ' + filiere.name;
                    listFilieresView.innerHTML += `
                        <div class="flex">${name}</div>
                    `;
                }
                
            } else {
                listFilieresViewParent.classList.add('hidden');
                viewFiliereParent.classList.remove('hidden');
            }

            //Pour les personnes visés
            if (annonce.choix_personnes != 'all') {
                viewPersonnes.forEach((personne) => {
                    if (personne.value == annonce.choix_personnes) {
                        personne.selected = true;
                    }
                });
            }

            viewContenu.innerHTML = annonce.content;
            
            viewModal.classList.remove('hidden');
        });
    });

    //////////////////////////
    //EDIT MODALS
    //////////////////////////
    const closeModalEdit = document.getElementById('closeModalEdit');
    const openModalEdit = Array.from(document.getElementsByClassName('openModalEdit'));
    const editModal = document.getElementById('editModal');

    const saveEditButton = document.getElementById('saveEditButton');
    const cancelEditButton = document.getElementById('cancelEditButton');

    const editTitle = document.getElementById('editTitle');
    const editDateExpiration = document.getElementById('editDateExpiration');
    const editFiliere = document.getElementById('editFiliere');
    const editPersonnes = document.getElementById('editPersonnes');
    const editContenu = document.getElementById('editContenu');

    const editId = document.getElementById('editId');

    const searchEdit = document.getElementById("searchEdit");
    const filiereEdit = document.getElementById('filiereEdit');
    const listFilieresEdit = document.getElementById('listFilieresEdit');

    let editList = false;

    const optionsParDefautEdit = Array.from(document.getElementsByClassName('editFilieres'));

    //Verification de l'option selectionné
    function VerifOptionSelectedEdit () {
        if (editFiliere.options[editFiliere.selectedIndex].textContent == 'Toutes les filières') {
            filiereEdit.classList.add('hidden');
        } else {
            filiereEdit.classList.remove('hidden');
        }
        
    }
    
    //Partie recherche filière
    searchEdit.addEventListener('input', (event) => {
        let options = Array.from(document.getElementsByClassName('editFilieres'));
        if (!searchEdit.value) {
            let selected = false;
            options.forEach((option) => {
                option.classList.remove('hidden');
                option.disabled = false;

                if (!selected) {
                    option.selected = true;
                    selected = true;

                    if (options[0].value == 'all') { filiereEdit.classList.add('hidden'); } else { filiereEdit.classList.remove('hidden'); }
                }
            });
        } else {
            searchForEdit(searchEdit.value, options);
        }
    });

    //Pour effectuer la recherche
    function searchForEdit(value, options) {
        let selected = false;
        options.forEach((option) => {
            if (RegExp('^' + value, 'i').test(option.textContent)) {
                option.classList.remove('hidden');
                option.disabled = false;
                
                if (!selected) {
                    selected = true;
                    option.selected = true;
                }
            } else {
                option.classList.add('hidden');
                option.disabled = true;
            }
        });

        if (!selected) {
            filiereEdit.classList.add('hidden');
            editFiliere.innerHTML += '';
        } else {
            VerifOptionSelectedEdit();
        }
    }

    //Changement de filière
    editFiliere.addEventListener('change', () => {
        VerifOptionSelectedEdit();
    });

    //Pour l'ajout de filière
    filiereEdit.addEventListener('click', () => {
        let options = Array.from(document.getElementsByClassName('editFilieres'));

        if (options.length == 0) {
            alert('Il n\'y a plus de filière restante');
            return
        }

        if (options[0].textContent == 'Toutes les filières') { options[0].remove() }

        let option = editFiliere.options[editFiliere.selectedIndex];
        let optionNext = editFiliere.options[editFiliere.selectedIndex + 1];
        let optionBefore = editFiliere.options[editFiliere.selectedIndex - 1];
        
        if (!editList) {
            listFilieresEdit.innerHTML = '';
            editList = true;
        }

        listFilieresEdit.innerHTML += `
            <div class="flex w-full">
                <input value="${option.value}" class="hidden" name="editFilieres${option.value}">
                <p class="w-1/4"></p>
                <p class="w-2/4 flex content justify-center items-center">${option.textContent}</p>
                <p class="w-1/4 flex justify-start items-center text-red-600"><i class="fas fa-times text-lg cursor-pointer cancelEdit" title="Retirer la filière"></i></p>
            </div>
        `;
        option.remove();
    });

    //Pour la suppression de filière
    const cancelEdit = Array.from(document.getElementsByClassName('cancelEdit'));
    listFilieresEdit.addEventListener('click', (event) => {
        let item = event.target;

        if (item.classList.contains('cancelEdit')) {
            let id = item.parentNode.parentNode.querySelector('input').value;
            let div = item.parentNode.parentNode;
            let value = item.parentNode.parentNode.querySelector('.content').textContent;

            div.remove();

            editFiliere.innerHTML += `
                <option value="${id}">${value}</option>
            `;  

            if (listFilieresEdit.childElementCount == 0) {
                editFiliere.innerHTML = '';

                optionsParDefaut.forEach((option) => {
                    editFiliere.innerHTML += `<option value="${option.value}" class="editFilieres">${option.textContent}</option>`;
                });

                VerifOptionSelectedEdit();
                editList = false;
                listFilieres.innerHTML = '---';
            }
        }
    })

    openModalEdit.forEach((btn) => {
        btn.addEventListener('click', async () => {
            let id = parseInt(btn.parentNode.querySelector('.id').textContent);

            let response = await fetch(`/annonces/getAnnonceRelation/${id}`);

            let data = await response.json();

            let options = Array.from(document.getElementsByClassName('editFilieres'));
            
            //Remplissage des input
            editTitle.value = data.annonce.title;
            editDateExpiration.value = data.annonce.date_expiration;
            editContenu.value = data.annonce.content;

            //Remplissage pour la selection des personnes;
            let optionsPeronnes = editPersonnes.querySelectorAll('option');
            
            optionsPeronnes.forEach((option) => {
                if (option.value == data.annonce.choix_personnes) {
                    option.selected = true;
                }
            });

            let listF = [];

            //Choix filière si partiels
            if (data.annonce.choix_filieres != 'all') {
                editList = true;
                
                if (options[0].value == 'all') { options[0].remove() }

                options.forEach((option) => {
                    data.filieres.forEach((filiere) => {
                        if (option.value == filiere.id) {
                            listF.push(option);
                            option.remove();
                        }
                    });
                });
                listFilieresEdit.innerHTML = '';

                listF.forEach((list) => {
                    listFilieresEdit.innerHTML += `
                    <div class="flex w-full">
                        <input value="${list.value}" class="hidden" name="editFilieres${list.value}">
                        <p class="w-1/4"></p>
                        <p class="w-2/4 flex content justify-center items-center">${list.textContent}</p>
                        <p class="w-1/4 flex justify-start items-center text-red-600"><i class="fas fa-times text-lg cursor-pointer cancelEdit" title="Retirer la filière"></i></p>
                    </div>
                    `;
                })
            }
            VerifOptionSelectedEdit();

            editId.value = id;

            editModal.classList.remove('hidden');
        });
    });

    //Fermeture modal en haut à droite
    closeModalEdit.addEventListener('click', () => {
        resetValuesAddModal();
    });

    //Fermeture modal en haut à droite
    cancelEditButton.addEventListener('click', () => {
        resetValuesAddModal();
    });

</script>
@endsection