<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-8">

        <h1 class="text-3xl font-bold mb-4">Add a New Blindbox</h1>
        <a href="{{ route('blindboxes.index') }}" class="text-blue-500 hover:underline mb-6 inline-block">
            &larr; Go Back
        </a>

        <form method="POST" action="{{ route('blindboxes.store') }}" enctype="multipart/form-data"
              class="bg-white shadow-md rounded-lg p-6 space-y-4">

            @csrf

            {{-- Name --}}
            <div>
                <label class="block font-semibold mb-1" for="name">Name:</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                @error('name')
                <p style="color:red;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Serie --}}
            <div>
                <label class="block font-semibold mb-1" for="serie_id">Serie:</label>
                <select name="serie_id" id="serie_id"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Select a Serie --</option>
                    @foreach($series as $serie)
                        <option value="{{ $serie->id }}" {{ old('serie_id') == $serie->id ? 'selected' : '' }}>
                            {{ $serie->name }}
                        </option>
                    @endforeach
                </select>
                @error('serie_id')
                <p style="color:red;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Summary --}}
            <div>
                <label class="block font-semibold mb-1" for="summary">Summary:</label>
                <textarea name="summary" id="summary" rows="4"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('summary') }}</textarea>
                @error('summary')
                <p style="color:red;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Image --}}
            <div>
                <label class="block font-semibold mb-1" for="image">Blindbox Image:</label>
                <input type="file" name="image" id="image" accept="image/*"
                       class="w-full text-gray-600">
            </div>

            {{-- Submit --}}
            <div>
                <button type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition">
                    Add Blindbox
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
