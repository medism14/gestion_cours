@extends('Layouts.authed')

@section('title', 'Documents')

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

    body {
        max-width: 100%;
        overflow-x: hidden;
    }

    @media screen and (max-width: 768px) {
        th div {
            padding: 3px;
        }

        td div {
            padding: 1px;
        }
    }

    .progress-bar {
        height: 20px;
        background-color: #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background-color: #4CAF50;
        transition: width 0.3s ease-in-out;
    }

</style>

@section('content')
    <h1 class="text-center md:text-3xl lg: font-bold">Documents PDF</h1>

    <!-- Barre de recherche -->
    <div class="block w-full mx-auto rounded-lg p-2 py-4 flex justify-center flex-col">
        <form action="{{ route('documents.index') }}" class="p-0 m-0" method="POST">
            @csrf
            <div class="w-full flex justify-center space-x-1 items-center">
                <input id="search" placeholder="Rechercher un document..." name="search" type="text" class="text-[0.7rem] lg:text-sm border-1 border-gray-900 bg-gray-300 text-black outline-none p-2 rounded h-[2rem]">
            </div>
            <div class="w-full flex justify-center mt-3">
                <button type="submit" class="text-[0.7rem] lg:text-sm p-1 border-2 border-blue-600 rounded-lg transition-all duration-300 ease-in-out bg-blue-600 hover:bg-blue-700 text-white">Rechercher</button>
            </div>
        </form> 
    </div>

    <!-- Tableau -->
    <div id="table-div" class="block w-full">
        @if (auth()->user()->role == 0)  
            <div class="mx-auto w-full max-w-full md:w-[90%] flex justify-end">
                <button id="openModalAdd" title="Ajouter un document" class="border-2 text-green-600 border-green-600 transition-all text-[0.7rem] lg:text-sm duration-300 ease-in-out hover:bg-green-600 hover:text-white p-1 rounded-lg font-bold px-4"><i class="fas fa-plus"></i></button>
            </div>
        @endif
        <table id="tableDocument" class="mx-auto p-2 w-full md:w-[90%] whitespace-nowrap text-[0.7rem] lg:text-sm">
            <thead>
                <tr>
                    <th class="hidden" id="informations"><div>Informations</div></th>
                    <th id="titre"><div>Titre</div></th>
                    <th id="nomFichier"><div>Nom du fichier</div></th>
                    <th id="taille"><div>Taille</div></th>
                    <th id="visibilite"><div>Visibilité</div></th>
                    <th id="date"><div>Date</div></th>
                    <th id="actions"><div>Actions</div></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $document)
                    @php
                        $date = explode(' ', $document->created_at)[0];
                        $visibilityLabel = match($document->visibility) {
                            'all' => 'Tous',
                            'teachers' => 'Professeurs',
                            'students' => 'Étudiants',
                        };
                    @endphp
                    <tr>
                        <td class="informations w-full hidden">
                            <div class="flex justify-center items-start flex-col tdDivs">
                                <span class="font-bold">Titre: {{ $document->title }}</span>
                                <span>Fichier: {{ $document->filename }}</span>
                                <span>Taille: {{ $document->formatted_file_size }}</span>
                                <span>Visibilité: {{ $visibilityLabel }}</span>
                                <span>Date: {{ $date }}</span>
                            </div>
                        </td>
                        <td class="titre"><div class="flex justify-center items-center tdDivs font-bold">{{ $document->title }}</div></td>
                        <td class="nomFichier"><div class="flex justify-center items-center tdDivs">{{ $document->filename }}</div></td>
                        <td class="taille"><div class="flex justify-center items-center tdDivs">{{ $document->formatted_file_size }}</div></td>
                        <td class="visibilite"><div class="flex justify-center items-center tdDivs">{{ $visibilityLabel }}</div></td>
                        <td class="date"><div class="flex justify-center items-center tdDivs">{{ $date }}</div></td>
                        <td class="actions">
                            <div class="flex justify-center items-center tdDivs font-bold">
                                <form method="post" action="{{ route('documents.download', ['id' => $document->id]) }}" class="m-0 p-0">
                                    @csrf
                                    <button title="Télécharger" type="submit" class="downLoad text-green-600 text-xs p-2 border-2 border-green-600 text-[0.7rem] lg:text-sm rounded-lg ml-3 mr-3 transition-all duration-300 ease-in-out hover:bg-green-600 hover:text-white"><i class="fas fa-cloud-download-alt"></i></button>
                                </form>
                                <button class="id hidden">{{ $document->id }}</button>
                                <button title="Voir" class="openModalView text-blue-600 text-xs p-2 border-2 border-blue-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-blue-600 hover:text-white"><i class="fas fa-search"></i></button>
                                @if (auth()->user()->role == 0)  
                                    <button title="Modifier" class="openModalEdit text-slate-600 text-xs p-2 border-2 border-slate-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-slate-600 hover:text-white"><i class="fas fa-pencil-alt"></i></button>
                                    <form method="POST" onsubmit="return confirm('Vous êtes sûr de votre choix ?')" action="{{ route('documents.delete', ['id' => $document->id]) }}" class="m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button title="Supprimer" value="{{ $document->id }}" class="text-red-600 text-xs p-2 border-2 border-red-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-red-600 hover:text-white"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if ($documents->isEmpty())
                    <tr>
                        <td id="changeTaille" colspan="6"><div class="flex justify-center items-center">Aucun document disponible</div></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($loup)
    <div class="w-full flex justify-center mt-3">
        <a href="/documents" class="bg-orange-400 text-white px-2 py-1 rounded-lg">Revenir</a>
    </div>
    @endif

    <!-- Pagination -->
    <div class="w-full max-w-full mt-5 md:w-[90%] mx-auto flex my-3 justify-center text-sm lg:text-base md:text-sm">
        <div class="pagination">
        @if ($documents->hasPages())
            <nav>
                @if ($documents->onFirstPage())
                    <span class="p-2 bg-gray-300 m-2 rounded shadow-md">
                        Précédent
                    </span>
                @else
                    <a href="{{ $documents->previousPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
                        Précédent
                    </a>
                @endif

                @if ($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
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
    
    @if (auth()->user()->role == 0)  
    <!-- ADD MODAL -->
    <div id="addModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center h-screen overflow-y-auto">
        <div id="subAddModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="m-0 p-0" id="addForm">    
            @csrf
            <div id="closeModalAdd" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Ajout de document PDF</div>
                
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full justify-center md:space-x-2">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addFile">Fichier PDF (max 50 Mo): </label>
                        <input id="addFile" name="addFile" type="file" accept=".pdf" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                        <div id="uploadProgress" class="hidden w-full mt-2">
                            <div class="progress-bar">
                                <div id="progressBarFill" class="progress-bar-fill" style="width: 0%"></div>
                            </div>
                            <p id="progressText" class="text-center text-sm mt-1">0%</p>
                        </div>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addTitle">Titre: </label>
                        <input name="addTitle" id="addTitle" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent" required>
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addVisibility">Visible par: </label>
                        <select name="addVisibility" id="addVisibility" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="all">Tous (Profs + Étudiants)</option>
                            <option value="teachers">Professeurs uniquement</option>
                            <option value="students">Étudiants uniquement</option>
                        </select>
                    </div>
                </div>

                <!-- Row -->
                <div class="w-full p-2 flex justify-center items-center overflow-hidden">
                    <div class="w-full md:w-3/4 flex justify-center flex-col">
                        <label for="addDescription" class="text-center">Description (optionnel): </label>
                        <textarea name="addDescription" id="addDescription" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent" cols="5" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="w-full p-5 py-3 flex justify-around items-center">
                <button type="submit" id="saveAddButton" class="p-2 bg-green-600 text-white rounded-lg transition-all duration-300 ease-in-out hover:bg-green-700">Enregistrer</button>
                <button type="button" id="cancelAddButton" class="p-2 bg-red-600 text-white rounded-lg transition-all duration-300 ease-in-out hover:bg-red-700">Annuler</button>
            </div>
        </form>    
        </div>
    </div>
    @endif

    <!-- VIEW MODAL -->
    <div id="viewModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center h-screen overflow-y-auto">
        <div id="subViewModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
            <div id="closeModalView" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Détails du document</div>
                
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewTitle">Titre: </label>
                        <input id="viewTitle" readonly name="viewTitle" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewFilename">Nom du fichier: </label>
                        <input id="viewFilename" readonly name="viewFilename" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewSize">Taille: </label>
                        <input id="viewSize" readonly name="viewSize" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewVisibility">Visible par: </label>
                        <input id="viewVisibility" readonly name="viewVisibility" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewAdmin">Posté par: </label>
                        <input id="viewAdmin" readonly name="viewAdmin" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewDate">Date: </label>
                        <input id="viewDate" readonly name="viewDate" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <div class="md:flex w-full md:space-x-2">
                    <div class="w-none md:w-1/5"></div>
                    <div class="w-full md:w-3/5 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewDescription">Description: </label>
                        <textarea name="viewDescription" id="viewDescription" readonly cols="30" rows="5" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent"></textarea>
                    </div>
                    <div class="w-none md:w-1/5"></div>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->role == 0) 
    <!-- EDIT MODAL -->
    <div id="editModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center h-screen overflow-y-auto">
        <div id="subEditModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem]">
        <form method="POST" action="{{ route('documents.edit') }}" enctype="multipart/form-data" class="m-0 p-0">    
            @csrf
            <div id="closeModalEdit" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Modification du document</div>
                
            <input type="text" class="hidden" id="editId" name="id">
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full justify-center md:space-x-2">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editFile">Nouveau fichier (optionnel): </label>
                        <input id="editFile" name="editFile" type="file" accept=".pdf" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                        <span>Fichier actuel: <span id="actualFile"></span></span>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editTitle">Titre: </label>
                        <input name="editTitle" id="editTitle" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent" required>
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editVisibility">Visible par: </label>
                        <select name="editVisibility" id="editVisibility" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="all">Tous (Profs + Étudiants)</option>
                            <option value="teachers">Professeurs uniquement</option>
                            <option value="students">Étudiants uniquement</option>
                        </select>
                    </div>
                </div>

                <!-- Row -->
                <div class="w-full p-2 flex justify-center items-center overflow-hidden">
                    <div class="w-full md:w-3/4 flex justify-center flex-col">
                        <label for="editDescription" class="text-center">Description: </label>
                        <textarea name="editDescription" id="editDescription" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent" cols="5" rows="3"></textarea>
                    </div>
                </div>
            </div>
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

    //////////////////////////
    //VIEW MODAL
    //////////////////////////
    const closeModalView = document.getElementById('closeModalView');
    const openModalView = Array.from(document.getElementsByClassName('openModalView'));
    const viewModal = document.getElementById('viewModal');

    const viewTitle = document.getElementById('viewTitle');
    const viewFilename = document.getElementById('viewFilename');
    const viewSize = document.getElementById('viewSize');
    const viewVisibility = document.getElementById('viewVisibility');
    const viewAdmin = document.getElementById('viewAdmin');
    const viewDate = document.getElementById('viewDate');
    const viewDescription = document.getElementById('viewDescription');

    function resetValuesViewModal() {
        viewModal.classList.add('hidden');
    }
    
    closeModalView.addEventListener('click', () => {
        resetValuesViewModal();
    });

    openModalView.forEach((btn) => {
        btn.addEventListener('click', () => {
            let id = parseInt(btn.parentNode.querySelector('.id').textContent);
            fetch('/documents/getDocument/' + id)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                let created_at = data.created_at.split('T')[0];
                let visibilityText = data.visibility === 'all' ? 'Tous' : 
                                    (data.visibility === 'teachers' ? 'Professeurs' : 'Étudiants');
                
                // Format file size
                let bytes = data.file_size;
                let sizeText;
                if (bytes >= 1048576) {
                    sizeText = (bytes / 1048576).toFixed(2) + ' Mo';
                } else if (bytes >= 1024) {
                    sizeText = (bytes / 1024).toFixed(2) + ' Ko';
                } else {
                    sizeText = bytes + ' octets';
                }
                
                viewTitle.value = data.title;
                viewFilename.value = data.filename;
                viewSize.value = sizeText;
                viewVisibility.value = visibilityText;
                viewAdmin.value = data.user ? data.user.first_name + ' ' + (data.user.last_name || '') : 'Admin';
                viewDate.value = created_at;
                viewDescription.value = data.description || 'Aucune description';

                viewModal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error)
            });
        });
    });

    @if (auth()->user()->role == 0)
    //////////////////////////
    //ADD MODAL
    //////////////////////////
    const closeModalAdd = document.getElementById('closeModalAdd');
    const openModalAdd = document.getElementById('openModalAdd');
    const addModal = document.getElementById('addModal');
    const cancelAddButton = document.getElementById('cancelAddButton');

    function resetValuesAddModal() {
        addModal.classList.add('hidden');
        document.getElementById('addForm').reset();
    }
    
    closeModalAdd.addEventListener('click', () => {
        resetValuesAddModal();
    });

    cancelAddButton.addEventListener('click', () => {
        resetValuesAddModal();
    });

    openModalAdd.addEventListener('click', () => {
        addModal.classList.remove('hidden');
    });

    //////////////////////////
    //EDIT MODAL
    //////////////////////////
    const closeModalEdit = document.getElementById('closeModalEdit');
    const openModalEdit = Array.from(document.getElementsByClassName('openModalEdit'));
    const editModal = document.getElementById('editModal');
    const cancelEditButton = document.getElementById('cancelEditButton');
    const editId = document.getElementById('editId');
    const editTitle = document.getElementById('editTitle');
    const editDescription = document.getElementById('editDescription');
    const editVisibility = document.getElementById('editVisibility');
    const actualFile = document.getElementById('actualFile');

    function resetValuesEditModal() {
        editModal.classList.add('hidden');
    }
    
    closeModalEdit.addEventListener('click', () => {
        resetValuesEditModal();
    });

    cancelEditButton.addEventListener('click', () => {
        resetValuesEditModal();
    });

    openModalEdit.forEach((btn) => {
        btn.addEventListener('click', () => {
            let id = parseInt(btn.parentNode.querySelector('.id').textContent);
            fetch('/documents/getDocument/' + id)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                editId.value = data.id;
                editTitle.value = data.title;
                editDescription.value = data.description || '';
                actualFile.textContent = data.filename;
                
                // Set visibility dropdown
                let options = editVisibility.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === data.visibility) {
                        options[i].selected = true;
                        break;
                    }
                }

                editModal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error)
            });
        });
    });
    @endif

    //////////////////////////
    //Responsive table
    //////////////////////////
    const informations = document.getElementById('informations');
    const titre = document.getElementById('titre');
    const nomFichier = document.getElementById('nomFichier');
    const taille = document.getElementById('taille');
    const visibilite = document.getElementById('visibilite');
    const date = document.getElementById('date');

    const c_informations = Array.from(document.getElementsByClassName('informations'));
    const c_titre = Array.from(document.getElementsByClassName('titre'));
    const c_nomFichier = Array.from(document.getElementsByClassName('nomFichier'));
    const c_taille = Array.from(document.getElementsByClassName('taille'));
    const c_visibilite = Array.from(document.getElementsByClassName('visibilite'));
    const c_date = Array.from(document.getElementsByClassName('date'));

    const changeTaille = document.getElementById('changeTaille');

    function media_change() {
        if (mediaQuery.matches) {
            informations.classList.remove('hidden');

            c_informations.forEach((information) => {
                information.classList.remove('hidden');
            });
            
            titre.classList.add('hidden');
            nomFichier.classList.add('hidden');
            taille.classList.add('hidden');
            visibilite.classList.add('hidden');
            date.classList.add('hidden');

            c_titre.forEach((t) => t.classList.add('hidden'));
            c_nomFichier.forEach((n) => n.classList.add('hidden'));
            c_taille.forEach((t) => t.classList.add('hidden'));
            c_visibilite.forEach((v) => v.classList.add('hidden'));
            c_date.forEach((d) => d.classList.add('hidden'));

            if (changeTaille) {
                changeTaille.setAttribute('colspan', 2);
            }

        } else {
            informations.classList.add('hidden');

            c_informations.forEach((information) => {
                information.classList.add('hidden');
            });

            titre.classList.remove('hidden');
            nomFichier.classList.remove('hidden');
            taille.classList.remove('hidden');
            visibilite.classList.remove('hidden');
            date.classList.remove('hidden');

            c_titre.forEach((t) => t.classList.remove('hidden'));
            c_nomFichier.forEach((n) => n.classList.remove('hidden'));
            c_taille.forEach((t) => t.classList.remove('hidden'));
            c_visibilite.forEach((v) => v.classList.remove('hidden'));
            c_date.forEach((d) => d.classList.remove('hidden'));

            if (changeTaille) {
                changeTaille.setAttribute('colspan', 6);
            }
        }
    }

    media_change();

    window.addEventListener('resize', () => {
        media_change();
    });

</script>
@endsection
