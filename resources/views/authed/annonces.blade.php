@extends('Layouts.authed')

@section('title', 'Annonces')

@section('content')
<div class="section-animate space-y-8 p-4 md:p-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Communiqués & Annonces</h1>
            <p class="mt-1 text-gray-500">Informations importantes et actualités de l'établissement.</p>
        </div>
        @if (auth()->user()->role != 2)
            <button id="openModalAdd" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 group">
                <i class="fa-solid fa-plus mr-2 group-hover:rotate-90 transition-transform"></i>
                Nouvelle annonce
            </button>
        @endif
    </div>

    <!-- Stats/Quick Info (Optional but adds premium feel) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-bullhorn text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Annonces</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $annonces->total() }}</p>
                </div>
            </div>
        </div>
        <!-- Add more stats if needed -->
    </div>

    <!-- Search & Filter Area -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('annonces.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            @csrf
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                </div>
                <input id="search" name="search" type="text" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    placeholder="Rechercher par titre, annonceur ou filière...">
                <div id="tooltipIcon" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-help">
                    <i class="fa-solid fa-circle-question text-gray-300 hover:text-blue-500 transition-colors"></i>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-8 py-2.5 bg-gray-900 text-white font-medium rounded-xl hover:bg-gray-800 transition-all">
                    Filtrer
                </button>
                @if(request('search') || $loup)
                    <a href="{{ route('annonces.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 font-medium rounded-xl hover:bg-gray-200 transition-all text-center">
                        Réinitialiser
                    </a>
                @endif
            </div>

            <div id="tooltipInfo" class="hidden absolute z-50 p-4 bg-gray-900 text-white text-xs rounded-xl shadow-xl max-w-xs mt-12 border border-gray-800">
                <p class="font-bold mb-2 text-blue-400">Conseil de recherche :</p>
                <p class="leading-relaxed opacity-80">
                    Vous pouvez rechercher par le titre de l'annonce, le nom de l'enseignant ou même une filière spécifique.
                </p>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Titre de l'annonce</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Posté par</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($annonces as $annonce)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mr-4">
                                        <i class="fa-solid fa-message-quote text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-gray-900">{{ $annonce->title }}</span>
                                        <span class="block text-[10px] text-gray-400 font-medium tracking-tighter">{{ $annonce->created_at->format('d M Y à H:i') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $annonce->user->role == 0 ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-blue-600' }}">
                                    <i class="fa-solid {{ $annonce->user->role == 0 ? 'fa-shield-halved' : 'fa-chalkboard-user' }} mr-1.5 text-[8px]"></i>
                                    {{ $annonce->user->role == 0 ? 'Administration' : 'Enseignant' }}
                                    <span class="ml-1 opacity-70"> • {{ $annonce->user->first_name }} {{ $annonce->user->last_name }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="id hidden">{{ $annonce->id }}</span>
                                    
                                    @if (auth()->user()->role == 2 || (auth()->user()->role == 1 && $annonce->user_id != auth()->id()))
                                        <!-- View only for students or other teachers -->
                                        <button title="Voir l'annonce" class="openModalView2 p-2.5 text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </button>
                                    @else
                                        <!-- Full actions for Owners/Admins -->
                                        <button title="Voir les détails" class="openModalView p-2.5 text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </button>
                                        <button title="Modifier" class="openModalEdit p-2.5 text-amber-600 bg-amber-50 hover:bg-amber-600 hover:text-white rounded-xl transition-all shadow-sm">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </button>
                                        <form method="POST" action="{{ route('annonces.delete', ['id' => $annonce->id]) }}" onsubmit="return confirm('Supprimer cette annonce ?')" class="contents">
                                            @csrf @method('DELETE')
                                            <button title="Supprimer" class="p-2.5 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white rounded-xl transition-all shadow-sm">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500 italic">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-inbox text-4xl text-gray-200 mb-3"></i>
                                    Aucune annonce trouvée.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($annonces->hasPages())
        <div class="mt-8">
            {{ $annonces->links() }}
        </div>
    @endif
</div>

<!-- ========================================================================= -->
<!-- MODALS -->
<!-- ========================================================================= -->

@if (auth()->user()->role != 2)
<!-- Generic Modal Structure for Add/Edit (Keeping IDs for JS compatibility) -->
<div id="addModal" class="hidden fixed inset-0 z-[100] bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div id="subAddModal" class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <form method="POST" action="{{ route('annonces.store') }}" onsubmit="return submitFunction()" class="flex flex-col h-full">
            @csrf
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Nouvelle Annonce</h3>
                    <p class="text-sm text-gray-500">Remplissez les informations pour publier.</p>
                </div>
                <button type="button" id="closeModalAdd" class="h-10 w-10 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-all text-gray-400"><i class="fa-solid fa-times"></i></button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-6">
                <!-- Row 1: Title & Exp Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 uppercase tracking-wider ml-1">Titre de l'annonce</label>
                        <input id="addTitle" name="addTitle" type="text" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 uppercase tracking-wider ml-1">Date d'expiration</label>
                        <input id="addDateExpiration" name="addDateExpiration" type="date" required min="{{ date('Y-m-d', strtotime('tomorrow')) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm">
                    </div>
                </div>

                <!-- Row 2: Targets Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="space-y-4 p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                        <label class="text-sm font-bold text-blue-900 uppercase tracking-wider ml-1 block">Filières ciblées</label>
                        <div class="flex space-x-2">
                            <div class="relative flex-1">
                                <input type="text" id="searchAdd" placeholder="Filtrer les filières..." class="w-full px-3 py-2 bg-white border border-blue-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <select name="addFiliere" id="addFiliere" class="flex-1 px-3 py-2 bg-white border border-blue-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                                @if (auth()->user()->role == 0) <option value="all" class="addFilieres">Toutes les filières</option> @endif
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" class="addFilieres">{{ $level->sector->name}}: {{$level->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="filiereAdd" class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-sm">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 uppercase tracking-wider ml-1">Public visé</label>
                        <select name="addPersonnes" id="addPersonnes" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm">
                            @if (auth()->user()->role == 0)
                                <option value="all">Tout le monde</option>
                                <option value="teachers">Professeurs uniquement</option>
                            @endif
                            <option value="students" {{ auth()->user()->role != 0 ? 'selected' : '' }}>Étudiants uniquement</option>
                        </select>
                    </div>
                </div>

                <!-- Selected Filières Display -->
                <div id="divDisplayListAdd" class="bg-gray-50 p-4 rounded-2xl space-y-3">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Liste des filières sélectionnées :</label>
                    <div id="listFilieres" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <!-- Filled by JS -->
                        <p class="text-gray-400 text-xs italic">Aucune filière sélectionnée (Toutes par défaut)</p>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 uppercase tracking-wider ml-1">Contenu de l'annonce</label>
                    <textarea name="addContenu" id="addContenu" rows="6" maxlength="1000" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm resize-none" placeholder="Rédigez ici le message de votre annonce..."></textarea>
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex justify-end space-x-3">
                <button type="button" id="cancelAddButton" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-all">Annuler</button>
                <button type="submit" id="saveAddButton" class="px-8 py-2.5 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">Publier l'annonce</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL (Reusing structure for consistency) -->
<div id="editModal" class="hidden fixed inset-0 z-[100] bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div id="subEditModal" class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <form method="POST" action="{{ route('annonces.edit') }}" onsubmit="return submitFunction()" class="flex flex-col h-full">
            @csrf
            <input type="hidden" id="editId" name="id">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Modifier l'Annonce</h3>
                    <p class="text-sm text-gray-500">Mettez à jour les informations de votre communiqué.</p>
                </div>
                <button type="button" id="closeModalEdit" class="h-10 w-10 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-all text-gray-400"><i class="fa-solid fa-times"></i></button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 uppercase tracking-wider ml-1">Titre</label>
                        <input id="editTitle" name="editTitle" type="text" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 uppercase tracking-wider ml-1">Date d'expiration</label>
                        <input id="editDateExpiration" name="editDateExpiration" type="date" required min="{{ date('Y-m-d', strtotime('tomorrow')) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="space-y-4 p-4 bg-amber-50/50 rounded-2xl border border-amber-100">
                        <label class="text-sm font-bold text-amber-900 uppercase tracking-wider ml-1 block">Filières ciblées</label>
                        <input type="text" id="searchEdit" placeholder="Filtrer..." class="w-full px-3 py-2 bg-white border border-amber-200 rounded-lg text-xs outline-none">
                        <div class="flex items-center space-x-2">
                            <select name="editFiliere" id="editFiliere" class="flex-1 px-3 py-2 bg-white border border-amber-200 rounded-lg text-xs">
                                @if (auth()->user()->role == 0) <option value="all" class="editFilieres">Toutes les filières</option> @endif
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}" class="editFilieres">{{ $level->sector->name}}: {{$level->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="filiereEdit" class="p-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-all shadow-sm">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 uppercase tracking-wider ml-1">Public visé</label>
                        <select name="editPersonnes" id="editPersonnes" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                            @if (auth()->user()->role == 0)
                                <option value="all">Tout le monde</option>
                                <option value="teachers">Professeurs</option>
                            @endif
                            <option value="students">Étudiants</option>
                        </select>
                    </div>
                </div>

                <div id="divDisplayListEdit" class="bg-gray-50 p-4 rounded-2xl space-y-3">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Sélection :</label>
                    <div id="listFilieresEdit" class="grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 uppercase tracking-wider ml-1">Contenu</label>
                    <textarea name="editContenu" id="editContenu" rows="6" maxlength="1000" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm resize-none"></textarea>
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex justify-end space-x-3">
                <button type="button" id="cancelEditButton" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-all">Annuler</button>
                <button type="submit" id="saveEditButton" class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- VIEW MODALS (Combined for Students & Admins) -->
<div id="viewModal" class="hidden fixed inset-0 z-[100] bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-blue-600 text-white">
            <div>
                <h3 class="text-xl font-bold">Détails de l'annonce</h3>
                <p class="text-xs opacity-70">Consultation des informations du communiqué.</p>
            </div>
            <button type="button" id="closeModalView" class="h-10 w-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-all"><i class="fa-solid fa-times"></i></button>
        </div>

        <div class="flex-1 overflow-y-auto p-8 space-y-6">
            <div class="space-y-1">
                <h2 id="viewTitle_H" class="text-2xl font-black text-gray-900 leading-tight"></h2>
                <input type="hidden" id="viewTitle"> <!-- For JS compat -->
                <div class="flex flex-wrap gap-2 mt-2">
                    <span id="viewAnnonceur_Span" class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] font-bold uppercase tracking-wider"></span>
                    <input id="viewAnnonceur" type="hidden"> <!-- For JS compat -->
                    <span id="viewDateExpiration_Span" class="px-2 py-1 bg-rose-50 text-rose-600 rounded text-[10px] font-bold uppercase tracking-wider border border-rose-100"></span>
                    <input id="viewDateExpiration" type="hidden"> <!-- For JS compat -->
                </div>
            </div>

            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-gray-700 leading-relaxed text-sm whitespace-pre-wrap min-h-[150px]" id="viewContenu"></div>

            <div id="listFilieresViewParent" class="hidden space-y-3">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Passée par les filières :</p>
                <div id="listFilieresView" class="flex flex-wrap gap-2"></div>
            </div>
            
            <!-- Hidden inputs for JS compatibility if any -->
            <select id="viewFiliere" class="hidden"></select>
            <div id="viewFiliereParent" class="hidden"></div>
            <div id="viewPersonneParent" class="hidden"></div>
        </div>
        <div class="px-8 py-4 bg-gray-50 flex justify-end">
            <button type="button" onclick="document.getElementById('viewModal').classList.add('hidden')" class="px-6 py-2 bg-white border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-all">Fermer</button>
        </div>
    </div>
</div>

<!-- Modal 2 Simplified (for students) -->
<div id="viewModal2" class="hidden fixed inset-0 z-[100] bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-8 space-y-6">
            <div class="flex justify-between items-start">
                <div class="h-12 w-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center"><i class="fa-solid fa-bullhorn text-xl"></i></div>
                <button id="closeModalView2" class="text-gray-400 hover:text-gray-900 transition-all"><i class="fa-solid fa-times text-xl"></i></button>
            </div>
            <div class="space-y-4">
                <div class="space-y-1">
                    <h3 id="annonceTitleViewModal2" class="text-2xl font-bold text-gray-900"></h3>
                    <p id="annonceUserViewModal2" class="text-xs font-bold text-emerald-600 uppercase tracking-widest"></p>
                </div>
                <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed bg-gray-50 p-6 rounded-2xl" id="annonceContentViewModal2"></div>
            </div>
            <button onclick="document.getElementById('viewModal2').classList.add('hidden')" class="w-full py-3 bg-gray-900 text-white font-bold rounded-2xl hover:bg-gray-800 transition-all shadow-lg animate-pulse">J'ai compris</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    (function() {
        // Tooltip logic
        const tooltipIcon = document.getElementById('tooltipIcon');
        const tooltipInfo = document.getElementById('tooltipInfo');
        if (tooltipIcon) {
            tooltipIcon.addEventListener('mouseenter', () => tooltipInfo.classList.remove('hidden'));
            tooltipIcon.addEventListener('mouseleave', () => tooltipInfo.classList.add('hidden'));
        }

        @if (auth()->user()->role != 2)
        // ADD MODAL LOGIC
        const addModal = document.getElementById('addModal');
        const openModalAdd = document.getElementById('openModalAdd');
        const closeModalAdd = document.getElementById('closeModalAdd');
        const cancelAddButton = document.getElementById('cancelAddButton');
        const filiereAdd = document.getElementById('filiereAdd');
        const addFiliere = document.getElementById('addFiliere');
        const listFilieres = document.getElementById('listFilieres');
        const searchAdd = document.getElementById('searchAdd');

        const optionsParDefaut = Array.from(document.getElementsByClassName('addFilieres'));
        let addListStarted = false;

        if (openModalAdd) openModalAdd.onclick = () => addModal.classList.remove('hidden');
        if (closeModalAdd) closeModalAdd.onclick = cancelAddButton.onclick = () => location.reload();

        function updateFiliereUI() {
            const isAll = addFiliere.value === 'all';
            filiereAdd.style.display = isAll ? 'none' : 'block';
        }
        if (addFiliere) {
            addFiliere.onchange = updateFiliereUI;
            updateFiliereUI();
        }

        if (searchAdd) {
            searchAdd.oninput = (e) => {
                const val = e.target.value.toLowerCase();
                Array.from(addFiliere.options).forEach(opt => {
                    const match = opt.text.toLowerCase().includes(val);
                    opt.style.display = match ? 'block' : 'none';
                    opt.disabled = !match;
                });
            };
        }

        if (filiereAdd) {
            filiereAdd.onclick = () => {
                if (!addListStarted) { listFilieres.innerHTML = ''; addListStarted = true; }
                const opt = addFiliere.options[addFiliere.selectedIndex];
                if (opt.value === 'all') return;
                
                const badge = document.createElement('div');
                badge.className = "flex items-center justify-between px-3 py-2 bg-blue-50 border border-blue-100 rounded-xl text-xs font-medium text-blue-700 animate-scale-in group";
                badge.innerHTML = `
                    <input type="hidden" name="addFilieres${opt.value}" value="${opt.value}">
                    <span class="truncate pr-2">${opt.text}</span>
                    <button type="button" class="cancelAdd text-rose-500 hover:text-rose-700 transition-colors"><i class="fa-solid fa-circle-xmark"></i></button>
                `;
                listFilieres.appendChild(badge);
                opt.remove();
            };
        }

        if (listFilieres) {
            listFilieres.onclick = (e) => {
                const btn = e.target.closest('.cancelAdd');
                if (btn) {
                    const div = btn.closest('div');
                    const id = div.querySelector('input').value;
                    const text = div.querySelector('span').innerText;
                    addFiliere.innerHTML += `<option value="${id}" class="addFilieres">${text}</option>`;
                    div.remove();
                    if (listFilieres.childElementCount === 0) {
                        listFilieres.innerHTML = '<p class="text-gray-400 text-xs italic">Aucune filière sélectionnée (Toutes par défaut)</p>';
                        addListStarted = false;
                    }
                }
            };
        }

        // EDIT MODAL LOGIC
        const editModal = document.getElementById('editModal');
        const closeModalEdit = document.getElementById('closeModalEdit');
        const cancelEditButton = document.getElementById('cancelEditButton');
        if (closeModalEdit) closeModalEdit.onclick = cancelEditButton.onclick = () => location.reload();

        document.querySelectorAll('.openModalEdit').forEach(btn => {
            btn.onclick = async () => {
                const id = btn.parentNode.querySelector('.id').innerText;
                const res = await fetch(`/annonces/getAnnonceRelation/${id}`);
                const data = await res.json();
                const ann = data.annonce;

                document.getElementById('editId').value = id;
                document.getElementById('editTitle').value = ann.title;
                document.getElementById('editDateExpiration').value = ann.date_expiration;
                document.getElementById('editContenu').value = ann.content;
                document.getElementById('editPersonnes').value = ann.choix_personnes;

                const listE = document.getElementById('listFilieresEdit');
                listE.innerHTML = '';
                if (ann.choix_filieres !== 'all') {
                    data.filieres.forEach(f => {
                        const badge = document.createElement('div');
                        badge.className = "flex items-center justify-between px-3 py-2 bg-amber-50 border border-amber-100 rounded-xl text-xs font-medium text-amber-700";
                        badge.innerHTML = `
                            <input type="hidden" name="editFilieres${f.id}" value="${f.id}">
                            <span class="truncate pr-2">${f.sector.name}: ${f.name}</span>
                            <button type="button" class="cancelEdit text-rose-500"><i class="fa-solid fa-circle-xmark"></i></button>
                        `;
                        listE.appendChild(badge);
                        // Remove from select if exists
                        const opt = Array.from(document.getElementById('editFiliere').options).find(o => o.value == f.id);
                        if (opt) opt.remove();
                    });
                }
                editModal.classList.remove('hidden');
            };
        });

        const filiereEditBtn = document.getElementById('filiereEdit');
        if (filiereEditBtn) {
            filiereEditBtn.onclick = () => {
                const sel = document.getElementById('editFiliere');
                const opt = sel.options[sel.selectedIndex];
                if (opt.value === 'all') return;
                const target = document.getElementById('listFilieresEdit');
                const badge = document.createElement('div');
                badge.className = "flex items-center justify-between px-3 py-2 bg-amber-50 border border-amber-100 rounded-xl text-xs font-medium text-amber-700";
                badge.innerHTML = `
                    <input type="hidden" name="editFilieres${opt.value}" value="${opt.value}">
                    <span class="truncate pr-2">${opt.text}</span>
                    <button type="button" class="cancelEdit text-rose-500"><i class="fa-solid fa-circle-xmark"></i></button>
                `;
                target.appendChild(badge);
                opt.remove();
            };
        }

        const listFilieresEdit = document.getElementById('listFilieresEdit');
        if (listFilieresEdit) {
            listFilieresEdit.onclick = (e) => {
                const btn = e.target.closest('.cancelEdit');
                if (btn) {
                    const div = btn.closest('div');
                    const id = div.querySelector('input').value;
                    const text = div.querySelector('span').innerText;
                    document.getElementById('editFiliere').innerHTML += `<option value="${id}" class="editFilieres">${text}</option>`;
                    div.remove();
                }
            };
        }
        @endif

        // VIEW LOGIC (Student/Admin)
        document.querySelectorAll('.openModalView2').forEach(btn => {
            btn.onclick = async () => {
                const id = btn.parentNode.querySelector('.id').innerText;
                const res = await fetch(`/annonces/getAnnonceRelation/${id}`);
                const data = await res.json();
                const ann = data.annonce;

                document.getElementById('annonceTitleViewModal2').innerText = ann.title;
                document.getElementById('annonceUserViewModal2').innerText = (ann.user.role == 0 ? 'Responsable: ' : 'Professeur: ') + ann.user.first_name + ' ' + (ann.user.last_name || '');
                document.getElementById('annonceContentViewModal2').innerHTML = ann.content.replace(/\n/g, '<br>');
                document.getElementById('viewModal2').classList.remove('hidden');
            };
        });

        document.querySelectorAll('.openModalView').forEach(btn => {
            btn.onclick = async () => {
                const id = btn.parentNode.querySelector('.id').innerText;
                const res = await fetch(`/annonces/getAnnonceRelation/${id}`);
                const data = await res.json();
                const ann = data.annonce;

                document.getElementById('viewTitle_H').innerText = ann.title;
                document.getElementById('viewAnnonceur_Span').innerText = (ann.user.role == 0 ? 'Responsable: ' : 'Enseignant: ') + ann.user.first_name + ' ' + (ann.user.last_name || '');
                document.getElementById('viewDateExpiration_Span').innerHTML = `<i class="fa-solid fa-calendar-xmark mr-1"></i> Expire le: ${ann.date_expiration}`;
                document.getElementById('viewContenu').innerHTML = ann.content;

                const listV = document.getElementById('listFilieresView');
                const parentV = document.getElementById('listFilieresViewParent');
                listV.innerHTML = '';
                if (ann.choix_filieres !== 'all') {
                    parentV.classList.remove('hidden');
                    data.filieres.forEach(f => {
                        listV.innerHTML += `<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase tracking-widest">${f.sector.name}: ${f.name}</span>`;
                    });
                } else {
                    parentV.classList.add('hidden');
                }
                const viewModal = document.getElementById('viewModal');
                if (viewModal) viewModal.classList.remove('hidden');
            };
        });

        const closeModalView = document.getElementById('closeModalView');
        const closeModalView2 = document.getElementById('closeModalView2');
        if (closeModalView) closeModalView.onclick = () => document.getElementById('viewModal').classList.add('hidden');
        if (closeModalView2) closeModalView2.onclick = () => document.getElementById('viewModal2').classList.add('hidden');
    })();
</script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    var pusher = new Pusher('6979301f0eee4d497b90', { cluster: 'eu' });
    var channel = pusher.subscribe('annonce-channel');
    channel.bind('annonce-refresh', () => location.reload());
</script>
@endsection