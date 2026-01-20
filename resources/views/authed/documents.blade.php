@extends('Layouts.authed')

@section('title', 'Bibliothèques')

@section('content')
<div class="section-animate space-y-8 p-4 md:p-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Documents numériques</h1>
            <p class="mt-1 text-gray-500">Gérez et accédez à l'ensemble des documents PDF de l'établissement.</p>
        </div>
        @if (auth()->user()->role == 0)  
            <button id="openModalAdd" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-md transform hover:-translate-y-0.5">
                <i class="fa-solid fa-plus mr-2 text-sm"></i>
                Ajouter un document
            </button>
        @endif
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('documents.index') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                </div>
                <input id="search" name="search" type="text" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    placeholder="Rechercher par titre ou nom de fichier...">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white font-medium rounded-xl hover:bg-gray-800 transition-all">
                Rechercher
            </button>
            @if(request('search') || $loup)
                <a href="{{ route('documents.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 font-medium rounded-xl hover:bg-gray-200 transition-all text-center">
                    Effacer
                </a>
            @endif
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Titre</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Fichier</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Taille</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Visibilité</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider hidden xl:table-cell">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($documents as $document)
                        @php
                            $visibilityLabel = match($document->visibility) {
                                'all' => 'Tous',
                                'teachers' => 'Professeurs',
                                'students' => 'Étudiants',
                            };
                            $visibilityClass = match($document->visibility) {
                                'all' => 'bg-blue-50 text-blue-600',
                                'teachers' => 'bg-emerald-50 text-emerald-600',
                                'students' => 'bg-purple-50 text-purple-600',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-red-50 text-red-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fa-solid fa-file-pdf text-lg"></i>
                                    </div>
                                    <div class="font-semibold text-gray-900">{{ $document->title }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-500 italic max-w-xs truncate">
                                {{ $document->filename }}
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell text-sm text-gray-500">
                                {{ $document->formatted_file_size }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $visibilityClass }}">
                                    {{ $visibilityLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden xl:table-cell text-sm text-gray-500">
                                {{ $document->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <div class="flex items-center justify-end space-x-1">
                                    <form method="post" action="{{ route('documents.download', $document->id) }}" class="inline">
                                        @csrf
                                        <button title="Télécharger" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                            <i class="fa-solid fa-download"></i>
                                        </button>
                                    </form>
                                    <button title="Voir" class="openModalView p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" data-id="{{ $document->id }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    @if (auth()->user()->role == 0)  
                                        <button title="Modifier" class="openModalEdit p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" data-id="{{ $document->id }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form method="POST" action="{{ route('documents.delete', $document->id) }}" class="inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce document ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button title="Supprimer" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <span class="id hidden">{{ $document->id }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                Aucun document trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if ($documents->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 italic">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODALS -->

<!-- ADD/EDIT MODAL TEMPLATE (Used via JS or common classes) -->
<div id="documentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
    
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Ajouter un document</h3>
            <button class="closeModal text-gray-400 hover:text-gray-600 p-1.5 hover:bg-white rounded-lg transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form Area -->
        <form id="documentForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="docId" name="id">
            
            <div class="space-y-1">
                <label for="docFile" class="block text-sm font-semibold text-gray-700">Fichier PDF <span id="fileRequired" class="text-red-500">*</span></label>
                <div class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-6 hover:border-blue-400 transition-colors bg-gray-50 group cursor-pointer relative">
                    <input id="docFile" name="addFile" type="file" accept=".pdf" class="absolute inset-0 opacity-0 cursor-pointer">
                    <div class="text-center group-hover:scale-105 transition-transform duration-200">
                        <i class="fa-solid fa-file-arrow-up text-3xl text-gray-400 group-hover:text-blue-500 mb-2"></i>
                        <p id="fileNameDisplay" class="text-xs text-gray-500 font-medium italic truncate max-w-[200px]">Cliquez ou glissez un fichier (Max 50Mo)</p>
                    </div>
                </div>
                <!-- Progress Area -->
                <div id="uploadProgress" class="hidden mt-4 space-y-2">
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div id="progressBarFill" class="h-full bg-blue-600 transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="progressText" class="text-center text-[10px] font-bold text-blue-600 uppercase tracking-widest">0%</p>
                </div>
            </div>

            <div class="space-y-1">
                <label for="docTitle" class="block text-sm font-semibold text-gray-700">Titre du document</label>
                <input name="addTitle" id="docTitle" type="text" required
                    class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
            </div>

            <div class="space-y-1">
                <label for="docVisibility" class="block text-sm font-semibold text-gray-700">Qui peut voir ce document ?</label>
                <select name="addVisibility" id="docVisibility" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    <option value="all">Tout le monde</option>
                    <option value="teachers">Professeurs uniquement</option>
                    <option value="students">Étudiants uniquement</option>
                </select>
            </div>

            <div class="space-y-1">
                <label for="docDescription" class="block text-sm font-semibold text-gray-700">Description (Optionnel)</label>
                <textarea name="addDescription" id="docDescription" rows="3"
                    class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" class="closeModal px-6 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </button>
                <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-md">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- VIEW MODAL (Simplified analytical view) -->
<div id="viewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all p-8 text-center">
        <button class="closeViewModal absolute top-4 right-4 text-gray-400 hover:text-gray-900 transition-colors">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
        
        <div class="mx-auto h-20 w-20 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-3xl mb-4">
            <i class="fa-solid fa-file-pdf"></i>
        </div>
        
        <h2 id="viewTitle" class="text-2xl font-bold text-gray-900 mb-2 truncate px-4"></h2>
        <p id="viewDescription" class="text-gray-500 text-sm mb-6 line-clamp-3 italic bg-gray-50 p-4 rounded-xl"></p>
        
        <div class="grid grid-cols-2 gap-4 text-left border-t border-gray-100 pt-6">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Taille</p>
                <p id="viewSize" class="text-sm font-semibold text-gray-700"></p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Visibilité</p>
                <p id="viewVisibility" class="text-sm font-semibold text-gray-700"></p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Posté par</p>
                <p id="viewAdmin" class="text-sm font-semibold text-gray-700 truncate"></p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date</p>
                <p id="viewDate" class="text-sm font-semibold text-gray-700"></p>
            </div>
        </div>
        
        <div class="mt-8">
            <a id="viewDownloadLink" href="#" class="w-full flex items-center justify-center py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition-all shadow-lg transform hover:-translate-y-0.5">
                <i class="fa-solid fa-download mr-2"></i>
                Télécharger maintenant
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const documentModal = document.getElementById('documentModal');
    const viewModal = document.getElementById('viewModal');
    const documentForm = document.getElementById('documentForm');
    const modalTitle = document.getElementById('modalTitle');
    const docFile = document.getElementById('docFile');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const fileRequired = document.getElementById('fileRequired');

    // Display filename on selection
    docFile.onchange = e => {
        const file = e.target.files[0];
        if (file) fileNameDisplay.innerText = file.name;
    };

    // Close Modals
    document.querySelectorAll('.closeModal').forEach(btn => {
        btn.onclick = () => documentModal.classList.add('hidden');
    });
    document.querySelector('.closeViewModal').onclick = () => viewModal.classList.add('hidden');

    // Add Modal
    const openAdd = document.getElementById('openModalAdd');
    if(openAdd) {
        openAdd.onclick = () => {
            modalTitle.innerText = "Ajouter un document";
            documentForm.action = "{{ route('documents.store') }}";
            documentForm.reset();
            fileNameDisplay.innerText = "Cliquez ou glissez un fichier (Max 50Mo)";
            fileRequired.classList.remove('hidden');
            docFile.required = true;
            documentModal.classList.remove('hidden');
        };
    }

    // View Modal
    document.querySelectorAll('.openModalView').forEach(btn => {
        btn.onclick = async () => {
            const id = btn.getAttribute('data-id') || btn.parentNode.querySelector('.id').innerText;
            try {
                const res = await fetch(`/documents/getDocument/${id}`);
                const data = await res.json();
                
                document.getElementById('viewTitle').innerText = data.title;
                document.getElementById('viewDescription').innerText = data.description || 'Aucune description fournie.';
                document.getElementById('viewSize').innerText = formatSize(data.file_size);
                document.getElementById('viewVisibility').innerText = data.visibility === 'all' ? 'Tous' : (data.visibility === 'teachers' ? 'Profs' : 'Étudiants');
                document.getElementById('viewAdmin').innerText = data.user.first_name;
                document.getElementById('viewDate').innerText = new Date(data.created_at).toLocaleDateString();
                document.getElementById('viewDownloadLink').href = `/documents/download/${id}`;
                
                viewModal.classList.remove('hidden');
            } catch (e) { console.error(e); }
        };
    });

    // Edit Modal
    document.querySelectorAll('.openModalEdit').forEach(btn => {
        btn.onclick = async () => {
            const id = btn.getAttribute('data-id') || btn.parentNode.querySelector('.id').innerText;
            try {
                const res = await fetch(`/documents/getDocument/${id}`);
                const data = await res.json();
                
                modalTitle.innerText = "Modifier le document";
                documentForm.action = "{{ route('documents.edit') }}";
                document.getElementById('docId').value = data.id;
                document.getElementById('docTitle').value = data.title;
                document.getElementById('docVisibility').value = data.visibility;
                document.getElementById('docDescription').value = data.description || '';
                fileNameDisplay.innerText = `Actuel: ${data.filename}`;
                fileRequired.classList.add('hidden');
                docFile.required = false;
                
                documentModal.classList.remove('hidden');
            } catch (e) { console.error(e); }
        };
    });

    function formatSize(bytes) {
        if (bytes === 0) return '0 Octet';
        const k = 1024;
        const sizes = ['Octets', 'Ko', 'Mo', 'Go'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
</script>
@endsection
