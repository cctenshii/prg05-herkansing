<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- Back link --}}
        <a href="{{ route('blindboxes.index') }}"
           class="text-blue-500 hover:underline mb-6 inline-block">
            &larr; Go Back
        </a>

        {{-- Main content card --}}
        <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col md:flex-row gap-6 p-6">

            {{-- Image --}}
            <div class="flex-shrink-0">
                @if ($blindbox->image && file_exists(public_path('storage/' . $blindbox->image)))
                    <img src="{{ asset('storage/' . $blindbox->image) }}"
                         alt="{{ $blindbox->name }}"
                         class="w-64 h-auto md:h-96 object-cover rounded shadow">
                @else
                    <img src="{{ asset('images/placeholder.jpg') }}"
                         alt="No image available"
                         class="w-64 h-auto md:h-96 object-cover rounded opacity-70 shadow">
                @endif
            </div>

            {{-- Text content --}}
            <div class="flex-1 flex flex-col justify-between">
                <h1 class="text-3xl font-bold mb-4">{{ $blindbox->name }}</h1>

                <div class="space-y-2 mb-4">
                    <p class="text-lg"><span
                            class="font-semibold">Serie:</span> {{ $blindbox->serie->name ?? 'Unknown' }}</p>

                    @if($blindbox->created_by)
                        <p class="text-lg text-gray-600"><span
                                class="font-semibold">Added by:</span> {{ $blindbox->created_by->name }}</p>
                    @endif
                </div>


                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $blindbox->summary }}</p>
            </div>

        </div>
    </div>
</x-app-layout>
