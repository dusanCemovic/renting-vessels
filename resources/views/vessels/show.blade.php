<x-layout title="{{ $vessel->name }} - Tasks">
    <h1 class="mb-6 text-2xl font-semibold">Tasks for Vessel: <span class="font-bold">{{ $vessel->name }}</span></h1>

    @if($vessel->reservations->isEmpty())
        <div class="rounded border border-yellow-300 bg-yellow-50 text-yellow-800 px-4 py-3">No tasks found for this
            vessel.
        </div>
    @else
        <div class="overflow-x-auto rounded border border-gray-200 bg-white">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Task Name</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Start At</th>
                    <th class="px-4 py-2">End At</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($vessel->reservations as $index => $reservation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $reservation->title }}</td>
                        <td class="px-4 py-2">{{ $reservation->description ?? '—' }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($reservation->start_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($reservation->end_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layout>
