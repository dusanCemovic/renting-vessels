<x-layout title="Edit Equipment">
    <h1 class="text-2xl font-semibold">Edit Equipment: {{ $equipment->code }}</h1>

    @if ($errors->any())
        <div class="mt-4 rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('equipments.update', $equipment) }}" class="mt-6 max-w-xl space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700">Code</label>
            <input value="{{ $equipment->code }}" class="mt-1 block w-full rounded border-gray-300 bg-gray-100 text-gray-600 shadow-sm" disabled />
            <p class="text-xs text-gray-500">Code is immutable.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input name="name" value="{{ old('name', $equipment->name) }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Description (optional)</label>
            <textarea name="description" rows="3" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $equipment->description) }}</textarea>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('equipments.index') }}" class="rounded border px-4 py-2">Cancel</a>
            <button type="submit" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
        </div>
    </form>
</x-layout>
