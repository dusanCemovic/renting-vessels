<x-layout title="Reservations & Maintenance">
    <h1 class="text-2xl font-semibold">Reservations & Maintenance</h1>

    @if($tasks->isEmpty())
        <div class="mt-4 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 px-4 py-3">No reservations or maintenance tasks yet.</div>
    @else
        <div class="mt-6 overflow-x-auto rounded border border-gray-200 bg-white">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Vessel</th>
                    <th class="px-4 py-2">Start</th>
                    <th class="px-4 py-2">End</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($tasks as $i => $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $i + 1 }}</td>
                        <td class="px-4 py-2">{{ $task->title }}</td>
                        <td class="px-4 py-2">{{ optional($task->vessel)->name ?? '—' }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($task->start_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($task->end_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">
                            @php
                                $type = empty($task->required_equipment) ? 'Maintenance' : 'Reservation';
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $type === 'Reservation' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">{{ $type }}</span>
                        </td>
                        <td class="px-4 py-2">
                            @php $status = $task->status ?? 'scheduled'; @endphp
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($status) }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layout>
