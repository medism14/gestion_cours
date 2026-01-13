@extends('Layouts.authed')

@section('title', 'Modules')

@section('content')
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
                        <button type="button" id="closeModalAddTop" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-white transition-all">
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
                        <button type="button" id="closeModalEditTop" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-white transition-all">
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
                                        <option value="{{ $level->id }}" class="editOptions">{{$sector->name}} - {{ $level->name }}</option>
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
                                <!-- Dynamic dynamic -->
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

    // Utility close buttons
    ['addModal', 'viewModal', 'editModal'].forEach(id => {
        document.querySelectorAll(`#${id} [id*="closeModal"], #${id} [id*="cancel"]`).forEach(b => {
            b.addEventListener('click', () => toggleModal(id, false));
        });
    });

    // Form validation
    function validProf() {
        if (!document.getElementById('addProf').value) {
            alert('Veuillez sélectionner un professeur.');
            return false;
        }
        return submitFunction();
    }

    function validProfEdit() {
        if (!document.getElementById('editProf').value) {
            alert('Veuillez sélectionner un professeur.');
            return false;
        }
        return submitFunction();
    }

    let formIsSubmitting = false;
    function submitFunction() {
        if (formIsSubmitting) return false;
        formIsSubmitting = true;
        return true;
    }
</script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    const pusher = new Pusher('6979301f0eee4d497b90', { cluster: 'eu' });
    pusher.subscribe('module-channel').bind('module-refresh', () => location.reload());
</script>
@endsection