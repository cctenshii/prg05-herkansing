<x-app-layout>
    <div class="pl-4 pr-4 flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Pop Mart blindboxes list</h1>
        @auth
            @if(optional(Auth::user()->role)->name === 'Admin' || optional(Auth::user()->role)->name === 'User')
                <a href="{{ route('blindboxes.create') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-400">
                    Add Blindbox
                </a>
            @endif
        @endauth
    </div>

    @if(session('status'))
        <div class="mb-4 mx-4 p-2 bg-green-200 rounded">{{ session('status') }}</div>
    @endif

    @if(session('error'))
        <div class="mb-4 mx-4 p-2 bg-red-200 rounded text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0">
        <form method="GET" action="{{ route('blindboxes.index') }}"
              class="flex flex-col md:flex-row md:items-center md:space-x-2 space-y-2 md:space-y-0 w-full px-4">

            {{-- Search --}}
            <input type="text" name="search" placeholder="Search Blindboxes..." value="{{ request('search') }}"
                   class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 flex-1">

            {{-- Series Filter --}}
            <select name="serie_id"
                    class="border border-gray-300 rounded pr-10 pl-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">All Series</option>
                @foreach($series as $serie)
                    <option value="{{ $serie->id }}" {{ request('serie_id') == $serie->id ? 'selected' : '' }}>
                        {{ $serie->name }}
                    </option>
                @endforeach
            </select>

            {{-- Submit --}}
            <button type="submit"
                    class="bg-gray-600 text-white px-8 py-2 rounded hover:bg-gray-400 transition">
                Filter
            </button>
        </form>
    </div>

    {{-- Not Found Message --}}
    @if($blindboxes->isEmpty())
        <div class="text-center text-gray-500 mt-6">
            No blindboxes found matching your search or filters.
        </div>
    @endif



    {{-- Grid container for 2-3 cards per row --}}
    <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($blindboxes as $blindbox)
            <div class="bg-white rounded-lg shadow p-4 flex flex-col md:flex-row items-start md:items-center h-full">

                {{-- Image on the left --}}
                <div class="w-full md:w-1/3 flex-shrink-0 mb-4 md:mb-0">
                    @if ($blindbox->image_url)
                        <img
                            src="{{ $blindbox->image_url }}"
                            alt="{{ $blindbox->name }}"
                            class="w-full aspect-[1.41] object-cover rounded"
                        >
                    @else
                        <div class="w-full aspect-[1.41] bg-gray-200 flex items-center justify-center rounded">
                            <span class="text-gray-500">No image</span>
                        </div>
                    @endif
                </div>

                {{-- Text and buttons on the right --}}
                <div class="flex-1 md:pl-6 flex flex-col justify-between h-full">
                    <div>
                        <h2 class="text-xl font-semibold mb-2">{{ $blindbox->name }}</h2>
                        <p class="text-gray-600 mb-2">Series: {{ $blindbox->serie->name ?? 'Unknown' }}</p>

                        @if($blindbox->creator)
                            <p class="text-gray-500 text-sm">Added by: {{ $blindbox->creator->name }}</p>
                        @endif

                    </div>

                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('blindboxes.show', $blindbox) }}"
                           class="bg-blue-400 text-white px-3 py-1 rounded hover:bg-blue-300">
                            View
                        </a>

                        @auth
                            <form method="POST" action="{{ route('blindboxes.favorite', $blindbox) }}">
                                @csrf
                                <button type="submit"
                                        class="{{ auth()->user()->favoriteBlindboxes()->whereKey($blindbox->id)->exists() ? 'bg-yellow-500 hover:bg-yellow-400' : 'bg-gray-500 hover:bg-gray-400' }} text-white px-3 py-1 rounded">
                                    {{ auth()->user()->favoriteBlindboxes()->whereKey($blindbox->id)->exists() ? '★ Favorited' : '☆ Favorite' }}
                                </button>
                            </form>
                        @endauth
                    </div>

                </div>

            </div>
        @endforeach
    </div>
</x-app-layout>
