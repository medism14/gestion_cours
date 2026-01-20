@extends('Layouts.authed')

@section('title', 'Filières')

@section('content')
<div class="section-animate space-y-8 p-4 md:p-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Gestion des Filières</h1>
            <p class="mt-1 text-gray-500">Gérez les départements académiques et leurs différents niveaux d'études.</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="openModalAdd" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-md transform hover:-translate-y-0.5">
                <i class="fa-solid fa-plus-circle mr-2 text-sm"></i>
                Nouvelle filière
            </button>
        </div>
    </div>

    <!-- Stats/Search Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-gray-50/20">
            <form action="{{ route('sectors.index') }}" method="GET" class="max-w-xl">
                @csrf
                <div class="flex gap-3">
                    <div class="relative flex-1 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input id="search" name="search" type="text" value="{{ request('search') }}"
                            class="block w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                            placeholder="Rechercher une filière...">
                        
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="tooltipIcon" class="fa-solid fa-circle-question text-gray-300 hover:text-blue-500 cursor-help transition-colors"></i>
                            <div id="tooltipInfo" class="hidden absolute bottom-full right-0 mb-2 w-64 p-3 bg-gray-900 text-white text-[10px] rounded-xl shadow-xl z-50">
                                <p class="font-bold mb-1 uppercase tracking-wider">Aide à la recherche</p>
                                <p class="text-gray-400">Saisissez le nom de la filière pour filtrer instantanément la liste.</p>
                                <div class="absolute top-full right-4 border-8 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white font-medium rounded-xl hover:bg-gray-800 transition-all shadow-sm">
                        Rechercher
                    </button>
                    @if ($loup)
                        <a href="{{ route('sectors.index') }}" class="p-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all" title="Réinitialiser">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table id="tablesector" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Désignation</th>
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre de Niveaux</th>
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($sectors as $sector)
                        <tr class="hover:bg-gray-50/30 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-bold shadow-inner mr-4">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </div>
                                    <div class="text-sm font-bold text-gray-900">{{ $sector->name }}</div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $sector->levels->count() }} Niveaux
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-end gap-2">
                                    <button title="Voir les détails" class="openModalView p-2.5 text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </button>
                                    <button title="Modifier" class="openModalEdit p-2.5 text-amber-600 bg-amber-50 hover:bg-amber-600 hover:text-white rounded-xl transition-all shadow-sm">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <form method="POST" action="{{ route('sectors.delete', ['id' => $sector->id]) }}" class="contents" onsubmit="return confirm('Attention: Supprimer cette filière supprimera également tous les niveaux associés. Continuer ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button title="Supprimer" class="p-2.5 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white rounded-xl transition-all shadow-sm">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                    <span class="id hidden">{{ $sector->id }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($sectors->isEmpty())
                        <tr>
                            <td colspan="3" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-folder-open text-4xl text-gray-100 mb-4"></i>
                                    <p class="text-gray-400 font-medium">Aucune filière trouvée.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($sectors->hasPages())
            <div class="px-8 py-4 bg-gray-50/50 border-t border-gray-100">
                {{ $sectors->links() }}
            </div>
        @endif
    </div>
</div>

    @if ($loup)
    <div class="w-full flex justify-center mt-3">
        <a href="/sectors" class="bg-orange-400 text-white px-2 py-1 rounded-lg">Revenir</a>
    </div>
    @endif

    <!-- MODALS -->
    <!-- Add Modal -->
    <div id="addModal" class="hidden fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <form method="POST" id="formAddSector" action="{{ route('sectors.store') }}" onsubmit="return submitFunction()">    
                    @csrf
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h3 class="text-xl font-black text-gray-900">Nouvelle Filière</h3>
                        <button type="button" id="closeModalAddTop" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-white transition-all">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        <!-- Sector Name -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nom de la filière</label>
                            <input id="addName" name="addName" required type="text" placeholder="ex: Informatique de Gestion"
                                class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                        </div>

                        <!-- Levels Management -->
                        <div class="space-y-4">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Gestion des Niveaux</label>
                            <div class="flex gap-2">
                                <input id="addLevel" type="text" placeholder="Ajouter un niveau (ex: L1, Master...)"
                                    class="flex-1 px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                <button type="button" id="addLevelBtn" class="px-6 bg-gray-900 text-white font-bold rounded-2xl hover:bg-gray-800 transition-all shadow-sm">
                                    Ajouter
                                </button>
                            </div>

                            <div id="levelList" class="space-y-2 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                <!-- Levels will be appended here -->
                            </div>
                            <input type="hidden" name="addMaxDegree" id="addMaxDegree">
                        </div>
                    </div>

                    <div class="p-8 bg-gray-50/50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" id="cancelAddButton" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-all">Annuler</button>
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5">
                            Créer la filière
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
                            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">Détails de la filière</p>
                            <h3 id="viewName" class="text-2xl font-black text-gray-900 leading-tight">...</h3>
                        </div>
                        <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Niveaux académiques</p>
                        <div id="levelListView" class="grid grid-cols-1 gap-2">
                            <!-- Levels list dynamic -->
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
                <form method="POST" action="{{ route('sectors.edit') }}" onsubmit="return submitFunction()">    
                    @csrf
                    <input type="hidden" name="id" id="editId">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h3 class="text-xl font-black text-gray-900">Modifier la Filière</h3>
                        <button type="button" id="closeModalEditTop" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-white transition-all">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nom de la filière</label>
                            <input id="editName" name="editName" required type="text"
                                class="block w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm font-semibold">
                        </div>

                        <div class="space-y-4">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Niveaux</label>
                            <div class="flex gap-2">
                                <input id="editLevel" type="text" placeholder="Ajouter un niveau..."
                                    class="flex-1 px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                <button type="button" id="editLevelBtn" class="px-6 bg-gray-900 text-white font-bold rounded-2xl hover:bg-gray-800 transition-all">
                                    Ajouter
                                </button>
                            </div>

                            <div id="levelListEdit" class="space-y-2 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                <!-- Dynamic levels -->
                            </div>
                            <input type="hidden" name="editMaxDegree" id="editMaxDegree">
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
        let currentAddDegree = 0;
        let currentEditDegree = 0;

        function toggleModal(id, show = true) {
            const m = document.getElementById(id);
            if (show) m.classList.remove('hidden');
            else m.classList.add('hidden');
        }

        function reindexLevels(containerId, mode) {
            const container = document.getElementById(containerId);
            const rows = container.querySelectorAll('.level-row');
            const namePrefix = mode === 'add' ? 'addName' : 'editName';
            const degreePrefix = mode === 'add' ? 'addDegree' : 'editDegree';
            
            rows.forEach((row, index) => {
                const degree = index + 1;
                row.querySelector('.level-index-label').textContent = `N°${degree}`;
                row.querySelector('input[type="hidden"]').name = `${degreePrefix}${degree}`;
                row.querySelector('input[type="hidden"]').value = degree;
                row.querySelector('input[type="text"]').name = `${namePrefix}${degree}`;
            });

            if (mode === 'add') {
                currentAddDegree = rows.length;
                document.getElementById('addMaxDegree').value = currentAddDegree;
            } else {
                currentEditDegree = rows.length;
                document.getElementById('editMaxDegree').value = currentEditDegree;
            }
        }

        function createLevelRow(containerId, value = '', mode = 'add') {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = "level-row flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border border-gray-100 mb-2";
            
            // Temporary IDs, they will be re-indexed immediately
            div.innerHTML = `
                <span class="level-index-label text-xs font-bold text-gray-400 w-8"></span>
                <input type="hidden" value="">
                <input type="text" value="${value}" class="flex-1 bg-transparent border-none focus:ring-0 text-sm font-semibold" readonly>
                <button type="button" class="text-red-400 hover:text-red-600 p-1 remove-level"><i class="fa-solid fa-circle-xmark"></i></button>
            `;

            div.querySelector('.remove-level').onclick = () => {
                div.remove();
                reindexLevels(containerId, mode);
            };

            container.appendChild(div);
            reindexLevels(containerId, mode);
        }

        document.getElementById('openModalAdd').addEventListener('click', () => toggleModal('addModal'));

        document.getElementById('addLevelBtn').addEventListener('click', () => {
            const input = document.getElementById('addLevel');
            if (!input.value.trim()) return;
            createLevelRow('levelList', input.value.trim(), 'add');
            input.value = '';
        });

        // View Sector Handlers
        document.querySelectorAll('.openModalView').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.closest('tr').querySelector('.id').textContent;
                try {
                    const res = await fetch(`/sectors/getSector/${id}`);
                    const data = await res.json();
                    document.getElementById('viewName').textContent = data.name;
                    const container = document.getElementById('levelListView');
                    container.innerHTML = '';
                    data.levels.forEach(l => {
                        const d = document.createElement('div');
                        d.className = "p-3 bg-blue-50 text-blue-700 rounded-xl text-sm font-bold border border-blue-100";
                        d.textContent = l.name;
                        container.appendChild(d);
                    });
                    toggleModal('viewModal');
                } catch(e) { console.error(e); }
            });
        });

        // Edit Sector Handlers
        document.querySelectorAll('.openModalEdit').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.closest('tr').querySelector('.id').textContent;
                try {
                    const res = await fetch(`/sectors/getSector/${id}`);
                    const data = await res.json();
                    document.getElementById('editId').value = data.id;
                    document.getElementById('editName').value = data.name;
                    const container = document.getElementById('levelListEdit');
                    container.innerHTML = '';
                    currentEditDegree = 0;
                    data.levels.forEach(l => {
                        createLevelRow('levelListEdit', l.name, 'edit');
                    });
                    toggleModal('editModal');
                } catch(e) { console.error(e); }
            });
        });

        document.getElementById('editLevelBtn').addEventListener('click', () => {
            const input = document.getElementById('editLevel');
            if (!input.value.trim()) return;
            createLevelRow('levelListEdit', input.value.trim(), 'edit');
            input.value = '';
        });

        // Close buttons logic
        ['addModal', 'viewModal', 'editModal'].forEach(id => {
            document.querySelectorAll(`#${id} [id*="closeModal"], #${id} [id*="cancel"]`).forEach(b => {
                b.addEventListener('click', () => {
                    toggleModal(id, false);
                    if(id === 'addModal') {
                        document.getElementById('levelList').innerHTML = '';
                        currentAddDegree = 0;
                    }
                });
            });
        });

        // Tooltip logic
        const tIcon = document.getElementById('tooltipIcon');
        const tInfo = document.getElementById('tooltipInfo');
        tIcon?.addEventListener('mouseenter', () => tInfo.classList.remove('hidden'));
        tIcon?.addEventListener('mouseleave', () => tInfo.classList.add('hidden'));
    })();
</script>
@endsection
```