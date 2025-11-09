<x-layout title="Reserve a Vessel">
    <h1 class="text-2xl font-semibold">Reserve a Vessel</h1>

    @php
        // Suggest a rounded start time (next quarter hour) and 2h duration in Slovenia timezone
        $now = now('Europe/Ljubljana');
        $roundedMinute = (int) (ceil($now->minute / 15) * 15);
        if ($roundedMinute === 60) { $now = $now->addHour(); $roundedMinute = 0; }
        $suggestedStart = $now->copy()->minute($roundedMinute)->second(0)->format('Y-m-d\\TH:i');
        $suggestedEnd = \Carbon\Carbon::parse($suggestedStart, 'Europe/Ljubljana')->addHours(2)->format('Y-m-d\\TH:i');
        $minDateTime = now('Europe/Ljubljana')->format('Y-m-d\\TH:i');
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
                <input id="start_at" type="text" name="start_at"
                       class="datetime-sl mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       value="{{ old('start_at', $suggestedStart) }}" required>
                <p class="mt-1 text-xs text-gray-500">Pick a start date and time (15 min steps).</p>
            </div>
            <div>
                <label for="end_at" class="block text-sm font-medium text-gray-700 mb-1">End</label>
                <input id="end_at" type="text" name="end_at"
                       class="datetime-sl mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
            <button class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    type="submit">Reserve
            </button>
        </div>
    </form>

    <!-- Flatpickr: lightweight datetime picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>

    <script>
        (function () {
            // Helpers to format using Europe/Ljubljana regardless of browser timezone
            function pad(n) { return String(n).padStart(2, '0'); }
            function partsInSlovenia(date) {
                const parts = new Intl.DateTimeFormat('en-GB', {
                    timeZone: 'Europe/Ljubljana',
                    year: 'numeric', month: 'long', day: 'numeric',
                    hour: '2-digit', minute: '2-digit', hour12: false
                }).formatToParts(date);
                const map = Object.fromEntries(parts.filter(p => p.type !== 'literal').map(p => [p.type, p.value]));
                // Need numeric month index too for ISO, get via another formatter with numeric month
                const partsNum = new Intl.DateTimeFormat('en-GB', {
                    timeZone: 'Europe/Ljubljana',
                    year: 'numeric', month: '2-digit', day: '2-digit',
                    hour: '2-digit', minute: '2-digit', hour12: false
                }).formatToParts(date);
                const mapNum = Object.fromEntries(partsNum.filter(p => p.type !== 'literal').map(p => [p.type, p.value]));
                return { day: map.day, monthLong: map.month, year: map.year, hour: map.hour, minute: map.minute,
                         monthNum: mapNum.month, dayNum: mapNum.day };
            }
            function toSloveniaISO(date) {
                const p = partsInSlovenia(date);
                return `${p.year}-${p.monthNum}-${p.dayNum}T${p.hour}:${p.minute}`;
            }
            function toSloveniaHuman(date) {
                const p = partsInSlovenia(date);
                // Format: D Month YYYY, HH:mm (e.g., 8 November 2025, 15:28)
                return `${parseInt(p.day, 10)} ${p.monthLong} ${p.year}, ${p.hour}:${p.minute}`;
            }

            // Initialize Flatpickr on both inputs
            const startInput = document.getElementById('start_at');
            const endInput = document.getElementById('end_at');
            if (!startInput || !endInput) return;

            function enhance(input) {
                return flatpickr(input, {
                    enableTime: true,
                    time_24hr: true,
                    minuteIncrement: 15,
                    // We will manage display ourselves, but altInput gives a nice UX input
                    altInput: true,
                    altFormat: 'j F Y, H:i', // display only; we'll override to ensure Slovenia TZ
                    dateFormat: 'Y-m-d\\TH:i', // value kept/submitted as ISO without TZ
                    minDate: '{{ $minDateTime }}',
                    defaultDate: input.value || null,
                    onReady: function (selectedDates, dateStr, instance) {
                        if (dateStr) {
                            // Ensure altInput matches Slovenia timezone display
                            const d = selectedDates[0] || new Date();
                            instance.input.value = dateStr; // keep ISO as-is
                            instance.altInput.value = toSloveniaHuman(d);
                        }
                    },
                    onChange: function (selectedDates, _dateStr, instance) {
                        const d = selectedDates[0];
                        if (!d) return;
                        // Compute Slovenia wall-time ISO string and human string
                        const iso = toSloveniaISO(d);
                        const human = toSloveniaHuman(d);
                        instance.input.value = iso;      // submit this
                        instance.altInput.value = human;  // show this
                    }
                });
            }

            const fpStart = enhance(startInput);
            const fpEnd = enhance(endInput);
        })();
    </script>
</x-layout>
