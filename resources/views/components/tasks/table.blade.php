@props([
    'tasks' => collect(),
    'filters' => ['type' => 'both', 'sort' => 'start', 'dir' => 'desc'],
    'baseRoute' => request()->route() ? request()->route()->getName() : 'home',
    'showTypeFilter' => false,
])

<form method="GET" class="mt-4 flex flex-wrap items-end gap-4">
    @if($showTypeFilter)
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Show</label>
            <select name="type"
                    class="mt-1 block w-48 rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="both" {{ ($filters['type'] ?? 'both') === 'both' ? 'selected' : '' }}>Both</option>
                <option value="reservations" {{ ($filters['type'] ?? 'both') === 'reservations' ? 'selected' : '' }}>
                    Reservations
                </option>
                <option value="maintenance" {{ ($filters['type'] ?? 'both') === 'maintenance' ? 'selected' : '' }}>
                    Maintenance
                </option>
            </select>
        </div>
    @endif
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sort by</label>
        <select name="sort"
                class="mt-1 block w-48 rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="start" {{ ($filters['sort'] ?? 'start') === 'start' ? 'selected' : '' }}>Start time</option>
            <option value="end" {{ ($filters['sort'] ?? 'start') === 'end' ? 'selected' : '' }}>End time</option>
            <option value="vessel" {{ ($filters['sort'] ?? 'start') === 'vessel' ? 'selected' : '' }}>Vessel</option>
            @if($showTypeFilter)
                <option value="type" {{ ($filters['sort'] ?? 'start') === 'type' ? 'selected' : '' }}>Type</option>
            @endif
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
        <select name="dir"
                class="mt-1 block w-32 rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="asc" {{ ($filters['dir'] ?? 'desc') === 'asc' ? 'selected' : '' }}>Asc</option>
            <option value="desc" {{ ($filters['dir'] ?? 'desc') === 'desc' ? 'selected' : '' }}>Desc</option>
        </select>
    </div>
    <div class="">
        <button class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                type="submit">Apply
        </button>
    </div>
</form>

@if(($tasks ?? collect())->isEmpty())
    <div class="mt-4 rounded border border-yellow-300 bg-yellow-50 text-yellow-800 px-4 py-3">No tasks found.</div>
@else
    <div class="mt-6 overflow-x-auto rounded border border-gray-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Title</th>
                <th class="px-4 py-2">
                    @php
                        $q = request()->query();
                        $nextDir = (($filters['sort'] ?? 'start') === 'vessel' && ($filters['dir'] ?? 'desc') === 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a class="hover:underline"
                       href="{{ route($baseRoute, array_merge($q, ['sort' => 'vessel', 'dir' => $nextDir])) }}">Vessel</a>
                </th>
                <th class="px-4 py-2">
                    @php
                        $q = request()->query();
                        $nextDirStart = (($filters['sort'] ?? 'start') === 'start' && ($filters['dir'] ?? 'desc') === 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a class="hover:underline"
                       href="{{ route($baseRoute, array_merge($q, ['sort' => 'start', 'dir' => $nextDirStart])) }}">Start</a>
                </th>
                <th class="px-4 py-2">
                    @php
                        $q = request()->query();
                        $nextDirEnd = (($filters['sort'] ?? 'start') === 'end' && ($filters['dir'] ?? 'desc') === 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a class="hover:underline"
                       href="{{ route($baseRoute, array_merge($q, ['sort' => 'end', 'dir' => $nextDirEnd])) }}">End</a>
                </th>
                @if($showTypeFilter)
                    <th class="px-4 py-2">Type</th>
                @endif
                <th class="px-4 py-2">Status</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @foreach($tasks as $i => $task)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $i + 1 }}</td>
                    <td class="px-4 py-2">{{ $task->title }}</td>
                    <td class="px-4 py-2"><a class="text-blue-500" href="{{ url('vessels/' . $task->vessel_id )}}">{{ $task->vessel_name ?? '—' }}</a></td>
                    <td class="px-4 py-2">@slDate($task->start_at)</td>
                    <td class="px-4 py-2">@slDate($task->end_at)</td>
                    @if($showTypeFilter)
                        <td class="px-4 py-2">
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ ($task->type ?? '') === 'Reservation' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">{{ $task->type }}</span>
                        </td>
                    @endif
                    <td class="px-4 py-2">
                        <span
                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($task->status ?? 'scheduled') }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
