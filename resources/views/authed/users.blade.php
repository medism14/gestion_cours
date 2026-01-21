@extends('Layouts.authed')

@section('title', 'Utilisateurs')

@section('content')
<div class="section-animate space-y-8 p-4 md:p-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Gestion des Utilisateurs</h1>
            <p class="mt-1 text-gray-500">Administrez les comptes des étudiants, professeurs et responsables.</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="openModalAdd" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-md transform hover:-translate-y-0.5">
                <i class="fa-solid fa-user-plus mr-2 text-sm"></i>
                Ajouter un utilisateur
            </button>
        </div>
    </div>

    <!-- Stats Overview (Optional but premium touch) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ count($allUsers) }}</h3>
                </div>
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Filter by Level -->
                <form action="{{ route('users.index') }}" method="GET" class="space-y-4">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-layer-group text-xs"></i>
                            </div>
                            <select name="searchFiliere" id="searchFiliere" 
                                class="block w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none bg-gray-50/50">
                                @if (!$levels->isEmpty())
                                    <option value="all">Toutes les filières</option>
                                @endif
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" {{ request('searchFiliere') == $level->id ? 'selected' : '' }}>
                                        {{ $level->sector->name }}: {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white font-medium rounded-xl hover:bg-gray-800 transition-all">
                            Filtrer
                        </button>
                    </div>
                </form>

                <!-- Global Search -->
                <form action="{{ route('users.index') }}" method="GET" class="space-y-4">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1 group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>
                            <input id="search" name="search" type="text" value="{{ request('search') }}"
                                class="block w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50/50"
                                placeholder="Nom, Email, Téléphone...">
                            
                            <!-- Tooltip replacement: a simple info button -->
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <div class="relative group/tooltip">
                                    <i class="fa-solid fa-circle-question text-gray-300 hover:text-blue-500 cursor-help transition-colors text-sm"></i>
                                    <div class="pointer-events-none absolute bottom-full right-0 mb-3 w-56 p-3 bg-gray-900 text-white text-[10px] rounded-xl opacity-0 invisible group-hover/tooltip:opacity-100 group-hover/tooltip:visible transition-all duration-200 shadow-2xl z-[100] transform group-hover/tooltip:translate-y-0 translate-y-1">
                                        <p class="font-bold mb-1.5 text-blue-400 uppercase tracking-wider text-[9px]">Recherche intelligente</p>
                                        <p class="text-gray-300 leading-relaxed">Recherchez par prénom, nom, email, téléphone ou filière.</p>
                                        <div class="absolute -bottom-2 right-3 w-0 h-0 border-l-[6px] border-r-[6px] border-t-[8px] border-l-transparent border-r-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                            Rechercher
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Toolbar -->
        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <form action="{{ route('users.download') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="users" value="{{ json_encode($allUsers) }}">
                    <button id="download" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-sm font-semibold text-emerald-600 rounded-xl hover:bg-emerald-50 transition-all shadow-sm">
                        <i class="fa-solid fa-file-export mr-2"></i>
                        Exporter CSV
                    </button>
                </form>

                <form id="formImport" action="{{ route('users.importCSV') }}" method="POST" enctype="multipart/form-data" class="inline">
                    @csrf
                    <input id="importInput" name="fichier" type="file" class="hidden" onchange="if(confirm('Importer ce fichier ?')) this.form.submit()">
                    <button type="button" onclick="document.getElementById('importInput').click()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-sm font-semibold text-blue-600 rounded-xl hover:bg-blue-50 transition-all shadow-sm">
                        <i class="fa-solid fa-file-import mr-2"></i>
                        Importer CSV
                    </button>
                </form>
            </div>
            @if ($loup)
                <a href="{{ route('users.index') }}" class="text-xs font-bold text-amber-600 hover:underline">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Réinitialiser les filtres
                </a>
            @endif
        </div>

        <!-- Desktop Table -->
        <div class="overflow-x-auto">
            <table id="tableUser" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Identité</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Contact</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center font-bold text-xs ring-2 ring-white shadow-sm">
                                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-600 font-medium">{{ $user->phone }}</span>
                                    <span class="text-[10px] text-gray-400">ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $roleLabel = $user->role == 0 ? 'Admin' : ($user->role == 1 ? 'Professeur' : 'Étudiant');
                                    $roleClass = $user->role == 0 ? 'bg-rose-50 text-rose-600' : ($user->role == 1 ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600');
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $roleClass }}">
                                    {{ $roleLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button title="Voir les détails" class="openModalView p-2.5 text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm" data-id="{{ $user->id }}">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </button>
                                    <button title="Modifier" class="openModalEdit p-2.5 text-amber-600 bg-amber-50 hover:bg-amber-600 hover:text-white rounded-xl transition-all shadow-sm" data-id="{{ $user->id }}">
                                        <i class="fa-solid fa-user-pen text-sm"></i>
                                    </button>
                                    <form method="POST" action="{{ route('users.delete', ['id' => $user->id]) }}" class="contents" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button title="Supprimer" class="p-2.5 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white rounded-xl transition-all shadow-sm">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($users->isEmpty())
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-user-slash text-4xl text-gray-200 mb-4"></i>
                                    <p class="text-gray-400 font-medium">Aucun utilisateur trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection

@section('modals')
    <!-- MODALS -->
    <!-- Add Modal -->
    <div id="addModal" class="hidden fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <form method="POST" action="{{ route('users.store') }}" onsubmit="return submitFunction()">    
                    @csrf
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h3 class="text-xl font-black text-gray-900">Nouvel Utilisateur</h3>
                        <button type="button" id="closeModalAdd" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-white transition-all">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Prénom</label>
                                <input name="addFirstName" required type="text" placeholder="ex: Jean"
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nom</label>
                                <input name="addLastName" required type="text" placeholder="ex: Dupont"
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Email professionnel</label>
                            <input name="addEmail" required type="email" placeholder="jean.dupont@ismd.com"
                                class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Téléphone</label>
                                <input name="addPhone" type="text" placeholder="06 12 34 56 78"
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Rôle</label>
                                <select id="addRole" name="addRole" 
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm appearance-none cursor-pointer">
                                    <option value="2">Étudiant</option>
                                    <option value="1">Professeur</option>
                                    <option value="0">Administrateur</option>
                                </select>
                            </div>
                        </div>

                        <div id="divSelectFiliere" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Filière & Niveau</label>
                                <button type="button" id="addListBtn" class="px-3 py-1 bg-gray-900 text-white text-[10px] font-bold rounded-lg hover:bg-gray-800 transition-all">
                                    <i class="fa-solid fa-plus mr-1"></i> Ajouter
                                </button>
                            </div>
                            <div class="relative">
                                <select id="addFiliere" name="addFiliere" 
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm appearance-none cursor-pointer">
                                    <option value="">Sélectionnez un niveau...</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}">{{ $level->sector->name }}: {{ $level->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                            <!-- Selected levels list -->
                            <div id="addsectorLists" class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar pr-2">
                                <!-- Dynamic dynamic -->
                            </div>
                        </div>
                    </div>

                    <div class="p-8 bg-gray-50/50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" id="cancelAddButton" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-all">
                            Annuler
                        </button>
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5">
                            Créer le compte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="hidden fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                <div class="px-8 py-10">
                    <div class="flex flex-col items-center text-center">
                        <div class="h-24 w-24 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center text-3xl font-black mb-4 shadow-inner ring-4 ring-gray-50">
                            <span id="viewInitiales">??</span>
                        </div>
                        <h3 id="viewFullName" class="text-2xl font-black text-gray-900">Chargement...</h3>
                        <span id="viewRoleBadge" class="mt-2 px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-widest rounded-full">Rôle</span>
                    </div>

                    <div class="mt-10 space-y-4">
                        <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100/50">
                            <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center text-blue-600 shadow-sm mr-4">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email</p>
                                <p id="viewEmail" class="text-sm font-semibold text-gray-900 underline decoration-blue-200">...</p>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100/50">
                            <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center text-emerald-600 shadow-sm mr-4">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Téléphone</p>
                                <p id="viewPhone" class="text-sm font-semibold text-gray-900">...</p>
                            </div>
                        </div>

                        <div id="viewFiliereContainer" class="p-4 bg-gray-50 rounded-2xl border border-gray-100/50">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center">
                                <i class="fa-solid fa-graduation-cap mr-2"></i> Filières & Niveaux
                            </p>
                            <div id="viewsectorLists" class="grid grid-cols-1 gap-2">
                                <!-- Dynamic levels -->
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-center">
                        <button type="button" id="closeModalView" class="px-10 py-3 bg-gray-900 text-white font-bold rounded-2xl hover:bg-gray-800 transition-all shadow-xl hover:shadow-gray-300 transform hover:-translate-y-0.5">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <form method="POST" action="{{ route('users.edit') }}" onsubmit="return submitFunction()">    
                    @csrf
                    <input type="hidden" name="id" id="editId">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h3 class="text-xl font-black text-gray-900">Modifier l'Utilisateur</h3>
                        <button type="button" id="closeModalEdit" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-white transition-all">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Prénom</label>
                                <input id="editFirstName" name="editFirstName" required type="text"
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nom</label>
                                <input id="editLastName" name="editLastName" required type="text"
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Email professionnel</label>
                            <input id="editEmail" name="editEmail" required type="email"
                                class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Téléphone</label>
                                <input id="editPhone" name="editPhone" type="text"
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Rôle</label>
                                <select id="editRole" name="editRole" 
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold appearance-none cursor-pointer">
                                    <option value="2">Étudiant</option>
                                    <option value="1">Professeur</option>
                                    <option value="0">Administrateur</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nouveau mot de passe <span class="text-gray-400 font-normal">(laisser vide pour ne pas changer)</span></label>
                            <div class="relative">
                                <input id="editPassword" name="editPassword" type="password" placeholder="••••••••" autocomplete="new-password"
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold pr-12">
                                <button type="button" onclick="togglePasswordVisibility('editPassword', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div id="divSelectFiliereEdit" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Filières & Niveaux</label>
                                <button type="button" id="editListBtn" class="px-3 py-1 bg-amber-900 text-white text-[10px] font-bold rounded-lg hover:bg-amber-800 transition-all">
                                    <i class="fa-solid fa-plus mr-1"></i> Ajouter
                                </button>
                            </div>
                            <div class="relative">
                                <select id="editFiliere" name="editFiliere" 
                                    class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold appearance-none cursor-pointer">
                                    <option value="">Sélectionnez un niveau...</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}" class="editOptions">{{ $level->sector->name }}: {{ $level->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                            <!-- Selected levels list -->
                            <div id="editsectorLists" class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar pr-2">
                                <!-- Dynamic dynamic -->
                            </div>
                        </div>
                    </div>

                    <div class="p-8 bg-gray-50/50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" id="cancelEditButton" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-all">
                            Annuler
                        </button>
                        <button type="submit" class="px-8 py-3 bg-amber-600 text-white font-bold rounded-2xl hover:bg-amber-700 shadow-lg shadow-amber-500/20 transition-all transform hover:-translate-y-0.5">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    (function() {
        // Modal management
        function toggleModal(id, show) {
            const modal = document.getElementById(id);
            if (show) modal.classList.remove('hidden');
            else modal.classList.add('hidden');
        }

        // --- ADD MODAL LOGIC ---
        const addRole = document.getElementById('addRole');
        const addFiliere = document.getElementById('addFiliere');
        const addListBtn = document.getElementById('addListBtn');
        const addsectorLists = document.getElementById('addsectorLists');

        // Show/hide filiere selector based on role
        if (addRole) {
            addRole.addEventListener('change', () => {
                const isStudent = addRole.value === "2";
                addsectorLists.innerHTML = '';
                if (isStudent) {
                    addListBtn.classList.add('hidden');
                } else {
                    addListBtn.classList.remove('hidden');
                }
            });
        }

        if (addListBtn) {
            addListBtn.addEventListener('click', () => {
                if (!addFiliere.value) return;
                const id = addFiliere.value;
                const name = addFiliere.options[addFiliere.selectedIndex].text;
                if (document.getElementById(`level_add_${id}`)) return;

                const div = document.createElement('div');
                div.id = `level_add_${id}`;
                div.className = "flex items-center justify-between p-2.5 bg-blue-50/50 rounded-xl border border-blue-100/50 group/item";
                div.innerHTML = `
                    <span class="text-xs font-bold text-blue-900">${name}</span>
                    <input type="hidden" name="levelIdAdd${id}" value="${id}">
                    <button type="button" onclick="this.parentElement.remove()" class="p-1.5 text-blue-300 hover:text-red-500 transition-all opacity-0 group-hover/item:opacity-100">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                addsectorLists.appendChild(div);
                addFiliere.value = '';
            });
        }

        document.getElementById('closeModalAdd').onclick = () => toggleModal('addModal', false);
        document.getElementById('cancelAddButton').onclick = () => toggleModal('addModal', false);
        document.getElementById('openModalAdd').onclick = () => toggleModal('addModal', true);

        // --- VIEW MODAL LOGIC ---
        document.querySelectorAll('.openModalView').forEach(btn => {
            btn.onclick = async () => {
                const id = btn.dataset.id;
                try {
                    const res = await fetch(`/users/getUser/${id}`);
                    const user = await res.json();
                    
                    document.getElementById('viewInitiales').textContent = (user.first_name[0] + user.last_name[0]).toUpperCase();
                    document.getElementById('viewFullName').textContent = `${user.first_name} ${user.last_name}`;
                    document.getElementById('viewEmail').textContent = user.email;
                    document.getElementById('viewPhone').textContent = user.phone || 'Non renseigné';
                    
                    const roles = {0: 'Administrateur', 1: 'Professeur', 2: 'Étudiant'};
                    document.getElementById('viewRoleBadge').textContent = roles[user.role];

                    const list = document.getElementById('viewsectorLists');
                    list.innerHTML = '';
                    user.levels_users.forEach(lu => {
                        const div = document.createElement('div');
                        div.className = "p-3 bg-white border border-gray-100 rounded-xl text-xs font-bold text-gray-700 shadow-sm";
                        div.textContent = `${lu.level.sector.name}: ${lu.level.name}`;
                        list.appendChild(div);
                    });
                    
                    if (user.levels_users.length === 0) {
                        list.innerHTML = '<p class="text-xs text-gray-400 italic">Aucune filière assignée</p>';
                    }

                    toggleModal('viewModal', true);
                } catch (err) { console.error(err); }
            };
        });
        document.getElementById('closeModalView').onclick = () => toggleModal('viewModal', false);

        // --- EDIT MODAL LOGIC ---
        const editRole = document.getElementById('editRole');
        const editFiliere = document.getElementById('editFiliere');
        const editListBtn = document.getElementById('editListBtn');
        const editsectorLists = document.getElementById('editsectorLists');

        if (editRole) {
            editRole.addEventListener('change', () => {
                if (editRole.value === "2") editListBtn.classList.add('hidden');
                else editListBtn.classList.remove('hidden');
            });
        }

        if (editListBtn) {
            editListBtn.addEventListener('click', () => {
                if (!editFiliere.value) return;
                const id = editFiliere.value;
                const name = editFiliere.options[editFiliere.selectedIndex].text;
                if (document.getElementById(`level_edit_${id}`)) return;

                const div = document.createElement('div');
                div.id = `level_edit_${id}`;
                div.className = "flex items-center justify-between p-2.5 bg-amber-50/50 rounded-xl border border-amber-100/50 group/item";
                div.innerHTML = `
                    <span class="text-xs font-bold text-amber-900">${name}</span>
                    <input type="hidden" name="levelIdEdit${id}" value="${id}">
                    <button type="button" onclick="this.parentElement.remove()" class="p-1.5 text-amber-300 hover:text-red-500 transition-all opacity-0 group-hover/item:opacity-100">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                editsectorLists.appendChild(div);
                editFiliere.value = '';
            });
        }

        document.querySelectorAll('.openModalEdit').forEach(btn => {
            btn.onclick = async () => {
                const id = btn.dataset.id;
                try {
                    const res = await fetch(`/users/getUser/${id}`);
                    const user = await res.json();
                    
                    document.getElementById('editId').value = user.id;
                    document.getElementById('editFirstName').value = user.first_name;
                    document.getElementById('editLastName').value = user.last_name;
                    document.getElementById('editEmail').value = user.email;
                    document.getElementById('editPhone').value = user.phone || '';
                    document.getElementById('editRole').value = user.role;
                    
                    if (user.role == 2 && editListBtn) editListBtn.classList.add('hidden');
                    else if (editListBtn) editListBtn.classList.remove('hidden');

                    editsectorLists.innerHTML = '';
                    user.levels_users.forEach(lu => {
                        const levelId = lu.level.id;
                        const levelName = `${lu.level.sector.name}: ${lu.level.name}`;
                        
                        const div = document.createElement('div');
                        div.id = `level_edit_${levelId}`;
                        div.className = "flex items-center justify-between p-2.5 bg-amber-50/50 rounded-xl border border-amber-100/50 group/item";
                        div.innerHTML = `
                            <span class="text-xs font-bold text-amber-900">${levelName}</span>
                            <input type="hidden" name="levelIdEdit${levelId}" value="${levelId}">
                            <button type="button" onclick="this.parentElement.remove()" class="p-1.5 text-amber-300 hover:text-red-500 transition-all opacity-0 group-hover/item:opacity-100">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        `;
                        editsectorLists.appendChild(div);
                    });
                    toggleModal('editModal', true);
                } catch (err) { console.error(err); }
            };
        });
        document.getElementById('closeModalEdit').onclick = () => toggleModal('editModal', false);
        document.getElementById('cancelEditButton').onclick = () => toggleModal('editModal', false);
    })();

    // Toggle password visibility
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    const pusher = new Pusher('6979301f0eee4d497b90', { cluster: 'eu' });
    const channel = pusher.subscribe('user-channel');
    channel.bind('user-refresh', () => location.reload());
</script>
@endsection