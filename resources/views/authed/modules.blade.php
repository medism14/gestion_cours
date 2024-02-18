@extends('Layouts.authed')

@section('title', 'Modules')

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
    <h1 class="text-center md:text-3xl lg: font-bold">Gestion de Modules</h1>

    <!-- Barre de recherche -->
    <div class="block w-full mx-auto rounded-lg p-2 py-4 flex justify-center flex-col">
        <div class="w-full flex justify-center items-center">
        <form action="{{ route('modules.index') }}" class="p-0 m-0">
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
        <table id="tableModule" class="mx-auto p-2 w-full md:w-[90%] whitespace-nowrap text-[0.7rem] lg:text-sm">
            <thead>
                <tr>
                    <th class="hidden" id="informations"><div>Informations</div></th>
                    <th id="nom"><div>Nom</div></th>
                    <th id="professeur"><div>Professeur</div></th>
                    <th id="filiere"><div>Filière</div></th>
                    <th id="actions"><div>Actions</div></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($modules as $module)
                    <tr>
                        <td class="informations">
                            <div class="flex justify-center items-start flex-col tdDivs">
                                <span class="font-bold">Module: {{ $module->name }}</span>
                                <span>Prof :{{ $module->user->first_name }} {{ $module->user->last_name }}</span>
                                <span>Filière: {{ $module->level->sector->name }}: {{ $module->level->name }}</span>
                            </div>
                        </td>
                        <td class="nom"><div class="flex justify-center items-center tdDivs">{{ $module->name }}</div></td>
                        <td class="professeur"><div class="flex justify-center items-center tdDivs">{{ $module->user->first_name }} {{ $module->user->last_name }}</div></td>
                        <td class="filiere"><div class="flex justify-center items-center tdDivs">{{ $module->level->sector->name }}: {{ $module->level->name }}</div></td>
                        <td class="actions">
                            <div class="flex justify-center items-center tdDivs">
                                <button class="openModalView text-blue-600 text-xs p-2 border-2 border-blue-600 text-[0.7rem] lg:text-sm rounded-lg ml-3 mr-3 transition-all duration-300 ease-in-out hover:bg-blue-600 hover:text-white"><i class="fas fa-search"></i></button>
                                <button class="id hidden">{{ $module->id }}</button>
                                <button class="openModalEdit text-slate-600 text-xs p-2 border-2 border-slate-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-slate-600 hover:text-white"><i class="fas fa-pencil-alt"></i></button>
                                <form method="POST" onsubmit="return confirm('Vous êtes sur de votre choix ?')" action="{{ route('modules.delete', ['id' => $module->id]) }}" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button value="{{ $module->id }}" class="text-red-600 text-xs p-2 border-2 border-red-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-red-600 hover:text-white"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if ($modules->isEmpty())
                    <tr>
                        <td colspan="4" id="changeTaille"><div class="flex justify-center items-center">La table est vide</div></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($loup)
    <div class="w-full flex justify-center mt-3">
        <a href="/modules" class="bg-orange-400 text-white px-2 py-1 rounded-lg">Revenir</a>
    </div>
    @endif

    <!-- Pagination -->
    <div class="w-full max-w-full mt-5 md:w-[90%] mx-auto flex my-3 justify-center text-sm lg:text-base md:text-sm">
        <div class="pagination">
        @if ($modules->hasPages())
            <nav>
                @if ($modules->onFirstPage())
                    <span class="p-2 bg-gray-300 m-2 rounded shadow-md">
                        Precedent
                    </span>
                @else
                    <a href="{{ $modules->previousPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
                        Precedent
                    </a>
                @endif

                @if ($modules->hasMorePages())
                    <a href="{{ $modules->nextPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
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
    <div id="addModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center overflow-y-auto">
        <!-- Modal -->
        <div id="subAddModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
        <form method="POST" action="{{ route('modules.store') }}" class="m-0 p-0" onsubmit="return validProf()">    
            @csrf
            <!-- Close -->
            <div id="closeModalAdd" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Ajout d'un module</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addName">Nom: </label>
                        <input id="addName" name="addName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>
                
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:w-1/2 mx-auto p-2 flex justify-center flex-col items-center overflow-hidden">
                        <input type="text" id="searchAdd" placeholder="Recherchez ici..." class="w-[75%] rounded focus:ring-2 outline-none px-2 py-1">
                        <label for="addFiliere">Filière: </label>
                        <select id="addFiliere" name="addFiliere" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            @foreach ($sectors as $sector)
                                @foreach ($sector->levels as $level)
                                    <option value="{{ $level->id }}">{{$sector->name}}: {{ $level->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:w-1/2 mx-auto p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addProf">Professeur: </label>
                        <select id="addProf" name="addProf" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            
                        </select>
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
    <div id="viewModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center overflow-y-auto">
        <!-- Modal -->
        <div id="subViewModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
        <form method="POST" action="{{ route('modules.store') }}" class="m-0 p-0">    
            @csrf
            <!-- Close -->
            <div id="closeModalView" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Vue d'un module</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewName">Nom: </label>
                        <input id="viewName" name="viewName" readonly type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>
                
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:w-1/2 mx-auto p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewFiliere">Filière: </label>
                        <span id="viewFiliere"></span>
                    </div>
                </div>
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:w-1/2 mx-auto p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewProf">Professeur: </label>
                        <span id="viewProf"></span>
                    </div>
                </div>
            </form>    
            </div>
        </div>
    </div>

    <!-- Edit MODALS -->
    <div id="editModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center overflow-y-auto">
        <!-- Modal -->
        <div id="subEditModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
        <form method="POST" action="{{ route('modules.edit') }}" class="m-0 p-0" onsubmit="return validProfEdit()">    
            @csrf
            <!-- Close -->
            <div id="closeModalEdit" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Modification d'un module</div>
                
            <!-- Corps -->
            <input type="text" class="hidden" id="editId" name="id">
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editName">Nom: </label>
                        <input id="editName" name="editName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>
                
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:w-1/2 mx-auto p-2 flex justify-center flex-col items-center overflow-hidden">
                        <input type="text" id="searchEdit" placeholder="Recherchez ici..." 
                        class="rounded focus:ring-2 outline-none px-2 py-1">
                        <label for="editFiliere">Filière: </label>
                        <select id="editFiliere" name="editFiliere" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            @foreach ($sectors as $sector)
                                @foreach ($sector->levels as $level)
                                    <option value="{{ $level->id }}" class="editOptions">{{$sector->name}}: {{ $level->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:w-1/2 mx-auto p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editProf">Professeur: </label>
                        <select id="editProf" name="editProf" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            
                        </select>
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

    const addName = document.getElementById('addName');
    const addFiliere = document.getElementById('addFiliere');
    var addProf = document.getElementById('addProf');
    const addListBtn = document.getElementById('addListBtn');

    var j = 1;


    function resetValuesAddModal() {
        addModal.classList.add('hidden');

        addName.value = '';

        const removeElement = Array.from(document.getElementsByClassName('removeElement'));
        
        removeElement.forEach((element, index) => {
            let row = element.parentNode.parentNode.parentNode;

            row.remove();
        })

        j = 1;

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
        profParDefaut();
    });

    var searchAdd = document.getElementById('searchAdd');

    //Prof par defaut add
        async function profParDefaut () {
            id = addFiliere.value;  
            fetch(`modules/getProfFiliere/${id}`)
            .then(response => response.json())
            .then(data => {
                addProf.innerHTML = '';
                data.forEach((user) => {
                    addProf.innerHTML += `
                        <option value="${user.id}">${user.first_name} ${user.last_name}</option>
                    `;
                })
            }) 
            .catch(error => {
                console.error(error);
            })
        }
    //

    //Faire une recherche dynamique
    searchAdd.addEventListener('input', async (event) => {
        value = event.target.value;
        remettreNormalAdd();
        if (value != '') {
            addProf.innerHTML = '';
            await rechercherAdd(value);
        } 
    });

    function remettreNormalAdd () {
        const options = Array.from(addFiliere.options);

        options.forEach((option) => {
            option.disabled = false;
            option.selected = true;
            option.classList.remove('hidden');
        });
    }
    async function rechercherAdd (value) {
        const options = Array.from(addFiliere.options);

        let inc = 0;
        options.forEach((option) => {
            if (RegExp('^' + value, 'i').test(option.textContent)) {
                if (inc == 0) {
                    options.forEach ((subOption) => {
                        if (subOption.value != option.value) {
                            subOption.selected = false;
                        }
                    })
                    option.selected = true;
                }
                inc++;
            } else {
                option.disabled = true;
                option.classList.add('hidden');
                option.selected = false;
            }
        });
    }

    addFiliere.addEventListener('change', () => {
        id = addFiliere.value;  
        fetch(`modules/getProfFiliere/${id}`)
        .then(response => response.json())
        .then(data => {
            addProf.innerHTML = '';

            data.forEach((user) => {
                addProf.innerHTML += `
                    <option value="${user.id}">${user.first_name} ${user.last_name}</option>
                `;
            })
        }) 
        .catch(error => {
            console.error(error);
        })
    })

    function validProf () {
        if (addProf.value == '') {
            alert('Vous devez saisir un professeur');
            return false;
        }
    }
    

    //////////////////////////
    //VIEW MODALS
    //////////////////////////
    const closeModalView = document.getElementById('closeModalView');
    const openModalView = Array.from(document.getElementsByClassName('openModalView'));
    const viewModal = document.getElementById('viewModal');

    const saveViewButton = document.getElementById('saveViewButton');
    const cancelViewButton = document.getElementById('cancelViewButton');

    const viewName = document.getElementById('viewName');
    const viewFiliere = document.getElementById('viewFiliere');

    function resetValuesViewModal() {
        viewModal.classList.add('hidden');

        viewName.value = '';

        const removeElementView = Array.from(document.getElementsByClassName('removeElementView'));
        
        removeElementView.forEach((element, index) => {
            let row = element;
            row.remove();
        })

        j = 1;
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
            fetch('/modules/getModule/' + id)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                viewName.value = data.name;
                viewFiliere.textContent = data.level.name + ': ' + data.level.sector.name;
                viewProf.textContent = data.user.first_name + ' ' + data.user.last_name;

                viewModal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error)
            });
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

    const editName = document.getElementById('editName');
    const editFiliere = document.getElementById('editFiliere');
    const editListBtn = document.getElementById('editListBtn');

    function resetValuesEditModal() {
        editModal.classList.add('hidden');

        editName.value = '';

        const removeElement = Array.from(document.getElementsByClassName('removeElement'));
        
        removeElement.forEach((element, index) => {
            let row = element.parentNode.parentNode.parentNode;

            row.remove();
        })

        j = 1;

        window.location.reload();

    }

    //Fermeture modal en haut à droite
    closeModalEdit.addEventListener('click', () => {
        resetValuesAddModal();
    });

    //Fermeture modal en appuyant sur le bouton annuler
    cancelEditButton.addEventListener('click', () => {
        resetValuesEditModal();
    });

    //Ouverture du modal avec le bouton +
    openModalEdit.forEach((btn) => {
            btn.addEventListener('click', () => {
                id = parseInt(btn.parentNode.querySelector('.id').textContent);
                
                fetch('modules/getModule/' + id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let name = data.name;
                    let level_id = data.level.id;
                    
                    let editOptions = Array.from(document.getElementsByClassName('editOptions'));

                    editOptions.forEach((option) => {
                        if (option.value == level_id) {
                            option.setAttribute('selected', 'true');
                        }
                    });

                    editId = document.getElementById('editId');
                    editId.value = data.id;
                    editName.value = name;

                    editModal.classList.remove('hidden');
                    profParDefautEdit();
            })
            .catch(error => {
                console.error('Error:', error)
            });
        });
    });

    var searchEdit = document.getElementById('searchEdit');

    //Prof par defaut add
        async function profParDefautEdit () {
            id = editFiliere.value; 
            console.log(id); 
            fetch(`modules/getProfFiliere/${id}`)
            .then(response => response.json())
            .then(data => {
                editProf.innerHTML = '';
                data.forEach((user) => {
                    editProf.innerHTML += `
                        <option value="${user.id}">${user.first_name} ${user.last_name}</option>
                    `;
                })
            }) 
            .catch(error => {
                console.error(error);
            })
        }
    //

    //Faire une recherche dynamique
    searchEdit.addEventListener('input', async (event) => {
        value = event.target.value;
        remettreNormalEdit();
        if (value != '') {
            editProf.innerHTML = '';
            await rechercherEdit(value);
        } 
    });

    function remettreNormalEdit () {
        const options = Array.from(editFiliere.options);

        options.forEach((option) => {
            option.disabled = false;
            option.selected = true;
            option.classList.remove('hidden');
        });
    }

    async function rechercherEdit (value) {
        const options = Array.from(editFiliere.options);
        let countDesactivated = 0;

        let inc = 0;
        options.forEach((option) => {
            if (RegExp('^' + value, 'i').test(option.textContent)) {
                if (inc == 0) {
                    options.forEach ((subOption) => {
                        if (subOption.value != option.value) {
                            subOption.selected = false;
                        }
                    })
                    option.selected = true;
                }
                inc++;
            } else {
                countDesactivated++;
                option.disabled = true;
                option.classList.add('hidden');
                option.selected = false;
            }
        });
    }

    editFiliere.addEventListener('change', () => {
        id = editFiliere.value;  
        fetch(`modules/getProfFiliere/${id}`)
        .then(response => response.json())
        .then(data => {
            editProf.innerHTML = '';

            data.forEach((user) => {
                editProf.innerHTML += `
                    <option value="${user.id}">${user.first_name} ${user.last_name}</option>
                `;
            })
        }) 
        .catch(error => {
            console.error(error);
        })
    })

    function validProfEdit () {
        if (editProf.value == '') {
            alert('Vous devez saisir un professeur');
            return false;
        }
    }

    //////////////////////////
    //Afficher les niveaux
    //////////////////////////
    //LES ID
    const informations = document.getElementById('informations');
    const nom = document.getElementById('nom');
    const professeur = document.getElementById('professeur');
    const filiere = document.getElementById('filiere');

    //LES CLASS
    const c_informations = Array.from(document.getElementsByClassName('informations'));
    const c_nom = Array.from(document.getElementsByClassName('nom'));
    const c_professeur = Array.from(document.getElementsByClassName('professeur'));
    const c_filiere = Array.from(document.getElementsByClassName('filiere'));

    const changeTaille = document.getElementById('changeTaille');
    var height;

    function media_change () {
        // Inférieur à 768px
        if (mediaQuery.matches) {

            informations.classList.remove('hidden');

            c_informations.forEach((element) => {
                element.classList.remove('hidden');
                height = element.offsetHeight;
            });

            nom.classList.add('hidden');

            c_nom.forEach((element) => {
                element.classList.add('hidden');
            });

            professeur.classList.add('hidden');

            c_professeur.forEach((element) => {
                element.classList.add('hidden');
            });

            filiere.classList.add('hidden');

            c_filiere.forEach((element) => {
                element.classList.add('hidden');
            });

            if (changeTaille) {
                changeTaille.setAttribute('colspan', 2);
            }

        // Supérieur à 768px
        } else {
            //ID
            informations.classList.add('hidden');

            c_informations.forEach((element) => {
                element.classList.add('hidden');
            });

            nom.classList.remove('hidden');

            c_nom.forEach((element) => {
                element.classList.remove('hidden');
                height = element.offsetHeight;
            });

            professeur.classList.remove('hidden');

            c_professeur.forEach((element) => {
                element.classList.remove('hidden');
            });

            filiere.classList.remove('hidden');

            c_filiere.forEach((element) => {
                element.classList.remove('hidden');
            });

            if (changeTaille) {
                changeTaille.setAttribute('colspan', 4  );
            }
        }
    }

    mediaQuery.addEventListener('change', (event) => {
        media_change();
    });

    media_change();

    const tableModule = document.getElementById('tableModule');
    const toutLesDiv = tableModule.querySelectorAll('.tdDivs');
        
    toutLesDiv.forEach((div) => {
        div.style.minHeight = height + 'px';
    });
</script>
@endsection