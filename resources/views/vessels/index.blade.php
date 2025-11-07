<x-layout title="All Vessels">
    <h1 class="text-2xl font-semibold">All Vessels</h1>

    @if($vessels->isEmpty())
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
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($vessels as $index => $vessel)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
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
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layout>
