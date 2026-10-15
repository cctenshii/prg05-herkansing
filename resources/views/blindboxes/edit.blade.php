<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-8">

        <h1 class="text-3xl font-bold mb-4">Edit Blindbox</h1>
        <a href="{{ route('blindboxes.index') }}" class="text-blue-500 hover:underline mb-6 inline-block">
            &larr; Go Back
        </a>

        <form method="POST" action="{{ route('blindboxes.update', $blindbox->id) }}" enctype="multipart/form-data"
              class="bg-white shadow-md rounded-lg p-6 space-y-4">

            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label class="block font-semibold mb-1" for="name">Name:</label>
                <input type="text" name="name" id="name" value="{{ old('name', $blindbox->name) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            {{-- Serie --}}
            <div>
                <label class="block font-semibold mb-1" for="serie_id">Serie:</label>
                <select name="serie_id" id="serie_id"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Select a Serie --</option>
                    @foreach($series as $serie)
                        <option
                            value="{{ $serie->id }}" {{ old('serie_id', $blindbox->serie_id) == $serie->id ? 'selected' : '' }}>
                            {{ $serie->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Summary --}}
            <div>
                <label class="block font-semibold mb-1" for="summary">Summary:</label>
                <textarea name="summary" id="summary" rows="4"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('summary', $blindbox->summary) }}</textarea>
            </div>

            {{-- Image --}}
            <div>
                <label class="block font-semibold mb-1" for="image">Blindbox Image:</label>

                @if($blindbox->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $blindbox->image) }}"
                             alt="{{ $blindbox->name }}"
                             class="w-40 h-auto rounded shadow">
                    </div>
                @endif

                <input type="file" name="image" id="image" accept="image/*" class="w-full text-gray-600">
            </div>

            {{-- Added By --}}
            <div>
                <label class="block font-semibold mb-1" for="created_by">Added By:</label>
                <select name="created_by" id="created_by"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option
                            value="{{ $user->id }}" {{ old('created_by', $blindbox->created_by) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>


            {{-- Submit --}}
            <div>
                <button type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition">
                    Update Blindbox
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
