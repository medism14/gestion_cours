@extends('Layouts.authed')

@section('title', 'Utilisateurs')

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

    /* Style de l'infobulle */
.tooltip {
    visibility: hidden;
    position: absolute;
    background-color: #333;
    color: #fff;
    padding: 5px;
    border-radius: 5px;
    z-index: 1;
}

/* Style de l'infobulle lorsqu'elle est affichée */
.tooltip.show {
    visibility: visible;
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
    <h1 class="text-center md:text-3xl lg: font-bold">Gestion d'utilisateurs</h1>

    <!-- Barre de recherche -->
    <div class="block w-full mx-auto rounded-lg p-2 py-4 flex justify-center flex-col">
        <form action="{{ route('users.index') }}" class="p-0 m-0 inline" onsubmit="return confirmFiliere()">
            @csrf
            <div class="w-full flex justify-center mb-1">
                <div class="mr-3 flex flex-col space-y-2 border-r-2 border-gray-900 px-6">
                    <input type="text" id="searchFiliereInput" placeholder="Selection rapide..." class="outline-none px-3 py-1 rounded-lg focus:ring-2 border-none focus:border-slate-700 shadow-md">
                    <select name="searchFiliere" id="searchFiliere" class="outline-none px-3 py-1 rounded-lg focus:ring-2 border-none focus:border-slate-700 shadow-md">
                        @if (!$levels->isEmpty())
                            <option value="all">Toutes les filières</option>
                        @endif
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->sector->name }}: {{ $level->name }}</option>
                        @endforeach
                    </select>
                    <div class="w-full flex justify-center mt-3">
                        <button type="submit" class="text-[0.7rem] lg:text-sm  p-1 border-2 border-blue-600 rounded-lg transition-all duration-300 ease-in-out bg-blue-600 hover:bg-blue-700 text-white">Rechercher</button>
                    </div>
                </div>
                </form> 
                <div class="flex flex-col items-center justify-center px-3">
                <form action="{{ route('users.index') }}" class="p-0 m-0 inline">
                    @csrf
                    <div class="flex">
                        <input id="search" placeholder="Ecrivez ici..." name="search" type="text" class="text-[0.7rem] lg:text-sm  border-1 border-gray-900 bg-gray-300 text-black outline-none p-2 rounded h-[2rem]">
                        <div class="px-3">
                            <i id="tooltipIcon" class="fas fa-question-circle p-1">
                            </i>
                            <div id="tooltipInfo" class="hidden break-words absolute bg-gray-600 z-1 px-3 md:px-5 py-1 md:py-3 text-white right-5 top-0 rounded-lg text-[0.6rem] md:text-sm">
                                Recherche par:
                                <p class="text-center mt-3 break-words">Prenom, Nom, Email, Telephone, Role, Nom de la filière</p>

                                <p class="text-start mt-5"><span class="underline">Conseil utile:</span> commencez par écrire le mot recherché et le système recherchera toutes les correspondances avec cette entrée.</p>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex justify-center mt-3">
                        <button type="submit" class="text-[0.7rem] lg:text-sm  p-1 border-2 border-blue-600 rounded-lg transition-all duration-300 ease-in-out bg-blue-600 hover:bg-blue-700 text-white">Rechercher</button>
                    </div>
                </form> 
                </div>
            </div>
    </div>

    <!-- Tableau -->
    <div id="table-div" class="block w-full">
        <div class="mx-auto w-full max-w-full md:w-[90%] flex space-x-5 justify-end">
            <div class="flex-1 flex justify-start space-x-1 md:space-x-3">
                <form action="{{ route('users.download') }}" method="POST" class="p-0 m-0" onsubmit="return submitFunction()">
                    @csrf
                    <input type="hidden" name="users" value="{{ json_encode($allUsers) }}">
                    <button id="download" title="Télécharger" class="border-2 text-green-700 border-green-700 transition-all text-[0.65rem] lg:text-sm duration-300 ease-in-out hover:bg-green-700 hover:text-white p-1 rounded-lg font-bold px-4">Exporter <i class="fas fa-upload"></i></button>
                </form>

                <form id="formImport" action="{{ route('users.importCSV') }}" method="POST" class="p-0 m-0" enctype="multipart/form-data" onsubmit="return submitFunction()">
                    @csrf
                    <input id="importInput" name="fichier" type="file" class="hidden">
                    <button title="Importer" title="Importer" id="import" class="border-2 text-green-700 border-green-700 transition-all text-[0.65rem] lg:text-sm duration-300 ease-in-out hover:bg-green-700 hover:text-white p-1 rounded-lg font-bold px-4">Importer <i class="fas fa-download"></i></button>
                </form>
            </div>
            <div class="flex-1 flex justify-end">
                <button title="Ajouter" id="openModalAdd" class="border-2 text-green-600 border-green-600 transition-all text-[0.65rem] lg:text-sm duration-300 ease-in-out hover:bg-green-600 hover:text-white p-1 rounded-lg font-bold px-4"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <table id="tableUser" class="mx-auto p-2 w-full md:w-[90%] whitespace-nowrap text-[0.65rem] lg:text-sm">
            <thead>
                <tr>
                    <th class="hidden" id="informations"><div>Informations</div></th>
                    <th id="prenom"><div>Prenom</div></th>
                    <th id="nom"><div>Nom</div></th>
                    <th id="email"><div>Email</div></th>
                    <th id="phone"><div>Phone</div></th>
                    <th id="role"><div>Role</div></th>
                    <th id="actions"><div>Actions</div></th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="informations w-full hidden">
                            <div class="flex items-center tdDivs">
                                <div class="flex-1">
                                    <span class="text-start block font-bold w-full text-[0.8rem]">Nom/Prenom: {{ $user->first_name }}, {{ $user->last_name }}</span>
                                    <span class="text-start block w-full">Email: {{ $user->email }}</span>
                                    <span class="text-start block w-full">Telephone: {{ $user->phone }}</span>
                                    <span class="text-start block w-full">Role: {{ $user->role }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="prenom"><div class="flex justify-center items-center font-bold tdDivs">{{ $user->first_name }}</div></td>
                        <td class="nom"><div class="flex justify-center items-center font-bold tdDivs">{{ $user->last_name }}</div></td>
                        <td class="email"><div class="flex justify-center items-center font-bold tdDivs">{{ $user->email }}</div></td>
                        <td class="phone"><div class="flex justify-center items-center font-bold tdDivs">{{ $user->phone }}</div></td>
                        <td class="role"><div class="flex justify-center items-center font-bold tdDivs">
                            {{ $user->role == 0 ? 'Administrateur' : ($user->role == 1 ? 'Professeur' : 'Etudiant' )}}
                        </div></td>
                        <td class="actions">
                            <div class="flex justify-center items-center font-bold tdDivs">
                                <button title="Voir" class="openModalView text-blue-600 p-2 border-2 border-blue-600 text-[0.7rem] lg:text-sm text-xs rounded-lg ml-3 mr-3 transition-all duration-300 ease-in-out hover:bg-blue-600 hover:text-white"><i class="fas fa-search"></i></button>
                                
                                <button class="id hidden">{{ $user->id }}</button>
                                <button title="Modifier" class="openModalEdit text-slate-600 text-xs p-2 border-2 border-slate-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-slate-600 hover:text-white"><i class="fas fa-pencil-alt"></i></button>
                                <form method="POST" onsubmit="return confirm('Vous êtes sur de votre choix ?')" action="{{ route('users.delete', ['id' => $user->id]) }}" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button title="Supprimer" value="{{ $user->id }}" class="text-red-600 text-xs p-2 border-2 border-red-600 text-[0.7rem] lg:text-sm rounded-lg mr-3 transition-all duration-300 ease-in-out hover:bg-red-600 hover:text-white"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if ($users->isEmpty())
                    <tr>
                        <td colspan="6" id="changeTaille"><div class="flex justify-center items-center">La table est vide</div></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($loup)
    <div class="w-full flex justify-center mt-3">
        <a href="/users" class="bg-orange-400 text-white px-2 py-1 rounded-lg">Revenir</a>
    </div>
    @endif

    <!-- Pagination -->
    <div class="w-full max-w-full mt-5 md:w-[90%] mx-auto flex my-3 justify-center text-sm lg:text-base md:text-sm">
        <div class="pagination">
        @if ($users->hasPages())
            <nav>
                @if ($users->onFirstPage())
                    <span class="p-2 bg-gray-300 m-2 rounded shadow-md">
                        Precedent
                    </span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
                        Precedent
                    </a>
                @endif

                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="p-2 bg-gray-300 m-1 rounded shadow-md">
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
        <div id="subAddModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem] pb-[1rem] md:pb-0">
        <form method="POST" action="{{ route('users.store') }}" class="m-0 p-0" onsubmit="return submitFunction()">    
            @csrf
            <!-- Close -->
            <div id="closeModalAdd" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Ajout d'utilisateur</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addFirstName">Prenom: </label>
                        <input id="addFirstName" name="addFirstName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addLastName">Nom: </label>
                        <input id="addLastName" name="addLastName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addEmail">Email: </label>
                        <input id="addEmail" name="addEmail" type="email" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addPhone">Phone: </label>
                        <input id="addPhone" name="addPhone" type="number" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addSexe">Sexe: </label>
                        <select name="addSexe" id="addSexe" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="H">Homme</option>
                            <option value="F">Femme</option>
                        </select>
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addRole">Role: </label>
                        <select id="addRole" name="addRole" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="0">Administrateur</option>                         
                            <option value="1" selected>Professeur</option>                         
                            <option value="2">Etudiant</option>                         
                        </select>
                    </div>
                    <div class="rowFiliereAdd w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="addFiliere">Filière: </label>
                        <input type="text" id="searchAdd" placeholder="Recherchez ici..." class="rounded focus:ring-2 outline-none px-2 py-1">
                        <select id="addFiliere" name="filiereSolo" {{ ($sectors->isEmpty() ? "disabled" : "") }} type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            @if ($sectors->isEmpty()) 
                                <option value="" disabled>Aucune valeur</option>
                            @else        
                                @foreach ($sectors as $sector)
                                    @foreach ($sector->levels as $level)
                                        <option value="{{ $level->id }}">{{ $sector->name }}: {{ $level->name }}</option>
                                    @endforeach
                                @endforeach
                            @endif
                        </select>
                        <button id="addListBtn" class=" bg-blue-600 text-white px-2 py-1 outline-none rounded-lg" type="button">Ajouter</button>
                    </div>
                </div>
                <div class="rowFiliereAdd md:flex w-full md:space-x-2">
                    <div id="addsectorLists" class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        
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

    <!-- VIEW MODALS -->
    <div id="viewModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center overflow-y-auto">
        <!-- Modal -->
        <div id="subViewModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem] pb-[1rem] md:pb-0">
            <!-- Close -->
            <div id="closeModalView" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Vue d'utilisateur</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewFirstName">Prenom: </label>
                        <input id="viewFirstName" readonly name="viewFirstName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewLastName">Nom: </label>
                        <input id="viewLastName" readonly name="viewLastName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewEmail">Email: </label>
                        <input id="viewEmail" readonly name="viewEmail" type="email" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewPhone">Phone: </label>
                        <input id="viewPhone" readonly name="viewPhone" type="number" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewSexe">Sexe: </label>
                        <select name="viewSexe" id="viewSexe" disabled class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="H">Homme</option>
                            <option value="F">Femme</option>
                        </select>
                    </div>
                </div>
                    
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="viewRole">Role: </label>
                        <select id="viewRole" disabled name="viewRole" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="0">Administrateur</option>                         
                            <option value="1">Professeur</option>                         
                            <option value="2">Etudiant</option>                         
                        </select>
                    </div>
                </div>
                <!-- Row -->
                <div class="flex w-full flex-col justify-center items-center md:space-x-2">
                    <div id="viewsectorLists" class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <span>Filieres:</span>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT MODALS -->
    <div id="editModal" class="hidden fixed z-10 inset-0 bg-gray-300 bg-opacity-75 flex justify-center overflow-y-auto">
        <!-- Modal -->
        <div id="subEditModal" class="absolute flex flex-col fixed w-full md:w-[60%] border-2 border-gray-300 bg-white rounded-lg my-[2rem] pb-[1rem] md:pb-0">
        <form method="POST" action="{{ route('users.edit') }}" class="m-0 p-0" onsubmit="return submitFunction()">    
            @csrf
            <!-- Close -->
            <div id="closeModalEdit" class="cursor-pointer absolute right-0 text-2xl p-2"><i class="fas fa-times"></i></div>
            <!-- Titre -->
            <div class="p-4 flex justify-center rounded-lg text-xl font-bold border-b-2">Modification d'utilisateur</div>
                
            <!-- Corps -->
            <div style="background-color: #e0d5b4;" class="flex-1 rounded-lg p-5">
                <!-- Row -->
                <input type="text" class="hidden" id="editId" name="id">
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editFirstName">Prenom: </label>
                        <input id="editFirstName" name="editFirstName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editLastName">Nom: </label>
                        <input id="editLastName" name="editLastName" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editEmail">Email: </label>
                        <input id="editEmail" name="editEmail" type="email" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editPhone">Phone: </label>
                        <input id="editPhone" name="editPhone" type="number" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>

                <!-- Row -->
                <div class="md:flex w-full md:space-x-2 justify-center">
                    <div class="w-full md:w-1/2 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editSexe">Sexe: </label>
                        <select name="editSexe" id="editSexe" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="H">Homme</option>
                            <option value="F">Femme</option>
                        </select>
                    </div>
                </div>
                    
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        <label for="editRole">Role: </label>
                        <select id="editRole" name="editRole" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="1">Professeur</option>                         
                            <option value="2">Etudiant</option>                         
                        </select>
                    </div>
                    <div class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        
                        <label for="editPassword">Mot de passe: </label>
                        <input id="editPassword" name="editPassword" type="password" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                    </div>
                </div>
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div class="w-full md:w-1/2 mx-auto p-2 flex justify-center flex-col items-center overflow-hidden">
                        <input type="text" id="searchEdit" placeholder="Recherchez ici..." class="rounded focus:ring-2 outline-none px-2 py-1">
                        <label for="editFiliere">Filière: </label>
                        <select id="editFiliere" name="filiereSolo" {{ ($sectors->isEmpty() ? "disabled" : "") }} name="addFiliere" type="text" class="m-2 shadow-md w-full border-none rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:border-transparent">
                            @if ($sectors->isEmpty()) 
                                <option value="" disabled>Aucune valeur</option>
                            @else        
                                @foreach ($sectors as $sector)
                                    @foreach ($sector->levels as $level)
                                        <option value="{{ $level->id }}" class="editOptions">{{ $sector->name }}: {{ $level->name }}</option>
                                    @endforeach
                                @endforeach
                            @endif
                        </select>
                        <button id="editListBtn" class=" bg-blue-600 text-white px-2 py-1 outline-none rounded-lg" type="button">Ajouter</button>
                    </div>
                </div>
                <!-- Row -->
                <div class="md:flex w-full md:space-x-2">
                    <div id="editsectorLists" class="w-full md:flex-1 p-2 flex justify-center flex-col items-center overflow-hidden">
                        
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

@endsection

@section('scripts')
    <script>
        //Recherche des filières
        
            function searchFiliereFunction(value) {
                let options = Array.from(searchFiliere.options);
                let i = 0;
                let selected = false;

                options.forEach((option) => {
                    if (!RegExp('^' + value, 'i').test(option.textContent)) {
                        option.disabled = true;
                        option.classList.add('hidden');
                        i++
                    } else {
                        if (!selected) {
                            option.selected = true;
                            selected = true;
                        }
                    }
                });

                if (options.length == i) {
                    searchFiliere.innerHTML += '';
                }
            }

            const searchFiliere = document.getElementById('searchFiliere');
            const searchFiliereInput = document.getElementById('searchFiliereInput');

            searchFiliereInput.addEventListener('input', () => {
                let options = Array.from(searchFiliere.options);
                let selected = false;

                if (searchFiliereInput.value.length == 0) {
                    options.forEach((option) => {
                        option.disabled = false;
                        option.classList.remove('hidden');

                        if (!selected) {
                            option.selected = true;
                            selected = true;
                        }

                    });
                } else {
                    searchFiliereFunction(searchFiliereInput.value);
                }
            });

            function confirmFiliere() {
                let options = Array.from(searchFiliere.options);
                let i = 0;

                options.forEach((option) => {
                    if (option.disabled) {
                        i++
                    }
                });

                if (options.length == i) {
                    alert('Veuillez selectionner une filière');
                    return false
                } else {
                    return true;
                }
            }  
        //

        //Tooltip manipulation
            let tooltipIcon = document.getElementById('tooltipIcon');
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

        //MODALS MANIPULATIONS


        //Manipulation boutons pour télécharger
        const importInput = document.getElementById('importInput');
        const formImport = document.getElementById('formImport');
        const importBtn = document.getElementById('import');

        importBtn.addEventListener('click', (event) => {
            event.preventDefault();
            importInput.click();
        });

        importInput.addEventListener('change', (event) => {
            let item = event.target;
            if (item.files && item.files.length > 0) {
                submitImport();
            }
        });

        function submitImport () {
            let result = confirm ("Voulez vous vraiment ajouter des utilisateurs avec un fichier excel ?");

            if (result) {
                formImport.submit();
            }
        }


        //////////////////////////
        //ADD MODALS
        //////////////////////////
        /////////////////////////////////Ouverture et Fermeture du modal
        const closeModalAdd = document.getElementById('closeModalAdd');
        const openModalAdd = document.getElementById('openModalAdd');
        const addModal = document.getElementById('addModal');

        const saveAddButton = document.getElementById('saveAddButton');
        const cancelAddButton = document.getElementById('cancelAddButton');

        const addFirstName = document.getElementById('addFirstName');
        const addLastName = document.getElementById('addLastName');
        const addEmail = document.getElementById('addEmail');
        const addPhone = document.getElementById('addPhone');
        const addRole = document.getElementById('addRole');
        const addSexe = document.getElementById('addSexe');
        const addFiliere = document.getElementById('addFiliere');
        const rowFiliereAdd = Array.from(document.getElementsByClassName('rowFiliereAdd'));

        const addsectorLists = document.getElementById('addsectorLists');
        const addListBtn = document.getElementById('addListBtn');

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
            const options = Array.from(addFiliere.options);

            options.forEach((option) => {
                option.disabled = false;
                option.selected = true;
                option.classList.remove('hidden');
            });
        }

        function rechercherAdd (value) {
            const options = Array.from(addFiliere.options);
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

        //Pour le changement de rôlé si changé alors redemerrage
        addRole.addEventListener('change', () => {
            let divs = Array.from(document.getElementsByClassName('divAdd'));
            
            if (addRole.value == "1") {
                rowFiliereAdd.forEach((row) => {
                    row.classList.remove('hidden');
                });

                addListBtn.classList.remove('hidden');

                divs.forEach((div) => {
                    div.classList.remove('hidden');
                })
            } else if (addRole.value == "2") {
                rowFiliereAdd.forEach((row) => {
                    row.classList.remove('hidden');
                });

                addListBtn.classList.add('hidden');
                
                divs.forEach((div) => {
                    reputElementsAdd(div);
                })
            } else {
                rowFiliereAdd.forEach((row) => {
                    row.classList.add('hidden');
                })

                divs.forEach((div) => {
                    reputElementsAdd(div);
                })
            }
        });

        function reputElementsAdd (row) {
            let level_id = row.parentNode.parentNode.querySelector('.addId').value;
            let value = row.parentNode.parentNode.querySelector('.valueSpan').textContent;

            let option = document.createElement('option');
            option.value = level_id;
            option.textContent = value;
                
            addFiliere.appendChild(option);

            row.remove();
        }

        //Pour les ajuouts des filières pour les profs et admin
        addListBtn.addEventListener('click', () => {
                if (addFiliere.options.length > 0 && addFiliere.value != '' && (addRole.value === "0" || addRole.value === "1")) {
                    let level_id = addFiliere.value;
                    let index = addFiliere.selectedIndex;
                    let value = addFiliere[index].textContent;

                    addFiliere.remove(index);

                    let div = document.createElement('div');
                    div.classList.add('w-full', 'flex', 'divAdd');

                    let input = document.createElement('input');
                    input.classList.add('hidden', 'addId');
                    input.setAttribute('name', 'levelIdAdd' + level_id)
                    input.value = level_id;

                    let span1 = document.createElement('span');
                    span1.classList.add('w-1/3');

                    let span = document.createElement('span');
                    span.innerHTML = value + '<i class="fas fa-times removedElementAdd cursor-pointer text-red-600 ml-3 text-lg"></i>';
                    span.classList.add('text-center', 'w-2/3', 'mr-3', 'valueSpan');

                    let span2 = document.createElement('span');
                    span2.classList.add('text-red-600', 'flex', 'text-lg', 'text-start', 'w-1/3');

                    let span3 = document.createElement('span');
                    span3.classList.add('flex-1')
                    
                    div.appendChild(input);
                    div.appendChild(span1);
                    div.appendChild(span);
                    div.appendChild(span2);

                    addsectorLists.appendChild(div);

                } else {
                    alert('C\'est vide');
                }
        });

        //Pour les retraits des filières pour les profs et admin
        addsectorLists.addEventListener('click', (event) => {
            if (event.target.classList.contains('removedElementAdd')) {
                let row = event.target.parentNode.parentNode;
                let level_id = event.target.parentNode.parentNode.querySelector('.addId').value;
                let value = event.target.parentNode.parentNode.querySelector('.valueSpan').textContent;

                let option = document.createElement('option');
                option.value = level_id;
                option.textContent = value;
                
                addFiliere.appendChild(option);

                row.remove();
            }
        });

        function resetValuesAddModal() {
            addModal.classList.add('hidden');

            addFirstName.value = '';
            addLastName.value = '';
            addEmail.value = '';
            addPhone.value = '';
            
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



        //////////////////////////
        //VIEW MODALS
        //////////////////////////
        const closeModalView = document.getElementById('closeModalView');
        const openModalView = Array.from(document.getElementsByClassName('openModalView'));
        const viewModal = document.getElementById('viewModal');
;
        const viewFirstName = document.getElementById('viewFirstName');
        const viewLastName = document.getElementById('viewLastName');
        const viewEmail = document.getElementById('viewEmail');
        const viewPhone = document.getElementById('viewPhone');
        const viewSexe = document.getElementById('viewSexe');
        const viewRole = document.getElementById('viewRole');
        const viewFiliere = document.getElementById('viewRole');

        const viewsectorLists = document.getElementById('viewsectorLists');

        function resetValuesViewModal() {
            viewFirstName.value = '';
            viewLastName.value = '';
            viewEmail.value = '';
            viewPhone.value = '';

            window.location.reload();
            viewModal.classList.add('hidden');
        }
        
        //Fermeture modal en haut à droite
        closeModalView.addEventListener('click', () => {
            resetValuesViewModal();
        });
        

        //Ouverture du modal avec le bouton +
        openModalView.forEach((btn) => {
            btn.addEventListener('click', () => {
                id = parseInt(btn.parentNode.querySelector('.id').textContent);
                
                fetch('users/getUser/' + id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network resonse was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let first_name = data.first_name;
                    let last_name = data.last_name;
                    let email = data.email;
                    let phone = data.phone;
                    let role = data.role;

                    let sexes = viewSexe.querySelectorAll('option');
                    sexes.forEach((sexe) => {
                        if (sexe.value == data.sexe) {
                            sexe.selected = true;
                        }
                    });

                     viewFirstName.value = first_name;
                     viewLastName.value = last_name;
                     viewEmail.value = email;
                     viewPhone.value = phone;
                     viewRole.value = role;
                
                    

                    let levels = data.levels_users;

                     //Ajout de la liste de niveau
                    levels.forEach((level) => {
                        
                        levelName = level.level.name;
                        sectorName = level.level.sector.name;
                        let span = document.createElement('span');
                        span.textContent = sectorName +': '+ levelName;

                        viewsectorLists.appendChild(span);                          
                    });

                    if (levels.length === 0) {
                        let span = document.createElement('span');
                        span.textContent = 'Aucune filière';
                        viewsectorLists.appendChild(span);
                    }

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
        const editFirstName = document.getElementById('editFirstName');
        const editLastName = document.getElementById('editLastName');
        const editEmail = document.getElementById('editEmail');
        const editPhone = document.getElementById('editPhone');
        const editRole = document.getElementById('editRole');
        const editSexe = document.getElementById('editSexe');
        const editFiliere = document.getElementById('editFiliere');

        const editsectorLists = document.getElementById('editsectorLists');
        const editListBtn = document.getElementById('editListBtn');

        var searchEdit = document.getElementById('searchEdit');

        function resetValuesEditModal() {
            editFirstName.value = '';
            editLastName.value = '';
            editEmail.value = '';
            editPhone.value = '';

            editModal.classList.add('hidden');

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

        //Faire une recherche dynamique
        searchEdit.addEventListener('input', (event) => {
            value = event.target.value;
            remettreNormalEdit();
            if (value != '') {
                rechercherEdit(value);
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

        function rechercherEdit (value) {
            const options = Array.from(editFiliere.options);
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

            if (countDesactivated == options.length) {
            }
        }

        //Pour le changement de rôlé si changé alors redemerrage
        editRole.addEventListener('change', () => {
            let divs = Array.from(document.getElementsByClassName('divEdit'));

            if (editRole.value === "0" || editRole.value === "1") {
                editListBtn.classList.remove('hidden');

            } else {
                editListBtn.classList.add('hidden');

                divs.forEach((div) => {
                    reputElementsEdit(div);
                })
            }
        });

        function reputElementsEdit (row) {
                let level_id = row.querySelector('.editId').value;
                let value = row.querySelector('.valueSpan').textContent;

                let option = document.createElement('option');
                option.value = level_id;
                option.textContent = value;
                    
                editFiliere.appendChild(option);

                row.remove();
        }

        //Ouverture du modal avec le bouton +
        openModalEdit.forEach((btn) => {
            btn.addEventListener('click', () => {
                id = parseInt(btn.parentNode.querySelector('.id').textContent);
                
                fetch('users/getUser/' + id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let first_name = data.first_name;
                    let last_name = data.last_name;
                    let email = data.email;
                    let phone = data.phone;
                    let role = data.role;

                    let sexes = editSexe.querySelectorAll('option');
                    sexes.forEach((sexe) => {
                        if (sexe.value == data.sexe) {
                            sexe.selected = true;
                        }
                    });


                    if (role == 0) {
                        editRole.parentNode.remove();
                        editFiliere.parentNode.remove();
                    }

                    let options = Array.from(document.getElementsByClassName('editOptions'));

                     editFirstName.value = first_name;
                     editLastName.value = last_name;
                     editEmail.value = email;
                     editPhone.value = phone;
                     editRole.value = role;

                     let levels = data.levels_users;

                     levels.forEach((level) => {
                        level = level.level;

                        if (editRole.value == 2) {
                            options.forEach((option) => {
                                if (level.id == option.value) {
                                    option.selected = true;
                                }
                            })

                            searchEdit.classList.add('hidden');
                            editListBtn.classList.add('hidden');
                        } else {
                            options.forEach((option) => {
                                if (level.id == option.value) {
                                    option.remove();
                                }
                            })

                            searchEdit.classList.remove('hidden');
                            editListBtn.classList.remove('hidden');

                            let level_id = level.id;
                            let value = level.sector.name + ': ' + level.name;

                            let div = document.createElement('div');
                            div.classList.add('w-full', 'flex', 'divEdit');

                            let input = document.createElement('input');
                            input.classList.add('hidden', 'editId');
                            input.setAttribute('name', 'levelIdEdit' + level_id)
                            input.value = level_id;

                            let div2 = document.createElement('div');
                            div2.classList.add('flex', 'justify-center', 'bg-red', 'space-x-3', 'flex-1');

                            let span = document.createElement('span');
                            span.textContent = value;
                            span.classList.add('text-end', 'mr-3', 'valueSpan', 'flex', 'justify-end');

                            let span2 = document.createElement('span');
                            span2.innerHTML = '<i class="fas fa-times removedElementEdit cursor-pointer"></i>';
                            span2.classList.add('text-red-600', 'flex', 'text-lg', 'items-center');

                            let span3 = document.createElement('span');
                            span3.classList.add('w-1/4');

                            let span4 = document.createElement('span');
                            span4.classList.add('w-1/4');
                            
                            div2.appendChild(span);
                            div2.appendChild(span2);
                            div2.appendChild(input);
                            
                            div.appendChild(span3);
                            div.appendChild(div2);
                            div.appendChild(span4);

                            editsectorLists.appendChild(div);
                        }
                     });

                     editId.value = data.id;

                     editModal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error)
                });
            });
        });

        //Pour les retraits des filières pour les profs et admin
        editsectorLists.addEventListener('click', (event) => {
            if (event.target.classList.contains('removedElementEdit')) {
                let row = event.target.parentNode.parentNode;
                let level_id = event.target.parentNode.parentNode.querySelector('.editId').value;
                let value = event.target.parentNode.parentNode.querySelector('.valueSpan').textContent;

                let option = document.createElement('option');
                option.value = level_id;
                option.textContent = value;
                    
                editFiliere.appendChild(option);

                row.remove();
            }
        });

        //Pour les ajuouts des filières pour les profs et admin
        editListBtn.addEventListener('click', () => {
            if (editFiliere.options.length > 0 && editFiliere.value != '' && (editRole.value === "0" || editRole.value === "1")) {
                    let level_id = editFiliere.value;
                    let index = editFiliere.selectedIndex;
                    let value = editFiliere[index].textContent;

                    editFiliere.remove(index);

                    let div = document.createElement('div');
                    div.classList.add('w-full', 'flex', 'divEdit');

                    let input = document.createElement('input');
                    input.classList.add('hidden', 'editId');
                    input.setAttribute('name', 'levelIdEdit' + level_id)
                    input.value = level_id;

                    let div2 = document.createElement('div');
                    div2.classList.add('flex', 'justify-center', 'bg-red', 'space-x-3', 'flex-1');

                    let span = document.createElement('span');
                    span.textContent = value;
                    span.classList.add('text-end', 'mr-3', 'valueSpan', 'flex', 'justify-end');

                    let span2 = document.createElement('span');
                    span2.innerHTML = '<i class="fas fa-times removedElementEdit cursor-pointer"></i>';
                    span2.classList.add('text-red-600', 'flex', 'text-lg', 'items-center');

                    let span3 = document.createElement('span');
                    span3.classList.add('w-1/4');

                    let span4 = document.createElement('span');
                    span4.classList.add('w-1/4');
                            
                    div2.appendChild(span);
                    div2.appendChild(span2);
                    div2.appendChild(input);
                            
                    div.appendChild(span3);
                    div.appendChild(div2);
                    div.appendChild(span4);

                    editsectorLists.appendChild(div);
            } else {
                    alert('C\'est vide');
            }
        });



        //LES ID
        const informations = document.getElementById('informations');
        const prenom = document.getElementById('prenom');
        const nom = document.getElementById('nom');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');

        //Les Class
        const c_informations = Array.from(document.getElementsByClassName('informations'));
        const c_prenom =  Array.from(document.getElementsByClassName('prenom'));
        const c_nom =  Array.from(document.getElementsByClassName('nom'));
        const c_email =  Array.from(document.getElementsByClassName('email'));
        const c_phone =  Array.from(document.getElementsByClassName('phone'));

        const changeTaille = document.getElementById('changeTaille');

        var height;


        function media_change () {
            // Inférieur à 768px
            if (mediaQuery.matches) {
                //ID
                informations.classList.remove('hidden');
                prenom.classList.add('hidden');
                nom.classList.add('hidden');
                email.classList.add('hidden');
                phone.classList.add('hidden');

                if (changeTaille) {
                    changeTaille.setAttribute('colspan', "3");
                }

                //CLASSES
                c_informations.forEach((value, index) => {
                    value.classList.remove('hidden');
                    height = value.offsetHeight;
                });
                c_prenom.forEach((value, index) => {
                    value.classList.add('hidden');
                });
                c_nom.forEach((value, index) => {
                    value.classList.add('hidden');
                });
                c_email.forEach((value, index) => {
                    value.classList.add('hidden');
                });
                c_phone.forEach((value, index) => {
                    value.classList.add('hidden');
                });
            // Supérieur à 768px
            } else {
                //ID
                informations.classList.add('hidden');
                prenom.classList.remove('hidden');
                nom.classList.remove('hidden');
                email.classList.remove('hidden');
                phone.classList.remove('hidden');

                if (changeTaille) {
                    changeTaille.setAttribute('colspan', "7");
                }

                //CLASSES
                c_informations.forEach((value, index) => {
                    value.classList.add('hidden');
                });
                c_prenom.forEach((value, index) => {
                    value.classList.remove('hidden');
                    height = value.offsetHeight;
                });
                c_nom.forEach((value, index) => {
                    value.classList.remove('hidden');
                });
                c_email.forEach((value, index) => {
                    value.classList.remove('hidden');
                });
                c_phone.forEach((value, index) => {
                    value.classList.remove('hidden');
                });
            }
        }

        mediaQuery.addEventListener('change', (event) => {
            media_change();
        });

        media_change();

        const tableUser = document.getElementById('tableUser');
        const toutLesDiv = tableUser.querySelectorAll('.tdDivs');
        
        toutLesDiv.forEach((div) => {
            div.style.minHeight = height + 'px';
        })
    </script>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        var pusher = new Pusher('6979301f0eee4d497b90', {
            cluster: 'eu'
        });

        var channel = pusher.subscribe('user-channel');

        channel.bind('user-refresh', async function (data) {
            location.reload();
        });
    </script>

@endsection