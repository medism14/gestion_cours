@extends('Layouts.authed')

@section('title', 'Ressources')

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
        background-color: rgb(195, 200, 213);
    }

    @media screen and (max-width: 768px) {
        th div {
            padding: 3px;
        }

        td div {
            padding: 1px;
        }
    }

</style>

@section('content')
    <h1 class="text-center md:text-3xl lg: font-bold">Gestion de resources</h1>

    <!-- Barre de recherche -->
    <div class="block w-full mx-auto rounded-lg p-2 py-4 flex justify-center flex-col">
        <div class="w-full flex justify-center items-center">
        <form action="{{ route('resources.index') }}" class="p-0 m-0">
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
        @if (auth()->user()->role != 2)  
            <div class="mx-auto w-full max-w-full md:w-[90%] flex justify-end">
                <button id="openModalAdd" class="border-2 text-green-600 border-green-600 transition-all text-[0.7rem] lg:text-sm duration-300 ease-in-out hover:bg-green-600 hover:text-white p-1 rounded-lg font-bold px-4"><i class="fas fa-plus"></i></button>
            </div>
        @endif
        <table id="tableResource" class="mx-auto p-2 w-full md:w-[90%] whitespace-nowrap text-[0.7rem] lg:text-sm">
            <thead>
                <tr>
                    <th class="hidden" id="informations"><div>Informations</div></th>
                    <th id="nomFichier"><div>Nom du fichier</div></th>
                    <th id="section"><div>Section</div></th>
                    <th id="module"><div>Module</div></th>
                    <th id="date"><div>Date</div></th>
                    <th id="actions"><div>Actions</div></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resources as $resource)
                    @php
                        $date = explode(' ', $resource->created_at)[0];
                    @endphp
                    <tr>
                        <td class="informations w-full hidden">
                            <div class="flex justify-center items-start flex-col tdDivs">
                                <span class="font-bold">Nom: {{ $resource->file->filename }}</span>
                                <span>Section: {{ $resource->section }}</span>
                                <span>Prof: {{ $resource->module->user->first_name }} {{ $resource->module->user->last_name }}</span>
                                <span>Module: {{ $resource->module->name }}</span>
                                <span>Date: {{ $date }}</span>
                            </div>
                        </td>
                        <td class="nomFichier"><div class="flex justify-center items-center tdDivs">{{ $resource->file->filename }}</div></td>
                        <td class="section"><div class="flex justify-center items-center tdDivs">{{ $resource->section }}</div></td>
                        <td class="module"><div class="flex justify-center items-center tdDivs">{{ $resource->module->name }}</div></td>
                        <td class="date"><div class="flex justify-center items-center tdDivs">{{ $date }}</div></td>
                        <td class="actions">
                            <div class="flex justify-center items-center tdDivs">
                                <form method="post" action="{{ route('resources.download', ['id' => $resource->id]) }}" class="m-0 p-0">
                                    @csrf
                                    <button type="submit" class="downLoad text-green-600 text-xs p-2 border-2 border-green-600 text-[0.7rem] lg:text-sm rounded-lg ml-3 mr-3 transition-all duration-300 ease-in-out hover:bg-green-600 hover:text-white"><i class="fas fa-cloud-download-alt"></i></button>
                                </form>
                                <button class="openModalView text-blue-600 text-xs p-2 border-2 border-blue-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-blue-600 hover:text-white"><i class="fas fa-search"></i></button>
                                <button class="id hidden">{{ $resource->id }}</button>
                                @if (auth()->user()->role != 2)  
                                    <button class="openModalEdit text-slate-600 text-xs p-2 border-2 border-slate-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-slate-600 hover:text-white"><i class="fas fa-pencil-alt"></i></button>
                                    <form method="POST" onsubmit="return confirm('Vous êtes sur de votre choix ?')" action="{{ route('resources.delete', ['id' => $resource->id]) }}" class="m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button value="{{ $resource->id }}" class="text-red-600 text-xs p-2 border-2 border-red-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-red-600 hover:text-white"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if ($resources->isEmpty())
                    <tr>
                        <td id="changeTaille" colspan="4"><div class="flex justify-center items-center">La table est vide</div></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($loup)
    <div class="w-full flex justify-center mt-3">
        <a href="/resources" class="bg-orange-400 text-white px-2 py-1 rounded-lg">Revenir</a>
    </div>
    @endif

    <!-- Pagination -->
    <div class="w-full max-w-full mt-5 md:w-[90%] mx-auto flex my-3 justify-center text-sm lg:text-base md:text-sm">
        <div class="pagination">
        @if ($resources->hasPages())
            <nav>
                @if ($resources->onFirstPage())
                    <span class="p-2 bg-gray-300 m-2 rounded shadow-md">
                        Precedent
                    </span>
                @else
                    <a href="{{ $resources->previousPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
                        Precedent
                    </a>
                @endif

                @if ($resources->hasMorePages())
                    <a href="{{ $resources->nextPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
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

    @if (auth()->user()->role != 2)  
    <!-- ADD MODALS -->
    <div id="addModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center h-screen overflow-y-auto">
        <!-- Modal -->
        <div id="subAddModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg md:mt-[2rem] pb-[1rem] md:pb-0">
        <form method="POST" action="{{ route('resources.store') }}" enctype="multipart/form-data" class="m-0 p-0">    
            @csrf
            <!-- Close -->
            <div id="closeModalAdd" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Ajout de ressource</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full justify-center md:space-x-2">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addFile">Fichier: </label>
                        <input id="addFile" name="addFile" type="file" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addDescription">Description: </label>
                        <textarea name="addDescription" id="addDescription" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent" cols="5" rows="3"></textarea>
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addSection">Section: </label>
                        <select name="addSection" id="addSection" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="td">TD</option>
                            <option value="tp">TP</option>
                            <option value="cours">COURS</option>
                            <option value="annales">Annales</option>
                        </select>
                    </div>
                </div>

                <!-- Row -->
                <div class="w-full p-2 flex justify-center items-center overflow-hidden">
                    <div class="w-full sm:w-1/2 mx-auto flex justify-center flex-col">
                        <input type="text" id="searchAdd" placeholder="Recherchez ici..." class="rounded focus:ring-2 outline-none px-2 py-1">
                        <label for="addModule" class="text-center">Modules: </label>
                        <select id="addModule" name="addModule" {{ ($modules->isEmpty() ? "disabled" : "") }} type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            @if ($modules->isEmpty()) 
                                <option value="" disabled>Aucune valeur</option>
                            @else        
                                @foreach ($modules as $module)
                                        <option value="{{ $module->id }}">{{ $module->level->sector->name }}: {{ $module->level->name }} ({{ $module->name }})</option>
                                @endforeach
                            @endif
                        </select>
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

    @endif
    <!-- VIEW MODALS -->
    <div id="viewModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center h-screen overflow-y-auto">
        <!-- Modal -->
        <div id="subViewModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg md:mt-[2rem] pb-[1rem] md:pb-0">
            <!-- Close -->
            <div id="closeModalView" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Vue ressource</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewModule">Module: </label>
                        <input id="viewModule" readonly name="viewModule" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewFiliere">Filière: </label>
                        <input id="viewFiliere" readonly name="viewFiliere" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewResource">Nom du ressouce: </label>
                        <input id="viewResource" readonly name="viewResource" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewProf">Professeur: </label>
                        <input id="viewProf" readonly name="viewProf" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewSection">Section: </label>
                        <input id="viewSection" readonly name="viewSection" type="email" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewDate">Date de mise en ligne: </label>
                        <input id="viewDate" readonly name="viewDate" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <div class="md:flex w-full md:space-x-2">
                    <div class="w-none md:w-1/5"></div>
                    <div class="w-full md:w-3/5 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewDescription">Description: </label>
                        <textarea name="viewDescription" id="viewDescription" cols="30" rows="10" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent"></textarea>
                    </div>
                    <div class="w-none md:w-1/5"></div>
                </div>
            </div>
        </div>
    </div>
    @if (auth()->user()->role != 2) 
    <!-- EDIT MODALS -->
    <div id="editModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center h-screen overflow-y-auto">
        <!-- Modal -->
        <div id="subEditModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg md:mt-[2rem] pb-[1rem] md:pb-0">
        <form method="POST" action="{{ route('resources.edit') }}" enctype="multipart/form-data" class="m-0 p-0">    
            @csrf
            <!-- Close -->
            <div id="closeModalEdit" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Modification de ressource</div>
                
            <!-- Corps -->
            <input type="text" class="hidden" id="editId" name="id">
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full justify-center md:space-x-2">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editFile">Fichier: </label>
                        <input id="editFile" name="editFile" type="file" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                        <span>Fichier actuel: <span id="actualFile"></span></span>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editDescription">Description: </label>
                        <textarea name="editDescription" id="editDescription" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent" cols="5" rows="3"></textarea>
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editSection">Section: </label>
                        <select name="editSection" id="editSection" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="td" class="sectionLists">TD</option>
                            <option value="tp" class="sectionLists">TP</option>
                            <option value="cours" class="sectionLists">COURS</option>
                            <option value="annales" class="sectionLists">Annales</option>
                        </select>
                    </div>
                </div>

                <!-- Row -->
                <div class="w-full p-2 flex justify-center items-center overflow-hidden">
                    <div class="w-full sm:w-1/2 mx-auto flex justify-center flex-col">
                        <input type="text" id="searchEdit" placeholder="Recherchez ici..." class="rounded focus:ring-2 outline-none px-2 py-1">
                        <label for="editModule" class="text-center">Modules: </label>
                        <select id="editModule" name="editModule" {{ ($modules->isEmpty() ? "disabled" : "") }} type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            @if ($modules->isEmpty()) 
                                <option value="" disabled>Aucune valeur</option>
                            @else        
                                @foreach ($modules as $module)
                                        <option value="{{ $module->id }}" class="modulesLists">{{ $module->level->sector->name }}: {{ $module->level->name }} ({{ $module->name }})</option>
                                @endforeach
                            @endif
                        </select>
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
    @endif

@endsection

@section('scripts')
<script>



    //MODALS MANIPULATIONS
    @if (auth()->user()->role != 2)  
        //////////////////////////
        //ADD MODALS
        //////////////////////////
        /////////////////////////////////Ouverture et Fermeture du modal
        const closeModalAdd = document.getElementById('closeModalAdd');
        const openModalAdd = document.getElementById('openModalAdd');
        const addModal = document.getElementById('addModal');

        const saveAddButton = document.getElementById('saveAddButton');
        const cancelAddButton = document.getElementById('cancelAddButton');

        const addDescription = document.getElementById('addDescription');
        const addSection = document.getElementById('addSection');

        const addModule = document.getElementById('addModule');

        var searchAdd = document.getElementById('searchAdd');

        //Faire une recherche dynamique
        searchAdd.addEventListener('input', (event) => {
            value = event.target.value;
            remettreNormalAdd();
            if (value != '') {
                rechercherAdd(value);
            } 
        });

        function remettreNormalAdd () {
            const options = Array.from(addModule.options);

            options.forEach((option) => {
                option.disabled = false;
                option.selected = true;
                option.classList.remove('hidden');
            });
        }

        function rechercherAdd (value) {
            const options = Array.from(addModule.options);
            let countDesactivated = 0;

            options.forEach((option) => {
                if (RegExp('^' + value, 'i').test(option.textContent)) {

                } else {
                    countDesactivated++;
                    option.disabled = true;
                    option.classList.add('hidden');
                    option.selected = false;
                }
            });

        }

        function resetValuesAddModal() {
            addModal.classList.add('hidden');

            window.location.reload();

        }
        
        //Fermeture modal en haut à droite
        closeModalAdd.addEventListener('click', () => {
            resetValuesAddModal();
        });

        //Fermeture modal en appuyant sur le bouton annuler
        cancelAddButton.addEventListener('click', () => {
            resetValuesAddModal();
        })

        //Ouverture du modal avec le bouton +
        openModalAdd.addEventListener('click', () => {
            addModal.classList.remove('hidden');
        });

    @endif

        //////////////////////////
        //VIEW MODALS
        //////////////////////////
        const closeModalView = document.getElementById('closeModalView');
        const openModalView = Array.from(document.getElementsByClassName('openModalView'));
        const viewModal = document.getElementById('viewModal');

        const viewModule = document.getElementById('viewModule');
        const viewFiliere = document.getElementById('viewFiliere');
        const viewResource = document.getElementById('viewResource');
        const viewProf = document.getElementById('viewProf');
        const viewSection = document.getElementById('viewSection');
        const viewDate = document.getElementById('viewDate');
        const viewDescription = document.getElementById('viewDescription');

        function resetValuesViewModal() {
            viewModal.classList.add('hidden');
            window.location.reload();
        }
        
        //Fermeture modal en haut à droite
        closeModalView.addEventListener('click', () => {
            resetValuesViewModal();
        });
        

        //Ouverture du modal avec le bouton +
        openModalView.forEach((btn) => {
            btn.addEventListener('click', () => {
                id = parseInt(btn.parentNode.querySelector('.id').textContent);
                fetch('resources/getResource/' + id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network resonse was not ok');
                    }
                    return response.json();
                })
                .then(data => {
    
                    let created_at = data.created_at.split('T')[0];
                    
                    viewModule.value = data.module.name;
                    viewFiliere.value = data.module.level.sector.name + ': ' + data.module.level.name;
                    viewResource.value = data.file.filename;
                    viewSection.value = data.section;
                    viewDate.value = created_at;
                    viewDescription.value = data.description;

                    viewProf.value = data.module.user.first_name + ' ' + data.module.user.last_name;

                    viewModal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error)
                });
            });
        });

    @if (auth()->user()->role != 2)  
        //////////////////////////
        //Edit MODALS
        //////////////////////////
        /////////////////////////////////Ouverture et Fermeture du modal
        const closeModalEdit = document.getElementById('closeModalEdit');
        const openModalEdit = Array.from(document.getElementsByClassName('openModalEdit'));
        const editModal = document.getElementById('editModal');

        const saveEditButton = document.getElementById('saveEditButton');
        const cancelEditButton = document.getElementById('cancelEditButton');

        const editDescription = document.getElementById('editDescription');
        const editSection = document.getElementById('editSection');

        const editModule = document.getElementById('editModule');
        const editId = document.getElementById('editId');
        const actualFile = document.getElementById('actualFile');

        var searchEdit = document.getElementById('searchEdit');

        //Faire une recherche dynamique
        searchEdit.addEventListener('input', (event) => {
            value = event.target.value;
            remettreNormalEdit();
            if (value != '') {
                rechercherEdit(value);
            } 
        });

        function remettreNormalEdit () {
            const options = Array.from(editModule.options);

            options.forEach((option) => {
                option.disabled = false;
                option.selected = true;
                option.classList.remove('hidden');
            });
        }

        function rechercherEdit (value) {
            const options = Array.from(editModule.options);
            let countDesactivated = 0;

            options.forEach((option) => {
                if (RegExp('^' + value, 'i').test(option.textContent)) {

                } else {
                    countDesactivated++;
                    option.disabled = true;
                    option.classList.add('hidden');
                    option.selected = false;
                }
            });

        }

        function resetValuesEditModal() {
            addModal.classList.add('hidden');

            window.location.reload();

        }
        
        //Fermeture modal en haut à droite
        closeModalEdit.addEventListener('click', () => {
            resetValuesEditModal();
        });

        //Fermeture modal en appuyant sur le bouton annuler
        cancelEditButton.addEventListener('click', () => {
            resetValuesEditModal();
        })

        openModalEdit.forEach((btn) => {
            btn.addEventListener('click', () => {
                id = parseInt(btn.parentNode.querySelector('.id').textContent);
                fetch('resources/getResource/' + id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network resonse was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    editId.value = data.id;
                    editDescription.value = data.description;
                    let options_section = Array.from(document.getElementsByClassName('sectionLists'));

                    actualFile.textContent = data.file.filename;

                    options_section.forEach((option) => {
                        if (data.section == option.value) {
                            option.selected = true;
                        }
                    })

                    let options_module = Array.from(document.getElementsByClassName('modulesLists'));

                    options_module.forEach((option) => {
                        if (data.module.id == option.value) {
                            option.selected = true;
                        }
                    });

                    editModal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error)
                });
            });
        });
    @endif


    //////////////////////////
    //Afficher les niveaux
    //////////////////////////
        //LES ID
        const informations = document.getElementById('informations');
        const nomFichier = document.getElementById('nomFichier');
        const section = document.getElementById('section');
        const modules = document.getElementById('module');
        const date = document.getElementById('date');

        //Les Class
        const c_informations = Array.from(document.getElementsByClassName('informations'));
        const c_nomFichier =  Array.from(document.getElementsByClassName('nomFichier'));
        const c_section =  Array.from(document.getElementsByClassName('section'));
        const c_module =  Array.from(document.getElementsByClassName('module'));
        const c_date =  Array.from(document.getElementsByClassName('date'));

        const changeTaille = document.getElementById('changeTaille');
        var height;

    function media_change () {
        // Inférieur à 768px
        if (mediaQuery.matches) {
            //ID
            informations.classList.remove('hidden');

            c_informations.forEach((information) => {
                information.classList.remove('hidden');
                height = information.offsetHeight;
            });
            
            nomFichier.classList.add('hidden');
            section.classList.add('hidden');
            modules.classList.add('hidden');
            date.classList.add('hidden');

            c_nomFichier.forEach((nomFichier) => {
                nomFichier.classList.add('hidden');
            });

            c_section.forEach((section) => {
                section.classList.add('hidden');
            });

            c_module.forEach((m) => {
                m.classList.add('hidden');
            });

            c_date.forEach((date) => {
                date.classList.add('hidden');
            });

            if (changeTaille) {
                changeTaille.setAttribute('colspan', 2);
            }

        // Supérieur à 768px
        } else {
            //ID
            
            informations.classList.add('hidden');

            c_informations.forEach((information) => {
                information.classList.add('hidden');
            });

            nomFichier.classList.remove('hidden');
            section.classList.remove('hidden');
            modules.classList.remove('hidden');
            date.classList.remove('hidden');

            c_nomFichier.forEach((nomFichier) => {
                nomFichier.classList.remove('hidden');
                height = nomFichier.offsetHeight;
            });

            c_section.forEach((section) => {
                section.classList.remove('hidden');
            });

            c_module.forEach((m) => {
                m.classList.remove('hidden');
            });

            c_date.forEach((date) => {
                date.classList.remove('hidden');
            });

            if (changeTaille) {
                changeTaille.setAttribute('colspan', 4);
            }
        }
    }

    mediaQuery.addEventListener('change', (event) => {
        media_change();
    });

    media_change();

        const tableResource = document.getElementById('tableResource');
        const toutLesDiv = tableResource.querySelectorAll('.tdDivs');
        
        toutLesDiv.forEach((div) => {
            div.style.minHeight = height + 'px';
        });
</script>
@endsection