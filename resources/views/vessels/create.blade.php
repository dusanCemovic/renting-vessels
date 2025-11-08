<x-layout title="Add Vessel">
    <h1 class="text-2xl font-semibold">Add Vessel</h1>

    @if ($errors->any())
        <div class="mt-4 rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('vessels.store') }}" class="mt-6 max-w-2xl space-y-4">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                <input type="number" min="1" name="size" value="{{ old('size', 1) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            </div>
        </div>
        <div class="sm:col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <input name="type" value="{{ old('type') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Equipment</label>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3">
                @foreach($allEquipment as $eq)
                    <label class="inline-flex items-center gap-2 rounded border bg-white p-2">
                        <input type="checkbox" name="equipment[]" value="{{ $eq->id }}" {{ in_array($eq->id, old('equipment', [])) ? 'checked' : '' }} />
                        <span><span class="font-mono text-xs text-gray-500">{{ $eq->code }}</span> — {{ $eq->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('vessels.index') }}" class="rounded border px-4 py-2">Cancel</a>
            <button type="submit" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Create</button>
        </div>
    </form>
</x-layout>
