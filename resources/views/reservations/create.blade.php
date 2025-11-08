<x-layout title="Reserve a Vessel">
    <h1 class="text-2xl font-semibold">Reserve a Vessel</h1>

    @php
        // Suggest a rounded start time (next quarter hour) and 2h duration
        $now = now();
        $roundedMinute = (int) (ceil($now->minute / 15) * 15);
        if ($roundedMinute === 60) { $now = $now->addHour(); $roundedMinute = 0; }
        $suggestedStart = $now->copy()->minute($roundedMinute)->second(0)->format('Y-m-d\\TH:i');
        $suggestedEnd = \Carbon\Carbon::parse($suggestedStart)->addHours(2)->format('Y-m-d\\TH:i');
        $minDateTime = now()->format('Y-m-d\\TH:i');
    @endphp

    <form action="{{ route('reservations.store') }}" method="POST" class="mt-6 space-y-6 max-w-2xl">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input id="title" type="text" name="title" autocomplete="off" placeholder="e.g. Sunset cruise"
                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   value="{{ old('title') }}" required>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="start_at" class="block text-sm font-medium text-gray-700 mb-1">Start</label>
                <input id="start_at" type="datetime-local" name="start_at" step="900"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       value="{{ old('start_at', $suggestedStart) }}" required>
                <p class="mt-1 text-xs text-gray-500">Pick a start date and time (15 min steps).</p>
            </div>
            <div>
                <label for="end_at" class="block text-sm font-medium text-gray-700 mb-1">End</label>
                <input id="end_at" type="datetime-local" name="end_at" step="900"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       value="{{ old('end_at', $suggestedEnd) }}" required>
                <p class="mt-1 text-xs text-gray-500">End time must be after start time.</p>
            </div>
        </div>

        <div>
            <label for="required_equipment" class="block text-sm font-medium text-gray-700 mb-1">Required
                Equipment</label>
            <select id="required_equipment" name="required_equipment[]" multiple size="6"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach($equipments as $equip)
                    <option value="{{ $equip->code }}">{{ $equip->name }} ({{ $equip->code }})</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple items.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" id="fill-now" class="rounded border px-3 py-2 text-sm">Start now + 2h</button>
            <button class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    type="submit">Reserve
            </button>
        </div>
    </form>

    <script>
        (function () {
            const btn = document.getElementById('fill-now');
            if (!btn) return;
            btn.addEventListener('click', function () {
                const start = document.getElementById('start_at');
                const end = document.getElementById('end_at');
                if (!start || !end) return;
                const now = new Date();
                const minutes = now.getMinutes();
                const rounded = Math.ceil(minutes / 15) * 15;
                if (rounded === 60) {
                    now.setHours(now.getHours() + 1);
                    now.setMinutes(0);
                } else {
                    now.setMinutes(rounded);
                }
                now.setSeconds(0);
                const pad = (n) => String(n).padStart(2, '0');
                const toLocal = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                const startVal = toLocal(now);
                const endVal = toLocal(new Date(now.getTime() + 2 * 60 * 60 * 1000));
                start.value = startVal;
                end.value = endVal;
            });
        })();
    </script>
</x-layout>
