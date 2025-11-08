<x-layout title="Add Equipment">
    <h1 class="text-2xl font-semibold">Add Equipment</h1>

    @if ($errors->any())
        <div class="mt-4 rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('equipments.store') }}" class="mt-6 max-w-xl space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Code</label>
            <input name="code" value="{{ old('code') }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Description (optional)</label>
            <textarea name="description" rows="3" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('equipments.index') }}" class="rounded border px-4 py-2">Cancel</a>
            <button type="submit" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Create</button>
        </div>
    </form>
</x-layout>
