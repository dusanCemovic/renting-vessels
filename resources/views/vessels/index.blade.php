<x-layout title="All Vessels">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">All Vessels</h1>
        <a href="{{ route('vessels.create') }}" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Add Vessel</a>
    </div>

    @if(session('status'))
        <div class="mt-4 rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="mt-4 rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3">{{ session('error') }}</div>
    @endif

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort by</label>
            <select name="sort" class="mt-1 block w-48 rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="name" {{ ($sort ?? 'name') === 'name' ? 'selected' : '' }}>Name</option>
                <option value="type" {{ ($sort ?? 'name') === 'type' ? 'selected' : '' }}>Type</option>
                <option value="size" {{ ($sort ?? 'name') === 'size' ? 'selected' : '' }}>Size</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
            <select name="dir" class="mt-1 block w-32 rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="asc" {{ ($dir ?? 'asc') === 'asc' ? 'selected' : '' }}>Asc</option>
                <option value="desc" {{ ($dir ?? 'asc') === 'desc' ? 'selected' : '' }}>Desc</option>
            </select>
        </div>
        <div>
            <button class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" type="submit">Apply</button>
        </div>
    </form>

    @if($vessels->count() === 0)
        <div class="mt-4 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 px-4 py-3">No vessels found.</div>
    @else
        <div class="mt-4 overflow-x-auto rounded border border-gray-200 bg-white">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Size</th>
                    <th class="px-4 py-2">Equipment</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($vessels as $index => $vessel)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ ($vessels->currentPage()-1)*$vessels->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-2">
                            <a class="text-blue-600 hover:underline" href="{{ route('vessels.show', $vessel) }}">{{ $vessel->name }}</a>
                        </td>
                        <td class="px-4 py-2">{{ $vessel->type }}</td>
                        <td class="px-4 py-2">{{ $vessel->size }}</td>
                        <td class="px-4 py-2">
                            @if($vessel->equipment->isEmpty())
                                <span class="text-gray-400">None</span>
                            @else
                                {{ $vessel->equipment->pluck('name')->join(', ') }}
                            @endif
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('vessels.edit', $vessel) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('vessels.destroy', $vessel) }}" onsubmit="return confirm('Delete vessel {{ $vessel->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $vessels->links() }}</div>
    @endif
</x-layout>
