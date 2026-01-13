@extends('Layouts.authed')

@section('title', 'Ressources')

@section('content')
<div class="section-animate space-y-8 p-4 md:p-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Ressources Académiques</h1>
            <p class="mt-1 text-gray-500">Accédez aux supports de cours, TD, TP et annales de vos modules.</p>
        </div>
        @if (auth()->user()->role == 1)  
            <button id="openModalAdd" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-md transform hover:-translate-y-0.5">
                <i class="fa-solid fa-plus mr-2 text-sm"></i>
                Nouvelle ressource
            </button>
        @endif
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('resources.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            @if (auth()->user()->role == 2)
                <div class="flex-1 lg:max-w-xs">
                    <label for="moduleList" class="sr-only">Filtrer par module</label>
                    <select name="moduleList" id="moduleList" onchange="this.form.submit()"
                        class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                        <option value="">Tous les modules</option>
                        @foreach ($modulesSearch as $moduleSearch)
                            <option value="{{ $moduleSearch->id }}" {{ request('moduleList') == $moduleSearch->id ? 'selected' : '' }}>
                                {{ $moduleSearch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                </div>
                <input id="search" name="search" type="text" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    placeholder="Rechercher par section, fichier, module...">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-8 py-2.5 bg-gray-900 text-white font-medium rounded-xl hover:bg-gray-800 transition-all">
                    Rechercher
                </button>
                @if(request('search') || request('moduleList') || $loup)
                    <a href="{{ route('resources.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 font-medium rounded-xl hover:bg-gray-200 transition-all text-center">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Fichier</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Section</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Module</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Professeur</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden xl:table-cell">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($resources as $resource)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fa-solid fa-file-lines text-lg"></i>
                                    </div>
                                    <div class="font-semibold text-gray-900 max-w-[200px] truncate" title="{{ $resource->file->filename }}">
                                        {{ $resource->file->filename }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider 
                                    {{ $resource->section == 'cours' ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $resource->section }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-sm font-medium text-gray-700">
                                {{ $resource->module->name }}
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell text-sm text-gray-500">
                                {{ $resource->module->user->first_name }} {{ $resource->module->user->last_name }}
                            </td>
                            <td class="px-6 py-4 hidden xl:table-cell text-sm text-gray-500">
                                {{ $resource->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <form method="post" action="{{ route('resources.download', $resource->id) }}" class="inline">
                                        @csrf
                                        <button title="Télécharger" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                            <i class="fa-solid fa-download"></i>
                                        </button>
                                    </form>
                                    @if (auth()->user()->role != 2)
                                        <button title="Voir" class="openModalView p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" data-id="{{ $resource->id }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button title="Modifier" class="openModalEdit p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" data-id="{{ $resource->id }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form method="POST" action="{{ route('resources.delete', $resource->id) }}" class="inline" onsubmit="return confirm('Supprimer cette ressource ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button title="Supprimer" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic font-medium">
                                Aucune ressource trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if ($resources->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                {{ $resources->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODALS -->

<!-- ADD/EDIT MODAL -->
<div id="resourceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
    
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Nouvelle ressource</h3>
            <button class="closeModal text-gray-400 hover:text-gray-600 p-1.5 hover:bg-white rounded-lg transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="resourceForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="resId" name="id">
            
            <div class="space-y-1">
                <label for="resFile" class="block text-sm font-semibold text-gray-700 font-bold">Le fichier <span id="fileRequired" class="text-red-500">*</span></label>
                <div class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-6 hover:border-blue-400 transition-colors bg-gray-50 cursor-pointer relative group">
                    <input id="resFile" name="addFile" type="file" class="absolute inset-0 opacity-0 cursor-pointer">
                    <div class="text-center group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 group-hover:text-blue-500 mb-2"></i>
                        <p id="fileNameDisplay" class="text-xs text-gray-500 font-medium italic truncate max-w-[250px]">Cliquez pour sélectionner un fichier</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="resSection" class="block text-sm font-semibold text-gray-700">Section</label>
                    <select name="addSection" id="resSection" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                        <option value="td">TD</option>
                        <option value="tp">TP</option>
                        <option value="cours">COURS</option>
                        <option value="annales">Annales</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label for="resModule" class="block text-sm font-semibold text-gray-700">Module</label>
                    <select name="addModule" id="resModule" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                        @foreach ($modules as $module)
                            <option value="{{ $module->id }}">{{ $module->name }} ({{ $module->level->name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label for="resDescription" class="block text-sm font-semibold text-gray-700">Description</label>
                <textarea name="addDescription" id="resDescription" rows="3"
                    class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                    placeholder="Détails sur le contenu de la ressource..."></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" class="closeModal px-6 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-md">
                    Enregistrer la ressource
                </button>
            </div>
        </form>
    </div>
</div>

<!-- VIEW MODAL -->
<div id="viewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all p-8 text-center">
        <button class="closeViewModal absolute top-4 right-4 text-gray-400 hover:text-gray-900 transition-colors">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
        
        <div class="mx-auto h-20 w-20 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-3xl mb-4">
            <i class="fa-solid fa-file-invoice"></i>
        </div>
        
        <h2 id="viewResource" class="text-2xl font-bold text-gray-900 mb-2 truncate px-4"></h2>
        <p id="viewDescription" class="text-gray-500 text-sm mb-6 line-clamp-3 italic bg-gray-50 p-4 rounded-xl"></p>
        
        <div class="grid grid-cols-2 gap-4 text-left border-t border-gray-100 pt-6">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Section</p>
                <p id="viewSection" class="text-sm font-semibold text-gray-700 uppercase"></p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Module</p>
                <p id="viewModuleDetail" class="text-sm font-semibold text-gray-700 truncate"></p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Professeur</p>
                <p id="viewProf" class="text-sm font-semibold text-gray-700 truncate"></p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date</p>
                <p id="viewDate" class="text-sm font-semibold text-gray-700"></p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const resourceModal = document.getElementById('resourceModal');
    const viewModal = document.getElementById('viewModal');
    const resourceForm = document.getElementById('resourceForm');
    const modalTitle = document.getElementById('modalTitle');
    const resFile = document.getElementById('resFile');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const fileRequired = document.getElementById('fileRequired');

    resFile.onchange = e => {
        const file = e.target.files[0];
        if (file) fileNameDisplay.innerText = file.name;
    };

    document.querySelectorAll('.closeModal').forEach(btn => btn.onclick = () => resourceModal.classList.add('hidden'));
    document.querySelector('.closeViewModal').onclick = () => viewModal.classList.add('hidden');

    // Add Modal logic
    const openAdd = document.getElementById('openModalAdd');
    if(openAdd) {
        openAdd.onclick = () => {
            modalTitle.innerText = "Nouvelle ressource";
            resourceForm.action = "{{ route('resources.store') }}";
            resourceForm.reset();
            fileNameDisplay.innerText = "Cliquez pour sélectionner un fichier";
            fileRequired.classList.remove('hidden');
            resFile.required = true;
            resourceModal.classList.remove('hidden');
        };
    }

    // View Modal logic
    document.querySelectorAll('.openModalView').forEach(btn => {
        btn.onclick = async () => {
            const id = btn.getAttribute('data-id');
            try {
                const res = await fetch(`/resources/getResource/${id}`);
                const data = await res.json();
                
                document.getElementById('viewResource').innerText = data.file.filename;
                document.getElementById('viewDescription').innerText = data.description || 'Pas de description';
                document.getElementById('viewSection').innerText = data.section;
                document.getElementById('viewModuleDetail').innerText = data.module.name;
                document.getElementById('viewProf').innerText = data.module.user.first_name + ' ' + data.module.user.last_name;
                document.getElementById('viewDate').innerText = new Date(data.created_at).toLocaleDateString();
                
                viewModal.classList.remove('hidden');
            } catch (e) { console.error(e); }
        };
    });

    // Edit Modal logic
    document.querySelectorAll('.openModalEdit').forEach(btn => {
        btn.onclick = async () => {
            const id = btn.getAttribute('data-id');
            try {
                const res = await fetch(`/resources/getResource/${id}`);
                const data = await res.json();
                
                modalTitle.innerText = "Modifier la ressource";
                resourceForm.action = "{{ route('resources.edit') }}";
                document.getElementById('resId').value = data.id;
                document.getElementById('resSection').value = data.section;
                document.getElementById('resModule').value = data.module_id;
                document.getElementById('resDescription').value = data.description || '';
                fileNameDisplay.innerText = `Actuel: ${data.file.filename}`;
                fileRequired.classList.add('hidden');
                resFile.required = false;
                
                resourceModal.classList.remove('hidden');
            } catch (e) { console.error(e); }
        };
    });
</script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    var pusher = new Pusher('6979301f0eee4d497b90', { cluster: 'eu' });
    var channel = pusher.subscribe('resource-channel');
    channel.bind('resource-refresh', async function (data) {
        let resource = data.resource;
        let actualUserId = {{ auth()->user()->id }};
        const response = await fetch(`/getUserInfos/${actualUserId}`);
        let actualUser = await response.json();

        if (actualUser.role == 0) {
            location.reload();
        } else if (actualUser.role == 2) {
            actualUser.levels_users.forEach((level) => {
                if (resource.module.level_id == level.level_id) location.reload();
            });
        }
    });
</script>
@endsection