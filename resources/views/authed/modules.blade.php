@extends('Layouts.authed')

@section('title', 'Modules')

@section('content')
<div class="section-animate space-y-8 p-4 md:p-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Gestion des Modules</h1>
            <p class="mt-1 text-gray-500">Administrez les modules de cours et leurs professeurs référents.</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="openModalAdd" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-md transform hover:-translate-y-0.5">
                <i class="fa-solid fa-plus mr-2 text-sm"></i>
                Ajouter un module
            </button>
        </div>
    </div>

    <!-- Search & Filters Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <form action="{{ route('modules.index') }}" method="GET" class="space-y-4">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input id="search" name="search" type="text" value="{{ request('search') }}"
                            class="block w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-gray-50/50"
                            placeholder="Rechercher un module, une filière...">
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                        Rechercher
                    </button>
                </div>
            </form>
        </div>

        @if ($loup)
            <div class="px-6 py-3 bg-amber-50 border-b border-amber-100 flex items-center justify-between">
                <span class="text-xs font-bold text-amber-700">Résultats filtrés</span>
                <a href="{{ route('modules.index') }}" class="text-xs font-bold text-amber-600 hover:underline">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Réinitialiser
                </a>
            </div>
        @endif

        <!-- Desktop Table -->
        <div class="overflow-x-auto">
            <table id="tableModule" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Module</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Filière</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Professeur</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($modules as $module)
                        <tr class="hover:bg-gray-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-bold text-xs">
                                        <i class="fa-solid fa-book-open"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $module->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium">ID: #{{ str_pad($module->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600">
                                    {{ $module->level->sector->name }}: {{ $module->level->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <div class="flex items-center gap-2">
                                    <div class="h-7 w-7 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($module->user->first_name, 0, 1) . substr($module->user->last_name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $module->user->first_name }} {{ $module->user->last_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button title="Voir les détails" class="openModalView p-2.5 text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </button>
                                    <button title="Modifier" class="openModalEdit p-2.5 text-amber-600 bg-amber-50 hover:bg-amber-600 hover:text-white rounded-xl transition-all shadow-sm">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <form method="POST" action="{{ route('modules.delete', ['id' => $module->id]) }}" class="contents" onsubmit="return confirm('Supprimer ce module ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button title="Supprimer" class="p-2.5 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white rounded-xl transition-all shadow-sm">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                    <span class="id hidden">{{ $module->id }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($modules->isEmpty())
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-book text-4xl text-gray-200 mb-4"></i>
                                    <p class="text-gray-400 font-medium">Aucun module trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($modules->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                {{ $modules->links() }}
            </div>
        @endif
    </div>
</div>

    <!-- MODALS -->
    <!-- Add Modal -->
    <div id="addModal" class="hidden fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <form method="POST" id="formAddModule" action="{{ route('modules.store') }}" onsubmit="return validProf()">    
                    @csrf
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h3 class="text-xl font-black text-gray-900">Nouveau Module</h3>
                        <button type="button" id="closeModalAdd" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-white transition-all">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        <!-- Module Name -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nom du module</label>
                            <input id="addName" name="addName" required type="text" placeholder="ex: Algorithmique Avancée"
                                class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                        </div>

                        <!-- Sector/Level Selection -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Filière & Niveau</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-graduation-cap text-xs"></i>
                                </div>
                                <select id="addFiliere" name="addFiliere" required
                                    class="block w-full pl-9 pr-3 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold appearance-none">
                                    @foreach ($sectors as $sector)
                                        @foreach ($sector->levels as $level)
                                            <option value="{{ $level->id }}">{{$sector->name}} - {{ $level->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Professor Selection -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Professeur Référent</label>
                                <div class="relative w-48">
                                    <input type="text" id="searchAdd" placeholder="Filtrer prof..." 
                                        class="w-full pl-8 pr-3 py-1.5 bg-gray-100 border-none rounded-xl text-[10px] focus:ring-1 focus:ring-blue-500 transition-all">
                                    <i class="fa-solid fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400"></i>
                                </div>
                            </div>
                            <select id="addProf" name="addProf" size="5" required
                                class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold custom-scrollbar overflow-y-auto">
                                <!-- Dynamic options -->
                            </select>
                            <p class="text-[10px] text-gray-400 italic text-center">Les professeurs affichés sont filtrés par filière.</p>
                        </div>
                    </div>

                    <div class="p-8 bg-gray-50/50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" id="cancelAddButton" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-all">Annuler</button>
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5">
                            Créer le module
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="hidden fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                <div class="px-8 py-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">Détails du module</p>
                            <h3 id="viewNameLabel" class="text-2xl font-black text-gray-900 leading-tight">...</h3>
                        </div>
                        <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100/50">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center">
                                <i class="fa-solid fa-graduation-cap mr-2"></i> Filière & Niveau
                            </p>
                            <p id="viewFiliere" class="text-sm font-bold text-gray-800">...</p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100/50">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 flex items-center">
                                <i class="fa-solid fa-user-tie mr-2"></i> Professeur Référent
                            </p>
                            <div class="flex items-center gap-3">
                                <div id="profAvatar" class="h-8 w-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-[10px] font-black">?</div>
                                <p id="viewProf" class="text-sm font-bold text-gray-800">...</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-center">
                        <button type="button" id="closeModalView" class="px-10 py-3 bg-gray-900 text-white font-bold rounded-2xl hover:bg-gray-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <form method="POST" action="{{ route('modules.edit') }}" onsubmit="return validProfEdit()">    
                    @csrf
                    <input type="hidden" name="id" id="editId">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h3 class="text-xl font-black text-gray-900">Modifier le Module</h3>
                        <button type="button" id="closeModalEdit" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-white transition-all">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nom du module</label>
                            <input id="editName" name="editName" required type="text"
                                class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Filière & Niveau</label>
                            <select id="editFiliere" name="editFiliere" required
                                class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                                @foreach ($sectors as $sector)
                                    @foreach ($sector->levels as $level)
                                        <option value="{{ $level->id }}">{{$sector->name}} - {{ $level->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Professeur Référent</label>
                                <div class="relative w-48">
                                    <input type="text" id="searchEdit" placeholder="Filtrer prof..." 
                                        class="w-full pl-8 pr-3 py-1.5 bg-gray-100 border-none rounded-xl text-[10px] focus:ring-1 focus:ring-blue-500 transition-all">
                                    <i class="fa-solid fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-[10px] text-gray-400"></i>
                                </div>
                            </div>
                            <select id="editProf" name="editProf" size="5" required
                                class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold custom-scrollbar">
                                <!-- Dynamic options -->
                            </select>
                        </div>
                    </div>

                    <div class="p-8 bg-gray-50/50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" id="cancelEditButton" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-all">Annuler</button>
                        <button type="submit" class="px-8 py-3 bg-amber-600 text-white font-bold rounded-2xl hover:bg-amber-700 shadow-lg shadow-amber-500/20 transition-all transform hover:-translate-y-0.5">
                            Sauvegarder
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
        // Global functions for modal management
        function toggleModal(modalId, show = true) {
            const modal = document.getElementById(modalId);
            if (show) modal.classList.remove('hidden');
            else modal.classList.add('hidden');
        }

        // Dynamic Professor Loading
        async function fetchProfessors(filiereId, selectId, currentProfId = null) {
            const select = document.getElementById(selectId);
            try {
                const res = await fetch(`/modules/getProfFiliere/${filiereId}`);
                const data = await res.json();
                select.innerHTML = '';
                data.forEach(user => {
                    const opt = document.createElement('option');
                    opt.value = user.id;
                    opt.textContent = `${user.first_name} ${user.last_name}`;
                    if (currentProfId && user.id == currentProfId) opt.selected = true;
                    opt.className = 'py-2 px-1 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer';
                    select.appendChild(opt);
                });
            } catch(e) { console.error(e); }
        }

        // Add Modal Handlers
        document.getElementById('openModalAdd').addEventListener('click', () => {
            toggleModal('addModal');
            fetchProfessors(document.getElementById('addFiliere').value, 'addProf');
        });

        document.getElementById('addFiliere').addEventListener('change', (e) => {
            fetchProfessors(e.target.value, 'addProf');
        });

        // Close handlers for Add Modal
        document.getElementById('closeModalAdd').addEventListener('click', () => toggleModal('addModal', false));
        document.getElementById('cancelAddButton').addEventListener('click', () => toggleModal('addModal', false));

        // Edit Modal Handlers
        document.querySelectorAll('.openModalEdit').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.closest('tr').querySelector('.id').textContent;
                try {
                    const res = await fetch(`/modules/getModule/${id}`);
                    const data = await res.json();
                    document.getElementById('editId').value = data.id;
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editFiliere').value = data.level.id;
                    await fetchProfessors(data.level.id, 'editProf', data.user.id);
                    toggleModal('editModal');
                } catch(e) { console.error(e); }
            });
        });

        document.getElementById('editFiliere').addEventListener('change', (e) => {
            fetchProfessors(e.target.value, 'editProf');
        });

        // Close handlers for Edit Modal
        document.getElementById('closeModalEdit').addEventListener('click', () => toggleModal('editModal', false));
        document.getElementById('cancelEditButton').addEventListener('click', () => toggleModal('editModal', false));

        // View Modal Handlers
        document.querySelectorAll('.openModalView').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.closest('tr').querySelector('.id').textContent;
                try {
                    const res = await fetch(`/modules/getModule/${id}`);
                    const data = await res.json();
                    document.getElementById('viewNameLabel').textContent = data.name;
                    document.getElementById('viewFiliere').textContent = `${data.level.sector.name} - ${data.level.name}`;
                    document.getElementById('viewProf').textContent = `${data.user.first_name} ${data.user.last_name}`;
                    document.getElementById('profAvatar').textContent = data.user.first_name[0] + data.user.last_name[0];
                    toggleModal('viewModal');
                } catch(e) { console.error(e); }
            });
        });

        document.getElementById('closeModalView').addEventListener('click', () => toggleModal('viewModal', false));

        // Search/Filter logic for selects
        function setupFilter(inputId, selectId) {
            document.getElementById(inputId).addEventListener('input', (e) => {
                const val = e.target.value.toLowerCase();
                const opts = document.getElementById(selectId).options;
                Array.from(opts).forEach(opt => {
                    const text = opt.textContent.toLowerCase();
                    opt.style.display = text.includes(val) ? '' : 'none';
                });
            });
        }
        setupFilter('searchAdd', 'addProf');
        setupFilter('searchEdit', 'editProf');

        // Form validation
        window.validProf = function() {
            if (!document.getElementById('addProf').value) {
                alert('Veuillez sélectionner un professeur.');
                return false;
            }
            return true;
        };

        window.validProfEdit = function() {
            if (!document.getElementById('editProf').value) {
                alert('Veuillez sélectionner un professeur.');
                return false;
            }
            return true;
        };
    })();
</script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    const pusher = new Pusher('6979301f0eee4d497b90', { cluster: 'eu' });
    pusher.subscribe('module-channel').bind('module-refresh', () => location.reload());
</script>
@endsection