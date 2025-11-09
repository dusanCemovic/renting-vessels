<x-layout title="{{ $vessel->name }} - Tasks">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Tasks for Vessel: <span class="font-bold">{{ $vessel->name }}</span></h1>
        <div class="flex gap-2">
            <a href="{{ route('maintenances.create', $vessel) }}" class="inline-flex items-center rounded bg-amber-600 px-4 py-2 text-white hover:bg-amber-700">Add Maintenance</a>
            <a href="{{ route('vessels.edit', $vessel) }}" class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Edit Vessel</a>
        </div>
    </div>

    @if(session('status'))
        <div class="mt-4 rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3">{{ session('status') }}</div>
    @endif

    <h2 class="mt-4 text-xl font-semibold">Reservations</h2>
    @if($vessel->reservations->isEmpty())
        <div class="mt-2 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 px-4 py-3">No reservations found for this vessel.</div>
    @else
        <div class="mt-2 overflow-x-auto rounded border border-gray-200 bg-white">
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
                        <td class="px-4 py-2">@slDate($reservation->start_at)</td>
                        <td class="px-4 py-2">@slDate($reservation->end_at)</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h2 class="mt-8 text-xl font-semibold">Maintenances</h2>
    @if($vessel->maintenances->isEmpty())
        <div class="mt-2 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 px-4 py-3">No maintenances found for this vessel.</div>
    @else
        <div class="mt-2 overflow-x-auto rounded border border-gray-200 bg-white">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Notes</th>
                    <th class="px-4 py-2">Start At</th>
                    <th class="px-4 py-2">End At</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($vessel->maintenances as $i => $mnt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $i + 1 }}</td>
                        <td class="px-4 py-2">{{ $mnt->title }}</td>
                        <td class="px-4 py-2">{{ $mnt->notes ?? '—' }}</td>
                        <td class="px-4 py-2">@slDate($mnt->start_at)</td>
                        <td class="px-4 py-2">@slDate($mnt->end_at)</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layout>
