@extends('Layouts.authed')

@section('title', 'Forums')

@section('content')
<div class="section-animate space-y-8 p-4 md:p-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Forums de Discussion</h1>
            <p class="mt-1 text-gray-500">Choisissez votre filière pour rejoindre les échanges collaboratifs.</p>
        </div>
    </div>

    <!-- Search Area -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('forums.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            @csrf
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                </div>
                <input id="search" name="search" type="text" value="{{ request('search') }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    placeholder="Rechercher une filière ou un niveau...">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-8 py-2.5 bg-gray-900 text-white font-medium rounded-xl hover:bg-gray-800 transition-all">
                    Filtrer
                </button>
                @if(request('search') || $loup)
                    <a href="{{ route('forums.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 font-medium rounded-xl hover:bg-gray-200 transition-all text-center">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Levels Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($levels as $level)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-comments text-xl"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">{{ $level->sector->name }}</span>
                        <h3 class="text-xl font-bold text-gray-900 leading-tight">
                            {{ $level->name }}
                        </h3>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                    <a href="{{ route('forums.forum', ['level_id' => $level->id]) }}" 
                       class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm">
                        Ouvrir le forum
                        <i class="fa-solid fa-chevron-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <div class="text-gray-400 mb-2">
                    <i class="fa-solid fa-folder-open text-4xl"></i>
                </div>
                <p class="text-gray-500 font-medium">Aucun forum disponible pour ces critères.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($levels->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $levels->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Keeping this section empty for potential future needs, but utilizing Tailwind and standard links for logic.
</script>
@endsection