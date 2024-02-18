@extends('Layouts.authed')

@section('title', 'Filières')

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
    <h1 class="text-center md:text-3xl lg: font-bold">Gestion de filières</h1>

    <!-- Barre de recherche -->
    <div class="block w-full mx-auto rounded-lg p-2 py-4 flex justify-center flex-col">
        <div class="w-full flex justify-center items-center">
        <form action="{{ route('sectors.index') }}" class="p-0 m-0">
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
        <table id="tablesector" class="mx-auto p-2 w-full md:w-[90%] whitespace-nowrap text-[0.7rem] lg:text-sm">
            <thead>
                <tr>
                    <th id="prenom"><div>Nom</div></th>
                    <th id="actions"><div>Actions</div></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sectors as $sector)
                    <tr>
                        <td class="nom"><div class="flex justify-center items-center">{{ $sector->name }}</div></td>
                        <td class="actions">
                            <div class="flex justify-center items-center">
                                <button class="openModalView text-blue-600 text-xs p-2 border-2 border-blue-600 text-[0.7rem] lg:text-sm rounded-lg ml-3 mr-3 transition-all duration-300 ease-in-out hover:bg-blue-600 hover:text-white"><i class="fas fa-search"></i></button>
                                <button class="id hidden">{{ $sector->id }}</button>
                                <button class="openModalEdit text-slate-600 text-xs p-2 border-2 border-slate-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-slate-600 hover:text-white"><i class="fas fa-pencil-alt"></i></button>
                                <form method="POST" onsubmit="return confirm('Vous êtes sur de votre choix ?')" action="{{ route('sectors.delete', ['id' => $sector->id]) }}" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button value="{{ $sector->id }}" class="text-red-600 text-xs p-2 border-2 border-red-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-red-600 hover:text-white"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if ($sectors->isEmpty())
                    <tr>
                        <td colspan="2"><div class="flex justify-center items-center">La table est vide</div></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($loup)
    <div class="w-full flex justify-center mt-3">
        <a href="/sectors" class="bg-orange-400 text-white px-2 py-1 rounded-lg">Revenir</a>
    </div>
    @endif

    <!-- Pagination -->
    <div class="w-full max-w-full mt-5 md:w-[90%] mx-auto flex my-3 justify-center text-sm lg:text-base md:text-sm">
        <div class="pagination">
        @if ($sectors->hasPages())
            <nav>
                @if ($sectors->onFirstPage())
                    <span class="p-2 bg-gray-300 m-2 rounded shadow-md">
                        Precedent
                    </span>
                @else
                    <a href="{{ $sectors->previousPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
                        Precedent
                    </a>
                @endif

                @if ($sectors->hasMorePages())
                    <a href="{{ $sectors->nextPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
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
        <form method="POST" action="{{ route('sectors.store') }}" class="m-0 p-0">    
            @csrf
            <!-- Close -->
            <div id="closeModalAdd" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Ajout de filiere</div>
                
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
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <label for="addLevel">Niveaux: </label>
                            <input id="addLevel" name="addLevel" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                        </div>
                        <div>
                            <button type="button" id="addLevelBtn" class="p-2 bg-slate-500 text-white rounded-lg transition-all duration-300 ease-in-out ">Ajouter</button>
                        </div>
                        
                    </div>
                </div>

                <div id="levelList" class="w-full justify-center p-2 m-2 text-center overflow-x-hidden">
                    <input type="text" class="hidden" name="addMaxDegree" id="addMaxDegree">
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
            <!-- Close -->
            <div id="closeModalView" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Vue de filière</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewName">Prenom: </label>
                        <input id="viewName" readonly name="viewName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div id="levelListView" class="w-full justify-center p-2 m-2 text-center overflow-x-hidden">
                    <div class="w-full flex space-x-6">
                        <span class="flex-1 flex justify-end">Identifiant</span>
                        <span class="flex-1 flex justify-start">Niveaux</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit MODALS -->
    <div id="editModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center overflow-y-auto">
        <!-- Modal -->
        <div id="subEditModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
        <form method="POST" action="{{ route('sectors.edit') }}" class="m-0 p-0">    
            @csrf
            <!-- Close -->
            <div id="closeModalEdit" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Modification de filiere</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <input type="text" class="hidden" id="editId" name="id">
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editName">Nom: </label>
                        <input id="editName" name="editName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <div class="flex-1 w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                            <label for="editLevel">Niveaux: </label>
                            <input id="editLevel" name="editLevel" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                        </div>
                        <div>
                            <button type="button" id="editLevelBtn" class="px-2 py-1 bg-slate-500 text-white rounded-lg transition-all duration-300 ease-in-out hover:bg-slate-600">Ajouter</button>
                        </div>
                        
                    </div>
                </div>

                <div id="levelListEdit" class="w-full justify-center p-2 m-2 text-center overflow-x-hidden">
                    <input type="text" class="hidden" name="editMaxDegree" id="editMaxDegree">
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
    const addLevel = document.getElementById('addLevel');
    const addLevelBtn = document.getElementById('addLevelBtn');
    const levelList = document.getElementById('levelList');

    var jAdd = 1;

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
    
    const addMaxDegree = document.getElementById('addMaxDegree');

    addLevelBtn.addEventListener('click', () => {
        
        let level = addLevel.value;
        addLevel.value = '';

        let input = document.createElement('input');
        let span = document.createElement('span');
        let input_nombreI = document.createElement('input');
        let i = document.createElement('i');

        let div1 = document.createElement('div');
        let div2 = document.createElement('div');
        let div3 = document.createElement('div');

        let divGenerale = document.createElement('div');

        divGenerale.classList.add('w-full', 'flex', 'justify-center', 'm-2', 'text-center', 'block');

        div1.classList.add('flex-1', 'flex', 'justify-center','items-center');
        div2.classList.add('flex-1', 'flex', 'justify-start','items-center');
        div3.classList.add('flex-1', 'flex', 'justify-end','items-center', 'divDegreeLevel');

        i.classList.add('fas', 'fa-times', 'text-red-600', 'text-xl', 'cursor-pointer', 'removeElement');


        input_nombreI.value = jAdd;
        input_nombreI.setAttribute('name', 'addDegree' + jAdd);
        input_nombreI.classList.add('hidden', 'inputDegreeLevel');

        input.value = level;
        input.classList.add('p-1', 'bg-transparent', 'overflow-x-auto', 'text-center', 'inputNameLevel');
        input.setAttribute('name', 'addName' + jAdd)
        input.setAttribute('readonly', true);

        span.appendChild(i);

        div1.appendChild(input);
        div2.appendChild(span);
        div3.textContent = jAdd;

        divGenerale.appendChild(input_nombreI);
        divGenerale.appendChild(div3);
        divGenerale.appendChild(div1);
        divGenerale.appendChild(div2);

        levelList.appendChild(divGenerale);

        addMaxDegree.value = jAdd;

        jAdd++;
    });
    
    levelList.addEventListener('click', (event) => {
        let item = event.target;

        if (item.classList.contains('removeElement')) {
            let row = item.parentNode.parentNode.parentNode;

            row.remove();

            ordinateDegreeAndName();
        }
    });

    function ordinateDegreeAndName () {

        const inputNameLevels = Array.from(document.getElementsByClassName('inputNameLevel'));
        const inputDegreeLevels = Array.from(document.getElementsByClassName('inputDegreeLevel'));
        const divDegreeLevels = Array.from(document.getElementsByClassName('divDegreeLevel'));

        addMaxDegree.value = inputNameLevels.length;

        inputNameLevels.forEach((inputNameLevel) => {
            inputNameLevel.setAttribute('name', 'inputNameLevel' + jAdd);
            inputNameLevel.value = inputNameLevel.value;
            jAdd++;
        });

        jAdd = 1;

        inputDegreeLevels.forEach((inputDegreeLevel) => {
            inputDegreeLevel.setAttribute('name', 'addDegree' + jAdd);
            inputDegreeLevel.value = jAdd;
            jAdd++;
        });
        
        jAdd = 1;

        divDegreeLevels.forEach((divDegreeLevel) => {
            divDegreeLevel.textContent = jAdd;
            jAdd++;
        });


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
    const viewLevel = document.getElementById('viewLevel');
    const viewLevelBtn = document.getElementById('viewLevelBtn');
    const levelListView = document.getElementById('levelListView');

    var j = 1;

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

            fetch('/sectors/getSector/' + id)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network resonse was not ok');
                }
                return response.json();
            })
            .then(data => {
                
                viewName.value = data.name;

                levels = data.levels;
                
                levels.forEach((level) => {

                    levelListView.innerHTML += `
                        <div class="w-full flex space-x-6">
                            <span class="flex-1 flex justify-end">${level.id}</span>
                            <span class="flex-1 flex justify-start">${level.name}</span>
                        </div>
                    `;

                })

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

    const editId = document.getElementById('editId');

    const editName = document.getElementById('editName');
    const editLevel = document.getElementById('editLevel');
    const editLevelBtn = document.getElementById('editLevelBtn');
    const levelListEdit = document.getElementById('levelListEdit');
    const editMaxDegree = document.getElementById('editMaxDegree');

    var jEdit = 1;


    function resetValuesEditModal() {
        editModal.classList.add('hidden');

        editName.value = '';
        editLevel.value = '';

        const removeElementEdit = Array.from(document.getElementsByClassName('removeElementEdit'));
        
        removeElementEdit.forEach((element, index) => {
            let row = element.parentNode.parentNode.parentNode;

            row.remove();

        })

        window.location.reload();

        jEdit = 1;

    }

    //Fermeture modal en haut à droite
    closeModalEdit.addEventListener('click', () => {
        resetValuesEditModal();
    });

    //Fermeture modal en appuyant sur le bouton annuler
    cancelEditButton.addEventListener('click', () => {
        resetValuesEditModal();
    });


    //Ouverture du modal avec le bouton +
    openModalEdit.forEach((btn) => {
        btn.addEventListener('click', () => {
            id = parseInt(btn.parentNode.querySelector('.id').textContent);

            fetch('/sectors/getSector/' + id)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network resonse was not ok');
                }
                return response.json();
            })
            .then(data => {
                
                editId.value = data.id;

                editName.value = data.name;

                levels = data.levels;
                
                levels.forEach((l) => {

                    let level = l.name;
                    
                    editLevel.value = '';

                    let input = document.createElement('input');
                    let span = document.createElement('span');
                    let input_nombreI = document.createElement('input');
                    let i = document.createElement('i');

                    let div1 = document.createElement('div');
                    let div2 = document.createElement('div');
                    let div3 = document.createElement('div');

                    let divGenerale = document.createElement('div');

                    divGenerale.classList.add('w-full', 'flex', 'justify-center', 'm-2', 'text-center', 'block');

                    div1.classList.add('flex-1', 'flex', 'justify-center','items-center');
                    div2.classList.add('flex-1', 'flex', 'justify-start','items-center');
                    div3.classList.add('flex-1', 'flex', 'justify-end','items-center', 'divDegreeLevelEdit');

                    i.classList.add('fas', 'fa-times', 'text-red-600', 'text-xl', 'cursor-pointer', 'removeElementEdit');

                    input_nombreI.value = jEdit;
                    input_nombreI.setAttribute('name', 'editDegree' + jEdit);
                    input_nombreI.classList.add('hidden', 'inputDegreeLevelEdit');

                    input.value = level;
                    input.classList.add('p-1', 'bg-transparent', 'overflow-x-auto', 'text-center', 'inputNameLevelEdit');
                    input.setAttribute('name', 'editName' + jEdit)
                    input.setAttribute('readonly', true);

                    span.appendChild(i);

                    div1.appendChild(input);
                    div2.appendChild(span);
                    div3.textContent = jEdit;

                    divGenerale.appendChild(input_nombreI);
                    divGenerale.appendChild(div3);
                    divGenerale.appendChild(div1);
                    divGenerale.appendChild(div2);

                    levelListEdit.appendChild(divGenerale);

                    editMaxDegree.value = jEdit;

                    j++;

                    ordinateDegreeAndNameEdit();

                })

                editModal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error)
            });
        });
    });
    

    editLevelBtn.addEventListener('click', () => {
        
        let level = editLevel.value;
        editLevel.value = '';

        let input = document.createElement('input');
        let span = document.createElement('span');
        let input_nombreI = document.createElement('input');
        let i = document.createElement('i');

        let div1 = document.createElement('div');
        let div2 = document.createElement('div');
        let div3 = document.createElement('div');

        let divGenerale = document.createElement('div');

        divGenerale.classList.add('w-full', 'flex', 'justify-center', 'm-2', 'text-center', 'block');

        div1.classList.add('flex-1', 'flex', 'justify-center','items-center');
        div2.classList.add('flex-1', 'flex', 'justify-start','items-center');
        div3.classList.add('flex-1', 'flex', 'justify-end','items-center', 'divDegreeLevelEdit');

        i.classList.add('fas', 'fa-times', 'text-red-600', 'text-xl', 'cursor-pointer', 'removeElementEdit');

        input_nombreI.value = jEdit;
        input_nombreI.setAttribute('name', 'editDegree' + jEdit);
        input_nombreI.classList.add('hidden', 'inputDegreeLevelEdit');

        input.value = level;
        input.classList.add('p-1', 'bg-transparent', 'overflow-x-auto', 'text-center', 'inputNameLevelEdit');
        input.setAttribute('name', 'editName' + jEdit)
        input.setAttribute('readonly', true);

        span.appendChild(i);

        div1.appendChild(input);
        div2.appendChild(span);
        div3.textContent = jEdit;

        divGenerale.appendChild(input_nombreI);
        divGenerale.appendChild(div3);
        divGenerale.appendChild(div1);
        divGenerale.appendChild(div2);

        levelListEdit.appendChild(divGenerale);

        editMaxDegree.value = jEdit;

        ordinateDegreeAndNameEdit();

        jEdit++;
    });
    

    levelListEdit.addEventListener('click', (event) => {
        let item = event.target;

        if (item.classList.contains('removeElementEdit')) {
            let row = item.parentNode.parentNode.parentNode;
            row.remove();

            ordinateDegreeAndNameEdit();
        }
    });

    function ordinateDegreeAndNameEdit () {
        const inputNameLevels = Array.from(document.getElementsByClassName('inputNameLevelEdit'));
        const inputDegreeLevels = Array.from(document.getElementsByClassName('inputDegreeLevelEdit'));
        const divDegreeLevelsEdit = Array.from(document.getElementsByClassName('divDegreeLevelEdit'));

        let jEdit = 1;

        editMaxDegree.value = inputNameLevels.length;

        inputNameLevels.forEach((inputNameLevel) => {
            inputNameLevel.setAttribute('name', 'editName' + jEdit);
            jEdit++;
        });

        jEdit = 1;

        inputDegreeLevels.forEach((inputDegreeLevel) => {
            inputDegreeLevel.setAttribute('name', 'editDegree' + jEdit);
            inputDegreeLevel.value = jEdit;
            jEdit++;
        });
        
        jEdit = 1;

        divDegreeLevelsEdit.forEach((divDegreeLevel) => {
            divDegreeLevel.textContent = jEdit;
            jEdit++;
        });

    }


    //////////////////////////
    //Afficher les niveaux
    //////////////////////////


</script>
@endsection