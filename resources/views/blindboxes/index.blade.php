<x-app-layout>
    <div class="pl-4 pr-4 flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Pop Mart blindboxes list</h1>
        @auth
            {{-- Show "Add" only for logged in users (Admin or User) --}}
            @if(Auth::user()->role->name === 'Admin' || Auth::user()->role->name === 'User')
                @if(Auth::user()->login_count >= 3)
                    <a href="{{ route('blindboxes.create') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-400">
                        Add Blindbox
                    </a>
                @else
                    <button disabled
                            class="bg-gray-300 text-gray-600 px-4 py-2 rounded cursor-not-allowed"
                            title="You need to log in at least 3 times to add a Blindbox">
                        Add Blindbox
                    </button>
                @endif
            @endif
        @endauth

    </div>

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0">
        <form method="GET" action="{{ route('blindboxes.index') }}"
              class="flex flex-col md:flex-row md:items-center md:space-x-2 space-y-2 md:space-y-0 w-full px-4">

            {{-- Search --}}
            <input type="text" name="search" placeholder="Search Blindboxes..." value="{{ request('search') }}"
                   class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 flex-1">

            {{-- Series Filter --}}
            <select name="series_id"
                    class="border border-gray-300 rounded pr-10 pl-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">All Series</option>
                @foreach($series as $serie)
                    <option value="{{ $serie->id }}" {{ request('series_id') == $serie->id ? 'selected' : '' }}>
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
                    @if ($blindbox->image)
                        <img
                            src="{{ asset('storage/' . $blindbox->image) }}"
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
                        <p class="text-gray-600 mb-2">Series: {{ $blindbox->series->name ?? 'Unknown' }}</p>

                        @if($blindbox->creator)
                            <p class="text-gray-500 text-sm">Added by: {{ $blindbox->creator->name }}</p>
                        @endif

                    </div>

                    <div class="flex space-x-2 mt-4">
                        {{-- View button: visible to everyone --}}
                        <a href="{{ route('blindboxes.show', $blindbox) }}"
                           class="bg-blue-400 text-white px-3 py-1 rounded hover:bg-blue-300">
                            View
                        </a>
                    </div>

                </div>

            </div>
        @endforeach
    </div>
</x-app-layout>
